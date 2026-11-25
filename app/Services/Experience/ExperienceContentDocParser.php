<?php

namespace App\Services\Experience;

use App\Services\Docx\DocxParagraphReader;

/**
 * Reads an experience content document (.docx) written by the content team:
 *
 *   Meta description: <text>          (or the text in the next paragraph)
 *   <Bold heading>                    page heading (H1)
 *   intro paragraph(s)
 *   <Bold heading> + paragraphs / bullet lists   (sections)
 *   FAQs
 *   <Bold question?> answer, or the question and the answer as separate paragraphs
 *
 * "Label: value" lines (Opening hours, Location / Address, Instagram, Website)
 * are collected as practical information.
 */
class ExperienceContentDocParser
{
    private const LABELS = [
        'openHours' => ['opening hours', 'hours', 'open', 'opening'],
        'address' => ['address', 'location'],
        'instagram' => ['instagram'],
        'website' => ['website'],
        'whatsapp' => ['whatsapp', 'whatsapp reservation', 'order via whatsapp', 'bookings', 'contact'],
        'email' => ['email'],
    ];

    public function __construct(private DocxParagraphReader $reader)
    {
    }

    public function parse(string $path): array
    {
        $paragraphs = $this->reader->read($path);

        $doc = [
            'file' => $path,
            'heading' => null,
            'metaDesc' => null,
            'intro' => [],       // paragraphs before the first section
            'sections' => [],    // [['heading' => ..., 'blocks' => [paragraph, ...]]]
            'faqs' => [],        // [['question' => ..., 'answer' => ..., 'answerHtml' => ...]]
            'practical' => [],   // openHours / address / instagram / website
        ];

        $waitingMeta = false;
        $inFaq = false;

        foreach ($paragraphs as $paragraph) {
            $text = $paragraph['text'];

            if (preg_match('/^meta\s*desc(ription)?\s*(\([^)]*\))?\s*:?\s*(.*)$/i', $text, $m)) {
                $doc['metaDesc'] = trim($m[3]) ?: null;
                $waitingMeta = $doc['metaDesc'] === null;
                continue;
            }

            if ($waitingMeta) {
                $doc['metaDesc'] = $text;
                $waitingMeta = false;
                continue;
            }

            $this->practical($doc['practical'], $paragraph);

            if ($inFaq) {
                $this->addFaq($doc['faqs'], $paragraph);
                continue;
            }

            if (preg_match('/^(faqs?|frequently asked questions)\s*:?$/i', $text)) {
                $inFaq = true;
                continue;
            }

            // a bold line is a heading, except a table and "Label: value" (the page heading may hold a colon)
            $isBoldLine = $paragraph['allBold'] && !$paragraph['list'] && !isset($paragraph['table']);
            $isHeading = $isBoldLine && ($doc['heading'] === null || !preg_match('/^[^:]{1,25}:\s*\S/', $text));

            if ($isHeading && $doc['heading'] === null) {
                $doc['heading'] = rtrim($text, ': ');
                continue;
            }

            if ($isHeading) {
                $doc['sections'][] = ['heading' => rtrim($text, ':'), 'blocks' => []];
                continue;
            }

            if ($doc['sections']) {
                $doc['sections'][count($doc['sections']) - 1]['blocks'][] = $paragraph;
            } else {
                $doc['intro'][] = $paragraph;
            }
        }

        $doc['faqs'] = array_values(array_filter($doc['faqs'], fn ($faq) => $faq['answer'] !== ''));

        return $doc;
    }

    private function addFaq(array &$faqs, array $paragraph): void
    {
        $last = count($faqs) - 1;

        // "<b>Question?</b> Answer" in one paragraph
        if ($paragraph['bold'] !== '' && !$paragraph['allBold']) {
            $answer = DocxParagraphReader::line($paragraph['rest']);
            $faqs[] = ['question' => $this->question($paragraph['bold']), 'answer' => $answer, 'answerHtml' => e($answer)];
            return;
        }

        // "Question?Answer" in one plain paragraph (or "Question?" + line break + answer)
        if (!$paragraph['allBold'] && preg_match('/^(.{5,250}?\?)\s*(\S.*)$/su', trim($paragraph['raw']), $m)) {
            $answer = DocxParagraphReader::line($m[2]);
            $faqs[] = ['question' => $this->question(DocxParagraphReader::line($m[1])), 'answer' => $answer, 'answerHtml' => e($answer)];
            return;
        }

        // a question on its own line (bold, or ending with "?") starts a new FAQ
        $isQuestion = $paragraph['allBold'] || str_ends_with($paragraph['text'], '?');

        if ($isQuestion && ($last < 0 || $faqs[$last]['answer'] !== '')) {
            $faqs[] = ['question' => $this->question($paragraph['text']), 'answer' => '', 'answerHtml' => ''];
            return;
        }

        if ($last < 0) {
            return;
        }

        $faqs[$last]['answer'] = trim($faqs[$last]['answer'] . ' ' . $paragraph['text']);
        $faqs[$last]['answerHtml'] .= ($faqs[$last]['answerHtml'] === '' ? '' : '<br>') . $paragraph['html'];
    }

    /** "1. Where is ...?" => "Where is ...?" */
    private function question(string $text): string
    {
        return preg_replace('/^\d+\s*[.)]\s*/', '', trim($text));
    }

    /** "Opening hours: 9am – 10pm" etc. (a paragraph can hold several lines). */
    private function practical(array &$practical, array $paragraph): void
    {
        foreach (preg_split('/\n/', $paragraph['raw']) as $line) {
            if (!preg_match('/^\s*([A-Za-z ]{2,25}?)\s*:\s*(.+)$/', $line, $m)) {
                continue;
            }

            $label = strtolower(trim($m[1]));
            $value = rtrim(DocxParagraphReader::line($m[2]), '.');

            foreach (self::LABELS as $field => $labels) {
                if (!in_array($label, $labels, true) || isset($practical[$field])) {
                    continue;
                }

                $value = match ($field) {
                    'website' => str_contains($value, '.') && !str_contains($value, ' ') ? $value : null,
                    'whatsapp' => preg_match('/\+?\d[\d\s-]{6,}\d/', $value, $phone) ? $phone[0] : null,
                    'email' => preg_match('/[\w.+-]+@[\w-]+\.[\w.]+/', $value, $email) ? $email[0] : null,
                    default => $value,
                };

                if ($value) {
                    $practical[$field] = $value;
                }
            }
        }
    }
}
