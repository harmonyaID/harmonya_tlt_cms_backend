<?php

namespace App\Services\WordPress;

use Illuminate\Support\Str;

/**
 * Turns WordPress page-builder content (WPBakery shortcodes) into plain HTML
 * that keeps the same layout, so the Experience description looks like the
 * old page without carrying any [vc_row ...] markup.
 *
 *   [vc_row] / [vc_column width="1/2"]   ->  flex rows and columns (div)
 *   [vc_column_text]                      ->  <div class="wp-text"> with paragraphs
 *   [qbutton text="MENU" link="..."]      ->  <a class="wp-button"> (colors kept)
 *   [vc_empty_space] / [vc_separator]     ->  spacer div / <hr>
 *   [qode_pricing_table(s)]               ->  price cards
 *   [bsf-info-box]                        ->  titled box
 *   [vc_video]                            ->  responsive embed
 *
 * Not convertible - they only hold a WordPress media / slider ID that is not in
 * the CSV, so there is nothing to show: [masterslider], [vc_single_image],
 * [vc_gallery], [envira-gallery], plus the theme's [be_search] box. Any other
 * unknown shortcode is dropped and the text inside it is kept.
 *
 * Every wrapper has a "wp-..." class, so the website can restyle them
 * (e.g. stack .wp-col on mobile) without touching the data.
 */
class WordPressContentConverter
{
    private const GAP = 24;

    private const BLOCK_TAGS = [
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'ul', 'ol', 'div', 'table', 'blockquote',
        'hr', 'pre', 'figure', 'section', 'iframe',
    ];

    /** @var array<string,string> WordPress media ID => file URL */
    private array $media = [];

    /** @param array<int|string,string> $idToUrl */
    public function setMedia(array $idToUrl): void
    {
        $this->media = array_map('strval', $idToUrl);
    }

