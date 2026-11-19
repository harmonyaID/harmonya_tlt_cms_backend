<?php

namespace App\Services\WordPress;

use App\Models\Property\Property;
use App\Models\Property\PropertyDescription;
use App\Models\Property\PropertyPhoto;
use App\Models\Property\PropertyPricing;
use App\Models\Property\PropertySourceType;
use App\Models\Property\PropertyType;
use App\Models\Property\PropertyVariant;
use App\Models\SEO\ContentSeo;
use App\Services\Constant\Property\PropertyListingType;
use App\Services\Constant\Property\PropertyStatus;
use App\Services\Constant\Property\PropertyUnitType;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Maps one "Stay" row of the WordPress "Select Portfolio Export" into a parent
 * Property with source type Bookeasy - same role as WordPressExperienceImporter.
 *
 *   Type "Stay - Villa"  -> Property type Villa
 *   Type "Stay - Resort" -> Property type Resort, Multi Unit
 *   Title "VILLA PANTAI • From IDR 11,625K" -> nickname "VILLA PANTAI", base price 11,625,000 IDR
 *
 * A row is never imported over existing data: when a Property or a Guesty
 * PropertyVariant with the same name (or SEO slug) already exists, existing()
 * describes it and the row is skipped.
 */
class WordPressStayImporter
{
    public const SOURCE_TYPE = 'Bookeasy';

    /** Photos that were not found in the image folder / could not be downloaded (file names). */
    public array $missingPhotos = [];

    /** Words left out when comparing names ("VILLA SAYANG" = "Sayang Villas"). */
    private const NAME_STOP_WORDS = [
        'villa', 'villas', 'resort', 'resorts', 'bungalow', 'bungalows', 'private', 'retreat',
        'lembongan', 'the', 'and', 'at', 'br', 'bed', 'bedroom', 'bedrooms',
    ];

    private ?array $existingNames = null;
    private array $types = [];
    private ?int $sourceTypeId = null;

    public function __construct(private WordPressContentConverter $converter)
    {
    }

    /** @param array<int|string,string> $idToUrl WordPress media ID => URL, for images inside descriptions */
    public function setMedia(array $idToUrl): void
    {
        $this->converter->setMedia($idToUrl);
    }

    /**
     * ['type' => 'Villa' | 'Resort' | null, 'multiUnit' => bool] for a Stay row,
     * null for every other row of the export.
     */
    public function resolve(array $row): ?array
    {
        $label = trim($row['Type'] ?? '');

        if (!preg_match('/^Stay\b/i', $label)) {
            return null;
        }

        [, $type] = array_pad(array_map('trim', explode(' - ', $label, 2)), 2, null);
        $labels = $label . ' ' . ($row['Additional Type'] ?? '');

        return [
            'type' => $type ?: null,
            'multiUnit' => stripos($labels, 'Resort') !== false,
        ];
    }

    /** "VILLA PANTAI • From IDR 11,625K" -> "VILLA PANTAI" */
    public function name(array $row): string
    {
        $title = $this->decode($row['Title'] ?? null) ?? '';

        return trim(preg_split('/\s*[•·]\s*From\b/iu', $title)[0]) ?: 'Untitled';
    }

    /**
     * Description of the Property / Guesty variant this row already exists as,
     * null when it does not exist yet.
     */
    public function existing(array $row): ?string
    {
        $slug = trim($row['Slug'] ?? '');

        if ($slug !== '' && ($id = ContentSeo::where('slug', $slug)->where('contentableType', Property::class)->value('contentableId'))) {
            return "Property #{$id} (same slug \"{$slug}\")";
        }

        $key = $this->nameKey($this->name($row));

        if ($key === '') {
            return null;
        }

        $matches = $this->existingNames()[$key] ?? [];

        return $matches ? implode(', ', $matches) : null;
    }

    public function import(array $row, ?string $imageDir = null, bool $download = false): Property
    {
        $target = $this->resolve($row);

        if (!$target) {
            throw new \InvalidArgumentException('Row ' . ($row['ID'] ?? '?') . ' is not a Stay.');
        }

        return DB::transaction(function () use ($row, $target, $imageDir, $download) {
            $name = $this->name($row);
            $price = $this->price($row['Title'] ?? '');

            $property = Property::create([
                'nickname' => $name,
                'propertyTypeId' => $target['type']
                    ? ($this->types[$target['type']] ??= PropertyType::firstOrCreate(['name' => $target['type']])->id)
                    : null,
                'unitTypeId' => $target['multiUnit'] ? PropertyUnitType::MULTI_UNIT_ID : PropertyUnitType::SINGLE_UNIT_ID,
                'listingTypeId' => PropertyListingType::ENTIRE_HOME_ID,
                'statusId' => ($row['Status'] ?? null) === 'publish' ? PropertyStatus::ACTIVE_ID : PropertyStatus::INACTIVE_ID,
                'sourceTypeId' => $this->sourceTypeId ??= PropertySourceType::firstOrCreate(['name' => self::SOURCE_TYPE])->id,
                'currency' => $price ? 'IDR' : 'USD',
                'sourceCreatedAt' => $this->date($row['Date'] ?? null),
                'sourceUpdatedAt' => $this->date($row['Post Modified Date'] ?? null),
            ]);

            PropertyDescription::create([
                'propertyId' => $property->id,
                'channel' => 'primary',
                'language' => 'en',
                'title' => $name,
                'summary' => $this->converter->convert($row['Content'] ?? '', $this->decode($row['Title'] ?? null) ?? $name),
            ]);

            if ($price) {
                PropertyPricing::create([
                    'propertyId' => $property->id,
                    'weekdayBasePrice' => $price,
                ]);
            }

            $this->syncPhotos($property, $row, $imageDir, $download);
            $this->syncSeo($property, $name, $row);

            $this->existingNames[$this->nameKey($name)][] = "Property #{$property->id} \"{$name}\"";

            return $property;
        });
    }

