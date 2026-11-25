<?php

namespace App\Services\Docx;

use DOMDocument;
use DOMElement;
use DOMXPath;
use RuntimeException;
use ZipArchive;

/**
 * Reads the paragraphs of a .docx (no external tools needed, works on any server).
 *
 * Every non-empty paragraph, in order:
 *   text    one line of plain text
 *   raw     plain text with the line breaks (\n)
 *   html    inline HTML: <strong>, <a href>, <br>
 *   bold    the leading bold text (up to the first non-bold text or line break)
 *   rest    the raw text after that bold part
 *   allBold the whole paragraph is bold (used as a heading)
 *   list    it is a bullet / numbered list item
 *
 * A table is one item with 'table' => rows of cell HTML (text = all cells).
 */
class DocxParagraphReader
{
    private const W = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
    private const R = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';

    public function read(string $path): array
    {
        $zip = new ZipArchive();

        if ($zip->open($path) !== true) {
            throw new RuntimeException("Cannot open {$path}");
        }

        $xml = $zip->getFromName('word/document.xml');
        $rels = $zip->getFromName('word/_rels/document.xml.rels');
        $zip->close();

        if ($xml === false) {
            throw new RuntimeException("{$path} has no word/document.xml");
        }

        $links = $this->links($rels ?: '');

        $dom = new DOMDocument();
        $dom->loadXML($xml, LIBXML_NONET);
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('w', self::W);

        $paragraphs = [];

        foreach ($xpath->query('//w:body//w:p[not(ancestor::w:tbl)] | //w:body//w:tbl[not(ancestor::w:tbl)]') as $paragraph) {
            if ($paragraph->localName === 'tbl') {
                if ($table = $this->table($xpath, $paragraph, $links)) {
                    $paragraphs[] = $table;
                }
                continue;
            }

            $segments = $this->segments($xpath, $paragraph, $links);
            $raw = implode('', array_column($segments, 'text'));
            $text = self::line($raw);

            if ($text === '') {
                continue;
            }

            $bold = '';
            $restIndex = count($segments);

            foreach ($segments as $i => $segment) {
                // line breaks / spaces before any text are skipped
                if (trim($bold) === '' && trim($segment['text']) === '') {
                    continue;
                }

                if ($segment['text'] === "\n" || (!$segment['bold'] && trim($segment['text']) !== '')) {
                    $restIndex = $i;
                    break;
                }
                $bold .= $segment['text'];
            }

            $paragraphs[] = [
                'text' => $text,
                'raw' => $raw,
                'html' => $this->html($segments),
                'bold' => self::line($bold),
                'rest' => implode('', array_column(array_slice($segments, $restIndex), 'text')),
                'allBold' => $restIndex === count($segments) || self::line(implode('', array_column(array_slice($segments, $restIndex), 'text'))) === '',
                'list' => $xpath->query('./w:pPr/w:numPr', $paragraph)->length > 0,
            ];
        }

        return $paragraphs;
    }

    private function table(DOMXPath $xpath, DOMElement $table, array $links): ?array
    {
        $rows = [];

        foreach ($xpath->query('./w:tr', $table) as $tr) {
            $cells = [];

            foreach ($xpath->query('./w:tc', $tr) as $tc) {
                $lines = [];
                foreach ($xpath->query('.//w:p', $tc) as $p) {
                    $html = $this->html($this->segments($xpath, $p, $links));
                    if (trim(strip_tags($html)) !== '') {
                        $lines[] = $html;
                    }
                }
                $cells[] = implode('<br>', $lines);
            }

            if (trim(strip_tags(implode('', $cells))) !== '') {
                $rows[] = $cells;
            }
        }

        if (!$rows) {
            return null;
        }

        $text = self::line(implode(' ', array_map(fn ($row) => strip_tags(implode(' ', $row)), $rows)));

        return ['text' => $text, 'raw' => $text, 'html' => '', 'bold' => '', 'rest' => $text, 'allBold' => false, 'list' => false, 'table' => $rows];
    }

