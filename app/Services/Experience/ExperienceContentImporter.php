<?php

namespace App\Services\Experience;

use App\Models\Experience\Experience;
use App\Models\SEO\ContentSeo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Applies parsed experience content documents (ExperienceContentDocParser) to
 * the Experience with the same name and type (EAT folder => Eat, PLAY => Play):
 * description (sections + FAQs), excerpt, SEO title / description, an FAQPage
 * schema, and the practical information (opening hours, address, Instagram,
 * website, WhatsApp, email) when those fields are still empty.
 *
 * Area and tags are not changed.
 */
class ExperienceContentImporter
{
    /** Words left out when comparing names. */
    private const NAME_STOP_WORDS = ['the', 'nusa', 'lembongan', 'restaurant', 'and', 'at'];

    private ?Collection $experiences = null;

    public function __construct(private bool $dryRun = false, private bool $onlyEmpty = false)
    {
    }

    /**
     * Pairs every document with an Experience. A record gets one document: an exact
     * name match wins over a partial one, then the document whose sub folder is
     * like the record's area (e.g. "Snorkel" for the area "Snorkelling").
     *
     * @param array<int,array{doc:array,type:string,folder:string}> $docs
     * @return array{0: array<int,array{doc:array,experience:Experience}>, 1: array<int,string>} [matched, skipped with reason]
     */
    public function match(array $docs): array
    {
        $this->experiences ??= Experience::with(['type', 'area'])->get();

        $candidates = [];
        $skipped = [];

        foreach ($docs as $i => $item) {
            $name = $item['name'];
            $sameType = $this->experiences->filter(fn (Experience $e) => strcasecmp((string) $e->type?->name, $item['type']) === 0);

            $exact = $sameType->filter(fn ($e) => $this->exactKey($e->name) === $this->exactKey($name));
            $partial = $exact->isEmpty()
                ? $sameType->filter(fn ($e) => $this->contains($e->name, $name))
                : collect();

            $found = $exact->isNotEmpty() ? $exact : $partial;

            if ($found->count() > 1) {
                $skipped[$i] = "{$item['label']}: more than one {$item['type']} Experience matches: " . $found->map(fn ($e) => "#{$e->id} {$e->name}")->implode(', ');
                continue;
            }

            if ($found->isEmpty()) {
                $other = $this->experiences->filter(fn ($e) => $this->exactKey($e->name) === $this->exactKey($name) || $this->contains($e->name, $name));
                $skipped[$i] = "{$item['label']}: no {$item['type']} Experience with this name"
                    . ($other->isNotEmpty() ? ' (only as ' . $other->map(fn ($e) => "{$e->type?->name} #{$e->id} {$e->name}")->implode(', ') . ')' : '');
                continue;
            }

            $experience = $found->first();
            $score = ($exact->isNotEmpty() ? 2 : 0) + ($this->folderLikeArea($item['folder'], $experience->area?->name) ? 1 : 0);
            $candidates[$experience->id][] = ['index' => $i, 'score' => $score, 'experience' => $experience];
        }

        $matched = [];

        foreach ($candidates as $list) {
            usort($list, fn ($a, $b) => [$b['score'], $a['index']] <=> [$a['score'], $b['index']]);
            $best = array_shift($list);
            $matched[$best['index']] = ['doc' => $docs[$best['index']], 'experience' => $best['experience']];

            foreach ($list as $other) {
                $skipped[$other['index']] = "{$docs[$other['index']]['label']}: Experience #{$best['experience']->id} {$best['experience']->name} already uses {$docs[$best['index']]['label']}";
            }
        }

        ksort($matched);
        ksort($skipped);

        return [array_values($matched), array_values($skipped)];
    }

    /** @return array<string,string> what was (or would be) changed, by field */
    public function apply(Experience $experience, array $doc): array
    {
        $changes = [];
        $data = [];

        $description = $this->descriptionHtml($doc);
        if ($description !== '' && $this->canSet($experience->description)) {
            $data['description'] = $description;
            $changes['description'] = count($doc['sections']) . ' section(s)' . ($doc['faqs'] ? ', ' . count($doc['faqs']) . ' FAQ(s)' : '');
        }

        $excerpt = $doc['intro'][0]['text'] ?? null;
        if ($excerpt && $this->canSet($experience->excerpt)) {
            $data['excerpt'] = $excerpt;
            $changes['excerpt'] = Str::limit($excerpt, 60);
        }

        // practical information: only into empty fields
        foreach (['openHours', 'address', 'instagram', 'website'] as $field) {
            $value = $doc['practical'][$field] ?? null;

            if ($value && blank($experience->{$field})) {
                $data[$field] = Str::limit($field === 'website' ? $this->url($value) : $value, 255, '');
                $changes[$field] = $data[$field];
            }
        }

        $contact = (array) ($experience->contactInfo ?? []);
        foreach (['whatsapp', 'email'] as $field) {
            if (!empty($doc['practical'][$field]) && blank($contact[$field] ?? null)) {
                $contact[$field] = $doc['practical'][$field];
                $changes[$field] = $contact[$field];
                $data['contactInfo'] = $contact;
            }
        }

        $seo = $this->seoData($experience, $doc);
        if ($seo) {
            $changes['seo'] = implode(' + ', array_keys($seo));
        }

        if (!$this->dryRun && ($data || $seo)) {
            DB::transaction(function () use ($experience, $data, $seo) {
                if ($data) {
                    $experience->update($data);
                }

                if ($seo) {
                    $current = $experience->seo()->first();
                    $current
                        ? $current->update($seo)
                        : $experience->seo()->create($seo + [
                            'slug' => $this->uniqueSlug(Str::slug($experience->name) ?: 'experience-' . $experience->id),
                            'robotIndex' => true,
                            'robotFollow' => true,
                        ]);
                }
            });
        }

        return $changes;
    }