    private function syncPhotos(Property $property, array $row, ?string $imageDir, bool $download): void
    {
        $images = array_map('trim', explode('|', (string) ($row['Image URL'] ?? '')));
        $titles = array_map('trim', explode('|', (string) ($row['Image Title'] ?? '')));
        $captions = array_combine($images, array_pad(array_slice($titles, 0, count($images)), count($images), null));

        // featured image first, then the rest in WordPress order
        $urls = array_values(array_unique(array_filter(
            array_merge([trim((string) ($row['Image Featured'] ?? ''))], $images),
            fn ($url) => preg_match('#^https?://#i', $url)
        )));

        $wordpressId = (int) ($row['ID'] ?? 0);

        foreach ($urls as $order => $url) {
            $original = $this->fileName($url);

            if (!$original) {
                continue;
            }

            $filename = "wp-{$wordpressId}-{$original}";

            if (!$this->placePhoto($filename, $original, $url, $imageDir, $download)) {
                $this->missingPhotos[] = $original;
                continue;
            }

            PropertyPhoto::create([
                'propertyId' => $property->id,
                'path' => $filename,
                'caption' => $this->decode($captions[$url] ?? null),
                'order' => $order,
            ]);
        }
    }

    private function syncSeo(Property $property, string $name, array $row): void
    {
        $seoTitle = trim($row['_yoast_wpseo_title'] ?? '');
        $base = trim($row['Slug'] ?? '') ?: (Str::slug($name) ?: 'property-' . $property->id);

        $slug = $base;
        $i = 2;

        while (ContentSeo::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        $property->seo()->create([
            'title' => $this->decode($seoTitle !== '' && !str_contains($seoTitle, '%%') ? $seoTitle : $name),
            'slug' => $slug,
            'description' => $this->decode($row['_yoast_wpseo_metadesc'] ?? null),
            'robotIndex' => true,
            'robotFollow' => true,
        ]);
    }

    /**
     * Makes sure <property photo folder>/<file> exists: already there, copied
     * from $imageDir, or (when $download) downloaded from the WordPress URL.
     */
    private function placePhoto(string $filename, string $original, string $url, ?string $imageDir, bool $download): bool
    {
        $dirPath = PathConstant::IMAGES_PROPERTY_PHOTO_STORAGE_PUBLIC_PATH();

        if (is_file($dirPath . $filename)) {
            return true;
        }

        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        if ($imageDir && is_file($source = rtrim($imageDir, '/\\') . DIRECTORY_SEPARATOR . $original)) {
            return copy($source, $dirPath . $filename);
        }

        if (!$download) {
            return false;
        }

        try {
            $response = Http::timeout(60)->connectTimeout(20)->retry(3, fn (int $attempt) => $attempt * 3000, throw: false)->get($url);

            return $response->successful() && $response->body() !== ''
                && file_put_contents($dirPath . $filename, $response->body()) !== false;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * [name key => ["Property #1 \"Aqua Nusa\"", ...]] of every Property and Guesty
     * variant. A variant is also listed under the part before " - "
     * ("Casa Batu - 2BR" -> "Casa Batu").
     */
    private function existingNames(): array
    {
        if ($this->existingNames !== null) {
            return $this->existingNames;
        }

        $names = [];

        foreach (Property::get(['id', 'nickname']) as $property) {
            $names[$this->nameKey($property->nickname)][] = "Property #{$property->id} \"{$property->nickname}\"";
        }

        foreach (PropertyVariant::get(['id', 'nickname', 'parentPropertyId']) as $variant) {
            $label = "Variant #{$variant->id} \"{$variant->nickname}\"" . ($variant->parentPropertyId ? '' : ' (no parent)');
            $base = trim(explode(' - ', $variant->nickname, 2)[0]);

            foreach (array_unique([$this->nameKey($variant->nickname), $this->nameKey($base)]) as $key) {
                $names[$key][] = $label;
            }
        }

        unset($names['']);

        return $this->existingNames = $names;
    }

    /** "Lembongan Hilltop Private Retreat Villa" and "Hilltop Retreat" -> "hilltop" */
    private function nameKey(?string $name): string
    {
        $name = mb_strtolower($this->decode($name) ?? '');
        $words = preg_split('/[^a-z]+/', $name, -1, PREG_SPLIT_NO_EMPTY);
        $words = array_unique(array_diff($words, self::NAME_STOP_WORDS));
        sort($words);

        return implode(' ', $words);
    }

    /** "From IDR 11,625K" -> 11625000, "From IDR 810K" -> 810000 */
    private function price(string $title): ?float
    {
        if (!preg_match('/From\s+IDR\s*([\d.,]+)\s*([KM])?/iu', html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8'), $m)) {
            return null;
        }

        $number = (float) str_replace(',', '', $m[1]);
        $multiplier = match (strtoupper($m[2] ?? '')) {
            'K' => 1000,
            'M' => 1000000,
            default => 1,
        };

        return $number > 0 ? $number * $multiplier : null;
    }

    private function fileName(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);

        return $path ? (rawurldecode(basename($path)) ?: null) : null;
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