    /** One line: whitespace (and non-breaking spaces) collapsed. */
    public static function line(string $text): string
    {
        return trim(preg_replace('/\s+/u', ' ', str_replace("\u{00A0}", ' ', $text)));
    }

    /** @return array<int,array{text:string,bold:bool,href:?string}> */
    private function segments(DOMXPath $xpath, DOMElement $paragraph, array $links): array
    {
        $segments = [];

        foreach ($xpath->query('.//w:r', $paragraph) as $run) {
            $bold = $this->isBold($xpath, $run);
            $href = $this->href($run, $paragraph, $links);

            foreach ($run->childNodes as $child) {
                $text = match ($child->localName) {
                    't' => $child->textContent,
                    'tab' => ' ',
                    'br', 'cr' => "\n",
                    default => null,
                };

                if ($text !== null) {
                    $segments[] = ['text' => $text, 'bold' => $bold, 'href' => $text === "\n" ? null : $href];
                }
            }
        }

        return $segments;
    }

    private function html(array $segments): string
    {
        $html = '';
        $i = 0;
        $count = count($segments);

        while ($i < $count) {
            if ($segments[$i]['text'] === "\n") {
                $html .= '<br>';
                $i++;
                continue;
            }

            // consecutive text with the same bold + link
            $text = '';
            [$bold, $href] = [$segments[$i]['bold'], $segments[$i]['href']];

            while ($i < $count && $segments[$i]['text'] !== "\n" && $segments[$i]['bold'] === $bold && $segments[$i]['href'] === $href) {
                $text .= $segments[$i]['text'];
                $i++;
            }

            $part = e(str_replace("\u{00A0}", ' ', $text));

            if ($bold && trim($text) !== '') {
                $part = '<strong>' . $part . '</strong>';
            }

            if ($href) {
                $part = '<a href="' . e($href) . '" target="_blank" rel="noopener">' . $part . '</a>';
            }

            $html .= $part;
        }

        $html = preg_replace('#(<br>)+$#', '', trim(preg_replace('/[ \t]+/', ' ', $html)));

        // <strong>a</strong><strong>b</strong> => <strong>ab</strong>
        return str_replace('</strong><strong>', '', $html);
    }

    private function isBold(DOMXPath $xpath, DOMElement $run): bool
    {
        $b = $xpath->query('./w:rPr/w:b', $run)->item(0);

        if (!$b instanceof DOMElement) {
            return false;
        }

        return !in_array(strtolower($b->getAttributeNS(self::W, 'val')), ['0', 'false', 'off'], true);
    }

    /** Link target of the <w:hyperlink> around the run (external links only). */
    private function href(DOMElement $run, DOMElement $paragraph, array $links): ?string
    {
        for ($node = $run->parentNode; $node && $node !== $paragraph; $node = $node->parentNode) {
            if ($node instanceof DOMElement && $node->localName === 'hyperlink') {
                $url = $links[$node->getAttributeNS(self::R, 'id')] ?? null;

                return $url ? $this->cleanUrl($url) : null;
            }
        }

        return null;
    }

    /** Removes tracking parameters (utm_*) from a link. */
    private function cleanUrl(string $url): string
    {
        $parts = parse_url($url);

        if (empty($parts['query'])) {
            return $url;
        }

        parse_str($parts['query'], $query);
        $query = array_filter($query, fn ($key) => !str_starts_with(strtolower($key), 'utm_'), ARRAY_FILTER_USE_KEY);

        $base = strtok($url, '?');
        $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';

        return $base . ($query ? '?' . http_build_query($query) : '') . $fragment;
    }

    /** @return array<string,string> relationship id => external URL */
    private function links(string $rels): array
    {
        if ($rels === '') {
            return [];
        }

        $dom = new DOMDocument();
        $dom->loadXML($rels, LIBXML_NONET);
        $links = [];

        foreach ($dom->getElementsByTagName('Relationship') as $rel) {
            if (str_ends_with($rel->getAttribute('Type'), '/hyperlink')) {
                $links[$rel->getAttribute('Id')] = $rel->getAttribute('Target');
            }
        }

        return $links;
    }
}
