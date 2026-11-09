<?php

namespace App\Services\WordPress;

use App\Models\Experience\Experience;
use App\Models\Experience\ExperienceArea;
use App\Models\Experience\ExperienceType;
use App\Models\SEO\ContentSeo;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WordPressExperienceImporter
{
    public array $missingThumbnails = [];

    private array $types = [];

    private array $areas = [];

    public function __construct(private WordPressContentConverter $converter)
    {
    }

    private const MAP = [
        23455 => 'Experience - Eat - Jungutbatu',
        30373 => 'Experience - Eat - Jungutbatu',
        32055 => 'Experience - Eat - Jungutbatu',
        48063 => 'Experience - Eat - Jungutbatu',
        49361 => 'Experience - Eat - Jungutbatu',
        52038 => 'Experience - Eat - Jungutbatu',
        54140 => 'Experience - Eat - Jungutbatu',
        54311 => 'Experience - Eat - Jungutbatu',
        58752 => 'Experience - Eat - Jungutbatu',
        61539 => 'Experience - Eat - Jungutbatu',
        61695 => 'Experience - Eat - Jungutbatu',
        62344 => 'Experience - Eat - Jungutbatu',
        65129 => 'Experience - Eat - Jungutbatu',
        66620 => 'Experience - Eat - Jungutbatu',
        69066 => 'Experience - Eat - Jungutbatu',
        70826 => 'Experience - Eat - Jungutbatu',
        71328 => 'Experience - Eat - Jungutbatu',
        71362 => 'Experience - Eat - Jungutbatu',
        71893 => 'Experience - Eat - Jungutbatu',
        74514 => 'Experience - Eat - Jungutbatu',
        74722 => 'Experience - Eat - Jungutbatu',
        75048 => 'Experience - Eat - Jungutbatu',
        75641 => 'Experience - Eat - Jungutbatu',
        75972 => 'Experience - Eat - Jungutbatu',
        78148 => 'Experience - Eat - Jungutbatu',
        78680 => 'Experience - Eat - Jungutbatu',
        78940 => 'Experience - Eat - Jungutbatu',
        78947 => 'Experience - Eat - Jungutbatu',
        78961 => 'Experience - Eat - Jungutbatu',
        79139 => 'Experience - Eat - Jungutbatu',
        23701 => 'Experience - Eat - Jungutbatu',
        23467 => 'Experience - Eat - Mushroom and Sandy Bay',
        23525 => 'Experience - Eat - Mushroom and Sandy Bay',
        33848 => 'Experience - Eat - Mushroom and Sandy Bay',
        51636 => 'Experience - Eat - Mushroom and Sandy Bay',
        67119 => 'Experience - Eat - Mushroom and Sandy Bay',
        77519 => 'Experience - Eat - Mushroom and Sandy Bay',
        30768 => 'Experience - Eat - Nusa Ceningan',
        32059 => 'Experience - Eat - Nusa Ceningan',
        41401 => 'Experience - Eat - Nusa Ceningan',
        71601 => 'Experience - Eat - Nusa Ceningan',
        78412 => 'Experience - Eat - Nusa Ceningan',
        25818 => 'Experience - Play - Beach Club',
        78802 => 'Experience - Play - Cowork',
        51720 => 'Experience - Play - Day Spa',
        60016 => 'Experience - Play - Day Spa',
        71185 => 'Experience - Play - Day Spa',
        73668 => 'Experience - Play - Day Spa',
        74033 => 'Experience - Play - Day Spa',
        75526 => 'Experience - Play - Day Spa',
        77758 => 'Experience - Play - Day Spa',
        78437 => 'Experience - Play - Day Spa',
        23662 => 'Experience - Play - Day Spa',
        70789 => 'Experience - Play - Day Spa',
        23594 => 'Experience - Play - Dive',
        50030 => 'Experience - Play - Dive',
        51157 => 'Experience - Play - Dive',
        76048 => 'Experience - Play - Dive',
        58263 => 'Experience - Play - Jetski Activities',
        50211 => 'Experience - Play - Snorkelling',
        51088 => 'Experience - Play - Snorkelling',
        57728 => 'Experience - Play - Snorkelling',
        68919 => 'Experience - Play - Snorkelling',
        77725 => 'Experience - Play - Snorkelling',
        28187 => 'Experience - Play - Surf',
        28729 => 'Experience - Play - Surf',
        33202 => 'Experience - Play - Tours',
        52078 => 'Experience - Play - Tours',
        74075 => 'Experience - Play - Tours',
        70772 => 'Experience - Play - Yoga & Wellness',
        72939 => 'Experience - Play - Yoga & Wellness',
        77296 => 'Experience - Play - Yoga & Wellness',
        77675 => 'Experience - Play - Yoga & Wellness',
        77996 => 'Experience - Play - Yoga & Wellness',
        78872 => 'Experience - Play - Yoga & Wellness',
        78954 => 'Experience - Play - Yoga & Wellness',
        78972 => 'Experience - Play - Yoga & Wellness',
        79021 => 'Experience - Play - Yoga & Wellness',
        56959 => 'Experience - Play - Zipline Ceningan',
    ];

    public function setMedia(array $idToUrl): void
    {
        $this->converter->setMedia($idToUrl);
    }

    public function resolve(array $row): ?array
    {
        $label = self::MAP[(int) ($row['ID'] ?? 0)] ?? null;

        if (!$label) {
            return null;
        }

        [, $type, $area] = array_pad(array_map('trim', explode(' - ', $label, 3)), 3, null);

        return $type ? ['type' => $type, 'area' => $area ?: null] : null;
    }

    public function import(array $row, ?string $imageDir = null, bool $withDescription = true, bool $download = false): Experience
    {
        $target = $this->resolve($row);

        if (!$target) {
            throw new \InvalidArgumentException('Row ' . ($row['ID'] ?? '?') . ' is not an Experience in the migration list.');
        }

        return DB::transaction(function () use ($row, $target, $imageDir, $withDescription, $download) {
            $type = $this->types[$target['type']] ??= ExperienceType::firstOrCreate(['name' => $target['type']]);
            $area = $target['area']
                ? ($this->areas[$type->id . '|' . $target['area']] ??= ExperienceArea::firstOrCreate([
                    'experienceTypeId' => $type->id,
                    'name' => $target['area'],
                ]))
                : null;

            $title = $this->decode($row['Title'] ?? null) ?: 'Untitled';
            $slug = trim($row['Slug'] ?? '') ?: Str::slug($title);

            $seoId = ContentSeo::where('slug', $slug)
                ->where('contentableType', Experience::class)
                ->value('contentableId');

            $experience = $seoId ? Experience::find($seoId) : null;

            $attributes = [
                'experienceTypeId' => $type->id,
                'experienceAreaId' => $area?->id,
                'name' => $title,
                'isActive' => ($row['Status'] ?? null) === 'publish',
            ];

            if ($withDescription) {
                $attributes['description'] = $this->converter->convert($row['Content'] ?? '', $title);
            }

            $imageUrl = trim((string) ($row['Image URL'] ?? ''));
            $thumbnail = $this->thumbnailName($imageUrl);

            if ($experience) {
                $experience->update($attributes);

                if (!$experience->thumbnail && $thumbnail) {
                    $experience->update(['thumbnail' => $thumbnail]);
                }
            } else {
                $experience = Experience::create($attributes + [
                    'thumbnail' => $thumbnail,
                    'showInquiry' => false,
                    'locale' => 'en',
                    'createdAt' => $this->date($row['Date'] ?? null),
                    'updatedAt' => $this->date($row['Post Modified Date'] ?? null),
                ]);
            }

            if ($experience->thumbnail && !$this->placeThumbnail($experience->thumbnail, $imageUrl, $imageDir, $download)) {
                $this->missingThumbnails[] = $experience->thumbnail;
            }

            $this->syncSeo($experience, $slug, $title, $row);

            return $experience;
        });
    }

    private function syncSeo(Experience $experience, string $slug, string $title, array $row): void
    {
        $seoTitle = trim($row['_yoast_wpseo_title'] ?? '');

        $experience->seo()->updateOrCreate(
            [
                'contentableId' => $experience->id,
                'contentableType' => Experience::class,
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

    private function thumbnailName(?string $imageUrl): ?string
    {
        $path = parse_url(trim((string) $imageUrl), PHP_URL_PATH);

        return $path ? (rawurldecode(basename($path)) ?: null) : null;
    }

    private function placeThumbnail(string $filename, string $imageUrl, ?string $imageDir, bool $download): bool
    {
        $dirPath = PathConstant::IMAGES_EXPERIENCE_STORAGE_PUBLIC_PATH();

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