    public function convert(string $content, string $title = ''): ?string
    {
        $c = str_replace(["\r\n", "\r"], "\n", $content);
        $c = preg_replace('/[\x{200B}-\x{200D}\x{2060}\x{FEFF}]/u', '', $c) ?? $c;
        $c = str_replace(["\xC2\xA0", '&nbsp;'], ' ', $c);

        // 1. needs WordPress media / slider data that is not available here
        foreach (['masterslider', 'envira-gallery', 'be_search', 'apss-share', 'wpcode', 'portfolio_list', 'data-writing-block'] as $tag) {
            $c = $this->replaceShortcode($c, $tag, fn () => '');
        }
        // raw HTML blocks hold base64 data, never readable text
        $c = $this->replaceEnclosing($c, 'vc_raw_html', fn () => '');

        // images: WordPress only stores media IDs in post content, so these are
        // converted when setMedia() was given an ID => URL map and dropped otherwise
        $c = $this->replaceShortcode($c, 'vc_single_image', fn (array $a) => $this->image($this->mediaUrls($a['image'] ?? ''), $a['alignment'] ?? 'center'));
        $c = $this->replaceShortcode($c, 'vc_gallery', fn (array $a) => $this->gallery($this->mediaUrls($a['images'] ?? ''), (int) ($a['column_number'] ?? 3)));
        $c = $this->replaceShortcode($c, 'gallery', fn (array $a) => $this->gallery($this->mediaUrls($a['ids'] ?? ''), (int) ($a['columns'] ?? 3)));
        $c = $this->replaceEnclosing($c, 'image_with_text', fn (array $a, string $in) => '<div class="wp-image-text" style="margin-bottom:16px">'
            . $this->image($this->mediaUrls($a['image'] ?? ''), 'center')
            . (!empty($a['title']) ? '<h4>' . $this->e(html_entity_decode($a['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8')) . '</h4>' : '')
            . $this->autop($in) . '</div>');
        $c = $this->replaceShortcode($c, 'image_with_text', fn (array $a) => $this->image($this->mediaUrls($a['image'] ?? ''), 'center'));
        $c = $this->replaceShortcode($c, 'blockquote', fn (array $a) => !empty($a['text'])
            ? '<blockquote style="margin:0 0 16px;padding:8px 0 8px 20px;border-left:4px solid ' . $this->css($a['text_color'] ?? '#00A79D') . ';font-style:italic">'
                . nl2br($this->e(html_entity_decode($a['text'], ENT_QUOTES | ENT_HTML5, 'UTF-8'))) . '</blockquote>'
            : '');
        $c = $this->replaceEnclosing($c, 'service_table', fn (array $a, string $in) => '<div class="wp-service" style="margin-bottom:16px">'
            . (!empty($a['title']) ? '<h3>' . $this->e(html_entity_decode($a['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8')) . '</h3>' : '') . $this->autop($in) . '</div>');
        $c = $this->replaceShortcode($c, 'service_table', fn (array $a) => !empty($a['title'])
            ? '<h3>' . $this->e(html_entity_decode($a['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8')) . '</h3>' : '');

        // 2. single shortcodes -> HTML
        $c = $this->replaceShortcode($c, 'qbutton', fn (array $a) => $this->button($a));
        $c = $this->replaceShortcode($c, 'vc_empty_space', fn (array $a) => $this->spacer($this->px($a['height'] ?? '20px')));
        $c = $this->replaceShortcode($c, 'vc_separator', fn (array $a) => $this->separator($a));
        $c = $this->replaceShortcode($c, 'vc_video', fn (array $a) => $this->video($a));

        // 3. shortcodes that wrap text
        $c = $this->replaceEnclosing($c, 'vc_column_text', fn (array $a, string $in) => '<div class="wp-text">' . $this->autop($in) . '</div>');
        $c = $this->replaceEnclosing($c, 'bsf-info-box', fn (array $a, string $in) => '<div class="wp-info" style="margin-bottom:16px">'
            . (!empty($a['title']) ? '<h4>' . $this->e($a['title']) . '</h4>' : '') . $this->autop($in) . '</div>');
        $c = $this->replaceEnclosing($c, 'qode_pricing_tables', function (array $a, string $in) {
            $columns = $this->pricingColumns($a['columns'] ?? '');
            $cards = $this->replaceEnclosing($in, 'qode_pricing_table', fn (array $card, string $text) => $this->pricingCard($card, $text, $columns));

            return '<div class="wp-pricing" style="display:flex;flex-wrap:wrap;gap:' . self::GAP . 'px">' . $cards . '</div>';
        });

        // 4. layout
        $c = $this->replaceEnclosing($c, 'vc_row_inner', fn (array $a, string $in) => $this->row($a, $in, true));
        $c = $this->replaceEnclosing($c, 'vc_row', fn (array $a, string $in) => $this->row($a, $in, false));
        $c = $this->replaceEnclosing($c, 'vc_column_inner', fn (array $a, string $in) => $this->column($a, $in));
        $c = $this->replaceEnclosing($c, 'vc_column', fn (array $a, string $in) => $this->column($a, $in));

        // the theme's "SEARCH ACCOMMODATION" bar (its search box shortcode is already gone)
        $c = preg_replace('/<(h[1-6]|p|strong)\b[^>]*>(?:(?!<\/\1>).)*?SEARCH ACCOMMODATION(?:(?!<\/\1>).)*?<\/\1>/is', '', $c) ?? $c;

        // 5. anything unknown: drop the tag, keep what is inside
        $c = preg_replace('/\[\/?[a-zA-Z0-9_-]+(?:\s[^\]]*)?\]/', '', $c) ?? $c;

        // The WordPress editor wraps shortcodes in <p> ... </p>; a <div> can't live
        // inside a <p>, so those wrappers are removed.
        $c = preg_replace('/<p>\s*(?=<div\b)/i', '', $c) ?? $c;
        $c = preg_replace('/(<\/div>)\s*<\/p>/i', '$1', $c) ?? $c;

        $c = $this->removeEmptyContainers($c);
        $c = $this->dropLeadingTitleHeading(trim($c), $title);
        $c = $this->removeEmptyContainers($c);
        $c = trim(preg_replace("/\n{3,}/", "\n\n", $c) ?? $c);

        return $c === '' ? null : $c;
    }

    /* ------------------------------------------------------------------ */
    /* Layout                                                             */
    /* ------------------------------------------------------------------ */

    private function row(array $a, string $inner, bool $nested): string
    {
        $style = ['display:flex', 'flex-wrap:wrap', 'gap:' . self::GAP . 'px'];

        if (!empty($a['text_align'])) {
            $style[] = 'text-align:' . $this->css($a['text_align']);
        }

        if (!empty($a['background_color'])) {
            $style[] = 'background-color:' . $this->css($a['background_color']);
            $style[] = 'padding:16px';
        }

        if (isset($a['padding_top']) && is_numeric($a['padding_top'])) {
            $style[] = 'padding-top:' . (int) $a['padding_top'] . 'px';
        }

        if (isset($a['padding_bottom']) && is_numeric($a['padding_bottom'])) {
            $style[] = 'padding-bottom:' . (int) $a['padding_bottom'] . 'px';
        }

        return '<div class="wp-row' . ($nested ? ' wp-row-inner' : '') . '" style="' . implode(';', $style) . '">' . $inner . '</div>';
    }

    private function column(array $a, string $inner): string
    {
        [$percent, $min] = $this->columnSize($a['width'] ?? '1/1');

        return '<div class="wp-col" style="flex:1 1 calc(' . $percent . '% - ' . self::GAP . 'px);min-width:' . $min . 'px">' . $inner . '</div>';
    }

    /**
     * "1/2" => [50, 240]. Narrow columns (1/6 ...) get a small min width so
     * they do not blow up; wide ones wrap under each other on small screens.
     */
    private function columnSize(string $width): array
    {
        if (!preg_match('/^(\d+)\/(\d+)$/', trim($width), $m) || (int) $m[2] === 0) {
            return [100, 240];
        }

        $percent = round(100 * (int) $m[1] / (int) $m[2], 2);

        return [$percent, $percent >= 30 ? 240 : 100];
    }

    /** @return string[] URLs from "12,13" (media IDs) or "https://..jpg|,https://..jpg|" */
    private function mediaUrls(string $value): array
    {
        $urls = [];
        foreach (preg_split('/[,|]+/', $value) ?: [] as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }
            if (preg_match('#^https?://#i', $part)) {
                $urls[] = $part;
            } elseif (isset($this->media[$part])) {
                $urls[] = $this->media[$part];
            }
        }

        return $urls;
    }

    private function image(array $urls, string $align): string
    {
        if (! $urls) {
            return '';
        }
        $text = in_array($align, ['left', 'right', 'center'], true) ? $align : 'center';

        return '<figure class="wp-image" style="margin:0 0 16px;text-align:' . $text . '"><img src="' . $this->e($urls[0])
            . '" alt="" loading="lazy" style="max-width:100%;height:auto"></figure>';
    }

    private function gallery(array $urls, int $columns): string
    {
        if (! $urls) {
            return '';
        }
        $columns = max(1, min($columns ?: 3, 6));
        $cells = '';
        foreach ($urls as $url) {
            $cells .= '<img src="' . $this->e($url) . '" alt="" loading="lazy" style="width:100%;height:100%;aspect-ratio:4/3;object-fit:cover;display:block">';
        }

        return '<div class="wp-gallery" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(' . (int) max(140, 900 / $columns - 24)
            . 'px,1fr));gap:8px;margin-bottom:16px">' . $cells . '</div>';
    }

    private function spacer(int $height): string
    {
        return '<div class="wp-space" style="height:' . $height . 'px" aria-hidden="true"></div>';
    }

    private function separator(array $a): string
    {
        $up = $this->px($a['up'] ?? '') ?: 10;
        $down = $this->px($a['down'] ?? '');

        if (($a['type'] ?? '') === 'transparent') {
            return $this->spacer($up + $down);
        }

        return '<hr class="wp-separator" style="margin:' . $up . 'px 0 ' . $down . 'px">';
    }

    /* ------------------------------------------------------------------ */
    /* Components                                                         */
    /* ------------------------------------------------------------------ */

    private function button(array $a): string
    {
        $text = $this->e($a['text'] ?? '');

        if ($text === '') {
            return '';
        }

        $background = $a['background_color'] ?? '';
        $color = $a['color'] ?? ($background !== '' ? '#ffffff' : '#333333');
        $border = $a['border_color'] ?? ($background !== '' ? $background : '#333333');
        $padding = ($a['size'] ?? '') === 'medium' ? '12px 28px' : '8px 22px';

        $style = 'display:inline-block;padding:' . $padding . ';border:2px solid ' . $this->css($border)
            . ';background-color:' . ($background !== '' ? $this->css($background) : 'transparent')
            . ';color:' . $this->css($color) . ';font-weight:' . (int) ($a['font_weight'] ?? 700)
            . ';text-decoration:none;text-transform:uppercase;margin:0 8px 8px 0';

        $link = trim(html_entity_decode($a['link'] ?? '', ENT_QUOTES | ENT_HTML5));

        if ($link === '') {
            return '<span class="wp-button" style="' . $style . '">' . $text . '</span>';
        }

        $blank = ($a['target'] ?? '') === '_blank' ? ' target="_blank" rel="noopener"' : '';

        return '<a class="wp-button" href="' . $this->e($link) . '"' . $blank . ' style="' . $style . '">' . $text . '</a>';
    }

    private function pricingColumns(string $value): int
    {
        return match (strtolower($value)) {
            'two_columns' => 2,
            'three_columns' => 3,
            'five_columns' => 5,
            'six_columns' => 6,
            default => 4,
        };
    }

    private function pricingCard(array $a, string $inner, int $columns = 4): string
    {
        $price = trim(($a['currency'] ?? '') . ' ' . ($a['price'] ?? ''));
        $period = !empty($a['price_period']) ? ' / ' . $this->e($a['price_period']) : '';
        $width = 'calc(' . round(100 / $columns, 2) . '% - ' . round(self::GAP * ($columns - 1) / $columns, 2) . 'px)';

        $list = str_replace('<ul>', '<ul style="list-style:none;padding:0;margin:0">', $this->autop($inner));

        return '<div class="wp-pricing-item" style="flex:1 1 ' . $width . ';max-width:' . $width . ';min-width:200px;box-sizing:border-box;border:1px solid #dddddd;padding:20px;text-align:center">'
            . (!empty($a['title']) ? '<h4>' . $this->e($a['title']) . '</h4>' : '')
            . ($price !== '' ? '<p class="wp-price"><strong>' . $this->e($price) . '</strong>' . $period . '</p>' : '')
            . $list . '</div>';
    }

    private function video(array $a): string
    {
        $link = trim($a['link'] ?? '');

        if (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $link, $m)) {
            $src = 'https://player.vimeo.com/video/' . $m[1];
        } elseif (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/)([\w-]{6,})~', $link, $m)) {
            $src = 'https://www.youtube.com/embed/' . $m[1];
        } else {
            return $link !== '' ? '<p><a href="' . $this->e($link) . '" target="_blank" rel="noopener">Watch the video</a></p>' : '';
        }

        return '<div class="wp-video" style="position:relative;height:0;padding-bottom:56.25%;margin-bottom:16px">'
            . '<iframe src="' . $src . '" style="position:absolute;top:0;left:0;width:100%;height:100%;border:0" allowfullscreen loading="lazy"></iframe></div>';
    }

