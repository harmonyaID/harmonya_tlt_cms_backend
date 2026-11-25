<?php

namespace App\Services\Property;

use App\Services\Docx\DocxParagraphReader;

/**
 * Reads a property content document (.docx) written by the content team:
 *
 *   <Title>
 *   6 bedrooms | Up to 18 guests | Pool | Breakfast
 *   Collection: a; b; c
 *   Description: paragraphs
 *   Room configuration: lines (optional)
 *   Features: "4 bedroom", "4 bathroom", "Up to 8 guests"
 *   Amenities & Services: list
 *   Why You'll Love This: <b>Title</b><br>text (or the text in the next paragraph)
 *   Meta title : ...
 *   Meta desc : ...
 */
class PropertyContentDocParser
{
    private const SECTIONS = [
        'description' => '/^description\s*:?$/i',
        'rooms' => '/^room configuration\s*:?$/i',
        'features' => '/^features\s*:?$/i',
        'amenities' => '/^amenities(\s*(&|and)\s*services)?\s*:?$/i',
        'why' => '/^why you.?ll love this\s*:?$/iu',
    ];

    public function __construct(private DocxParagraphReader $reader)
    {
    }

    public function parse(string $path): array
    {
        $blocks = $this->reader->read($path);

        $doc = [
            'file' => $path,
            'title' => $blocks[0]['text'] ?? pathinfo($path, PATHINFO_FILENAME),
            'summary' => null,
            'collection' => [],
            'description' => [],   // [['text' => ..., 'list' => bool]]
            'rooms' => [],
            'features' => [],
            'amenities' => [],
            'why' => [],
            'metaTitle' => null,
            'metaDesc' => null,
        ];

        $current = null;

        foreach (array_slice($blocks, 1) as $block) {
            $text = $block['text'];

            if (preg_match('/^meta\s*title\s*:\s*(.*)$/i', $text, $m)) {
                $doc['metaTitle'] = trim($m[1]) ?: null;
                $current = null;
                continue;
            }

            if (preg_match('/^meta\s*desc(ription)?\s*:\s*(.*)$/i', $text, $m)) {
                $doc['metaDesc'] = trim($m[2]) ?: null;
                $current = null;
                continue;
            }

            if (preg_match('/^collections?\s*:\s*(.*)$/i', $text, $m)) {
                $doc['collection'] = array_values(array_filter(array_map('trim', preg_split('/[;,]/', $m[1]))));
                continue;
            }

            // "Room Configuration: King bed" written on one line
            if (preg_match('/^room configuration\s*:\s*(.+)$/i', $text, $m)) {
                $current = 'rooms';
                $doc['rooms'][] = trim($m[1]);
                continue;
            }

            foreach (self::SECTIONS as $key => $pattern) {
                if (preg_match($pattern, $text)) {
                    $current = $key;
                    continue 2;
                }
            }

            if ($current === null && $doc['summary'] === null && str_contains($text, '|')) {
                $doc['summary'] = $text;
                continue;
            }

            match ($current) {
                'why' => $this->addWhy($doc['why'], $block),
                'description', null => $doc['description'][] = ['text' => $text, 'list' => $block['list']],
                default => $doc[$current][] = $text,
            };
        }

        return $doc;
    }

    /** "<b>Title</b><br>value", "Title<br>value", or a title paragraph followed by its text. */
    private function addWhy(array &$why, array $block): void
    {
        if ($block['bold'] !== '') {
            $why[] = ['title' => $block['bold'], 'value' => $this->line($block['rest'])];
            return;
        }

        $last = count($why) - 1;

        if ($last >= 0 && $why[$last]['value'] === '') {
            $why[$last]['value'] = $block['text'];
            return;
        }

        [$title, $value] = array_pad(explode("\n", trim($block['raw']), 2), 2, '');
        $why[] = $value !== ''
            ? ['title' => $this->line($title), 'value' => $this->line($value)]
            : ['title' => null, 'value' => $this->line($title)];
    }

    private function line(string $text): string
    {
        return DocxParagraphReader::line($text);
    }
}