    private function seoData(Experience $experience, array $doc): array
    {
        $current = $experience->seo()->first();
        $data = [];

        if ($doc['heading'] && $this->canSet($current?->title, $experience->name)) {
            $data['title'] = Str::limit($doc['heading'], 255, '');
        }

        if ($doc['metaDesc'] && $this->canSet($current?->description)) {
            $data['description'] = $doc['metaDesc'];
        }

        // FAQPage structured data; a schema written by hand (another type) is kept
        $schema = $current?->schemaMarkup;
        if ($doc['faqs'] && (empty($schema) || ($schema['@type'] ?? null) === 'FAQPage') && !($this->onlyEmpty && !empty($schema))) {
            $data['schemaMarkup'] = [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => array_map(fn ($faq) => [
                    '@type' => 'Question',
                    'name' => $faq['question'],
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['answer']],
                ], $doc['faqs']),
            ];
        }

        return $data;
    }

    /** Overwrite unless --only-empty (then only when empty, or still the default value). */
    private function canSet($current, ?string $default = null): bool
    {
        if (!$this->onlyEmpty) {
            return true;
        }

        $current = trim(strip_tags((string) $current));

        return $current === '' || ($default !== null && strcasecmp($current, $default) === 0);
    }

    /** Intro + sections (<h2>, <p>, <ul>, <table>) + FAQs (<h2>FAQs</h2>, <h3> question, <p> answer). */
    public function descriptionHtml(array $doc): string
    {
        $html = $this->blocksHtml($doc['intro']);

        foreach ($doc['sections'] as $section) {
            $html .= '<h2>' . e($section['heading']) . '</h2>' . $this->blocksHtml($section['blocks']);
        }

        if ($doc['faqs']) {
            $html .= '<h2>FAQs</h2>';
            foreach ($doc['faqs'] as $faq) {
                $html .= '<h3>' . e($faq['question']) . '</h3><p>' . $faq['answerHtml'] . '</p>';
            }
        }

        return $html;
    }

    private function blocksHtml(array $blocks): string
    {
        $html = '';
        $list = [];

        $flush = function () use (&$html, &$list) {
            if ($list) {
                $html .= '<ul>' . implode('', array_map(fn ($item) => "<li>{$item}</li>", $list)) . '</ul>';
                $list = [];
            }
        };

        foreach ($blocks as $block) {
            if ($block['list']) {
                $list[] = $block['html'];
                continue;
            }

            $flush();

            if (isset($block['table'])) {
                $html .= '<table><tbody>';
                foreach ($block['table'] as $row) {
                    $html .= '<tr>' . implode('', array_map(fn ($cell) => "<td>{$cell}</td>", $row)) . '</tr>';
                }
                $html .= '</tbody></table>';
                continue;
            }

            // a bold line inside a section ("Option A: Lembongan") as a sub heading
            $html .= $block['allBold']
                ? '<h3>' . e($block['text']) . '</h3>'
                : '<p>' . $block['html'] . '</p>';
        }

        $flush();

        return $html;
    }

    private function url(string $value): string
    {
        return preg_match('#^https?://#i', $value) ? $value : 'https://' . $value;
    }

    private function uniqueSlug(string $base): string
    {
        $slug = $base;
        $i = 2;

        while (ContentSeo::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    /** "Snorkel" ~ "Snorkelling", "Beach Clubs" ~ "Beach Club", "Day Spas" ~ "Day Spa". */
    private function folderLikeArea(string $folder, ?string $area): bool
    {
        $a = $this->exactKey($folder);
        $b = $this->exactKey((string) $area);

        return $a !== '' && $b !== '' && (str_starts_with($a, rtrim($b, 's')) || str_starts_with($b, rtrim($a, 's')));
    }

    private function exactKey(?string $name): string
    {
        return preg_replace('/[^a-z0-9]+/', '', strtolower(Str::ascii(str_replace(['’', "'"], '', (string) $name))));
    }

    /** All words of one name are in the other ("La Bianca" / "LA BIANCA NUSA LEMBONGAN"). */
    private function contains(string $a, string $b): bool
    {
        [$x, $y] = [$this->words($a), $this->words($b)];

        if (!$x || !$y) {
            return false;
        }

        return !array_diff($x, $y) || !array_diff($y, $x);
    }

    private function words(string $name): array
    {
        $words = preg_split('/[^a-z0-9]+/', strtolower(Str::ascii(str_replace(['’', "'"], '', $name))), -1, PREG_SPLIT_NO_EMPTY);
        $words = array_map(fn ($w) => strlen($w) > 3 ? rtrim($w, 's') : $w, $words);

        return array_values(array_unique(array_diff($words, self::NAME_STOP_WORDS)));
    }
}