    /* ------------------------------------------------------------------ */
    /* Text                                                               */
    /* ------------------------------------------------------------------ */

    /**
     * WordPress turns blank lines into paragraphs when it shows the page.
     * Same here: loose text between block elements becomes <p>, a single
     * line break becomes <br>, existing blocks (h2, ul, ...) are kept.
     */
    private function autop(string $html): string
    {
        $html = trim($html);

        if ($html === '') {
            return '';
        }

        $dom = new \DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8"?><div id="wp-root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $dom->getElementById('wp-root');

        if (!$root) {
            return $html;
        }

        $out = [];
        $inline = '';

        $flush = function () use (&$inline, &$out) {
            foreach (preg_split('/\n{2,}/', trim($inline)) as $chunk) {
                $chunk = trim($chunk);

                if ($chunk !== '') {
                    $out[] = '<p>' . nl2br($chunk, false) . '</p>';
                }
            }

            $inline = '';
        };

        foreach ($root->childNodes as $node) {
            $isBlock = $node instanceof \DOMElement && in_array(strtolower($node->nodeName), self::BLOCK_TAGS, true);

            if ($isBlock) {
                $flush();
                $out[] = $dom->saveHTML($node);
            } else {
                $inline .= $dom->saveHTML($node);
            }
        }

        $flush();

        return implode("\n", $out);
    }

