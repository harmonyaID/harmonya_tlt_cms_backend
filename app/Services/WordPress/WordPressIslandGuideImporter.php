<?php

namespace App\Services\WordPress;

use App\Models\IslandGuide\IslandGuide;
use App\Models\IslandGuide\IslandGuideType;
use App\Models\SEO\ContentSeo;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Maps one raw row of the WordPress "Select Portfolio Export" into an
 * Island Guide (name, type, thumbnail, description). Same role and shape as
 * WordPressExperienceImporter.
 *
 * The Type comes from the "Type" column of the TLT Migration List (MAP below,
 * keyed by WordPress post ID):
 *
 *   "Island Guide - Island Shop"
 *            |            '-> Island Guide Type (firstOrCreate)
 *            '--------------> saved as an Island Guide
 *
 * Island Guide Area is not used (the list has no area for Island Guide rows).
 * Safe to re-run: an existing Island Guide is matched by its WordPress slug
 * (kept as its SEO slug) and updated in place instead of duplicated.
 */
class WordPressIslandGuideImporter
{
    /** Thumbnails that could not be put in place (file names). */
    public array $missingThumbnails = [];

    private array $types = [];

    public function __construct(private WordPressContentConverter $converter)
    {
    }

    /**
     * WordPress post ID => "Type" column of the TLT Migration List.
     */
    private const MAP = [
        52078 => 'Island Guide - Island Transport',
        60951 => 'Island Guide - Island Services',
        72892 => 'Island Guide - Island Services',
        77323 => 'Island Guide - Island Services',
        32713 => 'Island Guide - Island Shop',
        37968 => 'Island Guide - Island Shop',
        53213 => 'Island Guide - Island Shop',
        63320 => 'Island Guide - Island Shop',
        70415 => 'Island Guide - Island Shop',
        74827 => 'Island Guide - Island Shop',
        77116 => 'Island Guide - Island Shop',
        76856 => 'Island Guide - Island Transport',
        77281 => 'Island Guide - Island Transport',
    ];

    /** @param array<int|string,string> $idToUrl WordPress media ID => URL, for images inside descriptions */
    public function setMedia(array $idToUrl): void
    {
        $this->converter->setMedia($idToUrl);
    }

    /**
     * @return array{type:string}|null null when the row is not an Island Guide
     */
    public function resolve(array $row): ?array
    {
        $label = self::MAP[(int) ($row['ID'] ?? 0)] ?? null;

        if (!$label) {
            return null;
        }

        [, $type] = array_pad(array_map('trim', explode(' - ', $label, 2)), 2, null);

        return $type ? ['type' => $type] : null;
    }

    /**
     * @param string|null $imageDir folder with the thumbnail files (copied when found there)
     * @param bool $download when a thumbnail is not in $imageDir, download it from its WordPress URL
     */
    public function import(array $row, ?string $imageDir = null, bool $withDescription = true, bool $download = false): IslandGuide
    {
        $target = $this->resolve($row);

        if (!$target) {
            throw new \InvalidArgumentException('Row ' . ($row['ID'] ?? '?') . ' is not an Island Guide in the migration list.');
        }

        return DB::transaction(function () use ($row, $target, $imageDir, $withDescription, $download) {
            $type = $this->types[$target['type']] ??= IslandGuideType::firstOrCreate(['name' => $target['type']]);

            $title = $this->decode($row['Title'] ?? null) ?: 'Untitled';
            $slug = trim($row['Slug'] ?? '') ?: Str::slug($title);

            // ContentSeo::contentable() can't be used here (its morphTo() looks for
            // snake_case columns), so the owner is looked up by contentableId.
            $seoId = ContentSeo::where('slug', $slug)
                ->where('contentableType', IslandGuide::class)
                ->value('contentableId');

            $guide = $seoId ? IslandGuide::find($seoId) : null;

            $attributes = [
                'islandGuideTypeId' => $type->id,
                'name' => $title,
                'isActive' => ($row['Status'] ?? null) === 'publish',
            ];

            if ($withDescription) {
                $attributes['description'] = $this->converter->convert($row['Content'] ?? '', $title);
            }

            $imageUrl = trim((string) ($row['Image URL'] ?? ''));
            $thumbnail = $this->thumbnailName($imageUrl);

            if ($guide) {
                $guide->update($attributes);

                // thumbnail is only filled in when the guide has none yet
                if (!$guide->thumbnail && $thumbnail) {
                    $guide->update(['thumbnail' => $thumbnail]);
                }
            } else {
                $guide = IslandGuide::create($attributes + [
                    'thumbnail' => $thumbnail,
                    'showInquiry' => false,
                    'locale' => 'en',
                    'createdAt' => $this->date($row['Date'] ?? null),
                    'updatedAt' => $this->date($row['Post Modified Date'] ?? null),
                ]);
            }

            if ($guide->thumbnail && !$this->placeThumbnail($guide->thumbnail, $imageUrl, $imageDir, $download)) {
                $this->missingThumbnails[] = $guide->thumbnail;
            }

            $this->syncSeo($guide, $slug, $title, $row);

            return $guide;
        });
    }

    private function syncSeo(IslandGuide $guide, string $slug, string $title, array $row): void
    {
        $seoTitle = trim($row['_yoast_wpseo_title'] ?? '');

        $guide->seo()->updateOrCreate(
            [
                'contentableId' => $guide->id,
                'contentableType' => IslandGuide::class,
            ],
            [
                'title' => $this->decode($seoTitle !== '' && !str_contains($seoTitle, '%%') ? $seoTitle : $title),
                'slug' => $slug,
                'description' => $this->decode($row['_yoast_wpseo_metadesc'] ?? null),
                'robotIndex' => true,
                'robotFollow' => true,
            ]
        );
    }

    /**
     * File name of the image, taken from its WordPress URL
     * (.../2018/08/Q8-e1533632481995-1.jpg => Q8-e1533632481995-1.jpg).
     */
    private function thumbnailName(?string $imageUrl): ?string
    {
        $path = parse_url(trim((string) $imageUrl), PHP_URL_PATH);

        return $path ? (rawurldecode(basename($path)) ?: null) : null;
    }

    /**
     * Makes sure <island guide image folder>/<file> exists: already there, copied
     * from $imageDir, or (when $download) downloaded from the WordPress URL.
     * Returns false when none of these worked.
     */
    private function placeThumbnail(string $filename, string $imageUrl, ?string $imageDir, bool $download): bool
    {
        $dirPath = PathConstant::IMAGES_ISLAND_GUIDE_STORAGE_PUBLIC_PATH();

        if (is_file($dirPath . $filename)) {
            return true;
        }

        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        if ($imageDir) {
            $source = rtrim($imageDir, '/\\') . DIRECTORY_SEPARATOR . $filename;

            if (is_file($source)) {
                return copy($source, $dirPath . $filename);
            }
        }

        if ($download && preg_match('#^https?://#i', $imageUrl)) {
            try {
                $response = Http::timeout(30)->get($imageUrl);

                if ($response->successful() && $response->body() !== '') {
                    return file_put_contents($dirPath . $filename, $response->body()) !== false;
                }
            } catch (\Throwable $e) {
                // falls through: reported as a missing thumbnail
            }
        }

        return false;
    }

    private function decode(?string $value): ?string
    {
        $value = trim(html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        return $value === '' ? null : $value;
    }

    private function date(?string $value): ?string
    {
        $value = trim((string) $value);

        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value . ' 00:00:00' : null;
    }
}