    /* ------------------------------------------------------------------ */
    /* Clean up                                                           */
    /* ------------------------------------------------------------------ */

    /**
     * Rows / columns / headings left with nothing in them (a removed slider, a
     * removed search box ...). Spacers alone do not count as content.
     */
    private function removeEmptyContainers(string $html): string
    {
        $spacer = '<div class="wp-space"[^>]*><\/div>';
        $pattern = '/<(h[1-6]|p|strong|em|b|i|span|li|ul|ol)\b[^>]*>(?:\s|<br\s*\/?>)*<\/\1>'
            . '|<div\b(?![^>]*wp-space)[^>]*>(?:\s|' . $spacer . ')*<\/div>/i';

        do {
            $html = preg_replace($pattern, '', $html, -1, $removed) ?? $html;
        } while ($removed > 0);

        return $html;
    }

    /**
     * "<h1>Hai Bar &amp; Grill</h1>" in front of an Experience called
     * "HAI BAR & GRILL" says nothing new, so it is removed.
     */
    private function dropLeadingTitleHeading(string $html, string $title): string
    {
        if ($title === '' || !preg_match('/^((?:\s|<div\b[^>]*>)*)<h([1-6])\b[^>]*>(.*?)<\/h\2>\s*/is', $html, $m)) {
            return $html;
        }

        $heading = $this->normalize(strip_tags($m[3]));
        $name = $this->normalize($title);

        if (strlen($heading) < 3 || $name === '') {
            return $html;
        }

        $same = $heading === $name || str_contains($name, $heading) || str_contains($heading, $name);

        return $same ? $m[1] . substr($html, strlen($m[0])) : $html;
    }

    /* ------------------------------------------------------------------ */
    /* Shortcode helpers                                                  */
    /* ------------------------------------------------------------------ */

    private function replaceShortcode(string $content, string $tag, callable $callback): string
    {
        $pattern = '/\[' . preg_quote($tag, '/') . '((?:\s[^\]]*)?)\]/';

        return preg_replace_callback($pattern, fn (array $m) => $callback($this->attributes($m[1])), $content) ?? $content;
    }

    /**
     * Replaces [tag ...]inner[/tag], innermost first so nesting works
     * ([vc_row][vc_column][vc_row_inner]...).
     */
    private function replaceEnclosing(string $content, string $tag, callable $callback): string
    {
        $quoted = preg_quote($tag, '/');
        $pattern = '/\[' . $quoted . '((?:\s[^\]]*)?)\]((?:(?!\[' . $quoted . '[\s\]]).)*?)\[\/' . $quoted . '\]/s';

        do {
            $content = preg_replace_callback($pattern, fn (array $m) => $callback($this->attributes($m[1]), $m[2]), $content, -1, $count) ?? $content;
        } while ($count > 0);

        return $content;
    }

    private function attributes(string $raw): array
    {
        preg_match_all('/([a-zA-Z0-9_-]+)\s*=\s*(?:"([^"]*)"|\'([^\']*)\')/', $raw, $matches, PREG_SET_ORDER);

        $attributes = [];

        foreach ($matches as $m) {
            $attributes[$m[1]] = html_entity_decode($m[2] !== '' ? $m[2] : ($m[3] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return $attributes;
    }

    private function px(string $value): int
    {
        return preg_match('/\d+/', $value, $m) ? (int) $m[0] : 0;
    }

    /** Only characters a CSS color / keyword can contain. */
    private function css(string $value): string
    {
        return preg_replace('/[^a-zA-Z0-9#(),.% -]/', '', $value) ?? '';
    }

    private function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    private function normalize(string $text): string
    {
        return preg_replace('/[^a-z0-9]+/', '', Str::lower(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'))) ?? '';
    }
}
