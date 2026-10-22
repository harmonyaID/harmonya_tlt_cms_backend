<?php

namespace App\Services\WordPress;

use App\Models\Experience\Experience;
use App\Models\Experience\ExperienceArea;
use App\Models\Experience\ExperienceType;
use App\Models\SEO\ContentSeo;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Maps one raw row of the WordPress "Select Portfolio Export" into an
 * Experience - mirrors the role GuestyPropertyImporter plays for Guesty: one
 * public import(array $row), everything else private.
 *
 * Which Type / Area a row belongs to comes from the "Type" column of the TLT
 * Migration List (MAP below, keyed by WordPress post ID):
 *
 *   "Experience - Eat - Jungutbatu"
 *        |           |       '-> Experience Area  (firstOrCreate, under that Type)
 *        |           '---------> Experience Type  (firstOrCreate)
 *        '---------------------> saved as an Experience
 *
 * The description keeps the look of the old page: WPBakery rows / columns /
 * buttons are converted to plain HTML by WordPressContentConverter.
 *
 * Rows that are not labelled "Experience - ..." (Stay, Island Guide, Boat,
 * Community ...) are not handled here - resolve() returns null for them.
 *
 * Safe to re-run: an existing Experience is matched by its WordPress slug
 * (kept as its SEO slug) and updated in place instead of duplicated.
 */
class WordPressExperienceImporter
{
    /** Thumbnails that were not found in the image folder (file names). */
    public array $missingThumbnails = [];

    private array $types = [];
    private array $areas = [];

    public function __construct(private WordPressContentConverter $converter)
    {
    }

    /**
     * WordPress post ID => "Type" column of the TLT Migration List.
     */
    private const MAP = [
        23455 => 'Experience - Eat - Jungutbatu', // BALI ECO DELI
        30373 => 'Experience - Eat - Jungutbatu', // GINGER & JAMU
        32055 => 'Experience - Eat - Jungutbatu', // OHANA'S BEACH LOUNGE
        48063 => 'Experience - Eat - Jungutbatu', // KOA DONUTS
        49361 => 'Experience - Eat - Jungutbatu', // WHITE CORNER COFFEE SHOP
        52038 => 'Experience - Eat - Jungutbatu', // LA BIANCA NUSA LEMBONGAN
        54140 => 'Experience - Eat - Jungutbatu', // THE PLAYFUL TABLE BY MORIN
        54311 => 'Experience - Eat - Jungutbatu', // TIGERLILLYS BEACH SHACK
        58752 => 'Experience - Eat - Jungutbatu', // TIGERLILLY'S CAFE
        61539 => 'Experience - Eat - Jungutbatu', // THE SAMPAN RESTAURANT
        61695 => 'Experience - Eat - Jungutbatu', // NISI
        62344 => 'Experience - Eat - Jungutbatu', // KAYU LEMBONGAN
        65129 => 'Experience - Eat - Jungutbatu', // WARUNG CASSAVA
        66620 => 'Experience - Eat - Jungutbatu', // NYOMAN'S WARUNG
        69066 => 'Experience - Eat - Jungutbatu', // 3 MONKEYS BEACH CLUB
        70826 => 'Experience - Eat - Jungutbatu', // BALIWOOD SOCIAL CLUB
        71328 => 'Experience - Eat - Jungutbatu', // BAR RIVA
        71362 => 'Experience - Eat - Jungutbatu', // THE GENERAL
        71893 => 'Experience - Eat - Jungutbatu', // AMARA
        74514 => 'Experience - Eat - Jungutbatu', // MILO & OLIVER
        74722 => 'Experience - Eat - Jungutbatu', // AGHA LEMBONGAN HOME SERVICE MASSAGE
        75048 => 'Experience - Eat - Jungutbatu', // WAVE ISLAND COFFEE & EATERY
        75641 => 'Experience - Eat - Jungutbatu', // TIKI BAR & MORE
        75972 => 'Experience - Eat - Jungutbatu', // TIGA CEMARA CAFE
        78148 => 'Experience - Eat - Jungutbatu', // BARUNA WARUNG
        78680 => 'Experience - Eat - Jungutbatu', // KALM KITCHEN
        78940 => 'Experience - Eat - Jungutbatu', // DAWN by Lago
        78947 => 'Experience - Eat - Jungutbatu', // DUSK by Lago
        78961 => 'Experience - Eat - Jungutbatu', // PONTOON by Lago
        79139 => 'Experience - Eat - Jungutbatu', // MAMATI MOROCCAN RESTAURANT
        23701 => 'Experience - Eat - Jungutbatu', // TIGERLILLYS RESTAURANT
        23467 => 'Experience - Eat - Mushroom and Sandy Bay', // SANDY BAY BEACH CLUB
        23525 => 'Experience - Eat - Mushroom and Sandy Bay', // HAI BAR & GRILL
        33848 => 'Experience - Eat - Mushroom and Sandy Bay', // HAI RI ZEN
        51636 => 'Experience - Eat - Mushroom and Sandy Bay', // ALPONTE RISTORANTE
        67119 => 'Experience - Eat - Mushroom and Sandy Bay', // TALES BOOKCAF
        77519 => 'Experience - Eat - Mushroom and Sandy Bay', // CHEZ NYOMAN LEMBONGAN RESTAURANT
        30768 => 'Experience - Eat - Nusa Ceningan', // SEA BREEZE CENINGAN
        32059 => 'Experience - Eat - Nusa Ceningan', // NEXT LEVEL CAFE & BAR
        41401 => 'Experience - Eat - Nusa Ceningan', // THE PALMS RESTAURANT
        71601 => 'Experience - Eat - Nusa Ceningan', // SUKU BEACH CLUB
        78412 => 'Experience - Eat - Nusa Ceningan', // AURA CENINGAN RESTO
        25818 => 'Experience - Play - Beach Club', // BALI HAI CRUISES BEACH CLUB & PONTOON
        78802 => 'Experience - Play - Cowork', // KALM COWORK LOUNGE
        51720 => 'Experience - Play - Day Spa', // SPRING SPA & SANCTUARY NUSA LEMBONGAN
        60016 => 'Experience - Play - Day Spa', // JAMU DAY SPA
        71185 => 'Experience - Play - Day Spa', // UMI NAIL BAR
        73668 => 'Experience - Play - Day Spa', // AQUAM SPA & SEAWEED BATHS
        74033 => 'Experience - Play - Day Spa', // ROSIE'S NAIL BAR
        75526 => 'Experience - Play - Day Spa', // LEMBONGAN SURF & ORGANIC SPA
        77758 => 'Experience - Play - Day Spa', // MANTRA HOME SERVICE SPA LEMBONGAN
        78437 => 'Experience - Play - Day Spa', // ALOEVERA SALON & SPA
        23662 => 'Experience - Play - Day Spa', // GLO DAY SPA
        70789 => 'Experience - Play - Day Spa', // KARUNA ISLAND SPA
        23594 => 'Experience - Play - Dive', // BALI HAI DIVING ADVENTURES
        50030 => 'Experience - Play - Dive', // SCUBA CENTER ASIA LEMBONGAN
        51157 => 'Experience - Play - Dive', // BALI DIVING ACADEMY
        76048 => 'Experience - Play - Dive', // SOBER DIVING CENTRE
        58263 => 'Experience - Play - Jetski Activities', // JETSKI LEMBONGAN ADVENTURE
        50211 => 'Experience - Play - Snorkelling', // OCEAN ADVENTURES BY MORIN
        51088 => 'Experience - Play - Snorkelling', // NEMO ACTIVITIES LEMBONGAN
        57728 => 'Experience - Play - Snorkelling', // CAPTAIN YIN YANG
        68919 => 'Experience - Play - Snorkelling', // UTOPIA CATAMARAN
        77725 => 'Experience - Play - Snorkelling', // KALM CHARTERS
        28187 => 'Experience - Play - Surf', // LEMBONGAN SURF LESSONS
        28729 => 'Experience - Play - Surf', // NUSA ISLANDS SURF SCHOOL
        33202 => 'Experience - Play - Tours', // LEMOS PENIDA TOURS
        52078 => 'Experience - Play - Tours', // Buggy Hire Scooter Rentals & Activities  MADE
        74075 => 'Experience - Play - Tours', // ISLAND DISCOVERY CRUISE
        70772 => 'Experience - Play - Yoga & Wellness', // K GYM LEMBONGAN
        72939 => 'Experience - Play - Yoga & Wellness', // ALL YOGA TRAINING
        77296 => 'Experience - Play - Yoga & Wellness', // SHIZARU NUSA LEMBONGAN
        77675 => 'Experience - Play - Yoga & Wellness', // ISLAND YOGA & SOUND HEALING LEMBONGAN
        77996 => 'Experience - Play - Yoga & Wellness', // LEMBONGAN PADEL CLUB
        78872 => 'Experience - Play - Yoga & Wellness', // JUNGLE PADEL LEMBONGAN
        78954 => 'Experience - Play - Yoga & Wellness', // SUNDECK by Lago
        78972 => 'Experience - Play - Yoga & Wellness', // FIRM by Lago
        79021 => 'Experience - Play - Yoga & Wellness', // KALM MOVEMENT SHALA
        56959 => 'Experience - Play - Zipline Ceningan', // ZIPLINE CENINGAN
    ];

    /** @param array<int|string,string> $idToUrl WordPress media ID => URL, for images inside descriptions */
    public function setMedia(array $idToUrl): void
    {
        $this->converter->setMedia($idToUrl);
    }

    /**
     * ['type' => 'Eat', 'area' => 'Jungutbatu'] for an Experience row,
     * null for every other row of the export.
     */
    public function resolve(array $row): ?array
    {
        $label = self::MAP[(int) ($row['ID'] ?? 0)] ?? null;

        if (!$label) {
            return null;
        }

        [, $type, $area] = array_pad(array_map('trim', explode(' - ', $label, 3)), 3, null);

        return $type ? ['type' => $type, 'area' => $area ?: null] : null;
    }

    public function import(array $row, ?string $imageDir = null, bool $withDescription = true): Experience
    {
        $target = $this->resolve($row);

        if (!$target) {
            throw new \InvalidArgumentException('Row ' . ($row['ID'] ?? '?') . ' is not an Experience in the migration list.');
        }

        return DB::transaction(function () use ($row, $target, $imageDir, $withDescription) {
            $type = $this->types[$target['type']] ??= ExperienceType::firstOrCreate(['name' => $target['type']]);
            $area = $target['area']
                ? ($this->areas[$type->id . '|' . $target['area']] ??= ExperienceArea::firstOrCreate([
                    'experienceTypeId' => $type->id,
                    'name' => $target['area'],
                ]))
                : null;

            $title = $this->decode($row['Title'] ?? null) ?: 'Untitled';
            $slug = trim($row['Slug'] ?? '') ?: Str::slug($title);

            // ContentSeo::contentable() can't be used here (its morphTo() looks for
            // snake_case columns), so the owner is looked up by contentableId.
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

            // --without-description: leave the description empty (and untouched on re-runs).
            if ($withDescription) {
                $attributes['description'] = $this->converter->convert($row['Content'] ?? '', $title);
            }

            if ($experience) {
                $experience->update($attributes);
            } else {
                $thumbnail = $this->thumbnailName($row['Image URL'] ?? null);

                $experience = Experience::create($attributes + [
                    'thumbnail' => $thumbnail,
                    'showInquiry' => false,
                    'locale' => 'en',
                    'createdAt' => $this->date($row['Date'] ?? null),
                    'updatedAt' => $this->date($row['Post Modified Date'] ?? null),
                ]);

                if ($thumbnail && $imageDir && !$this->copyThumbnail($thumbnail, $imageDir)) {
                    $this->missingThumbnails[] = $thumbnail;
                }
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

    /**
     * File name of the image, taken from its WordPress URL
     * (.../2018/11/Ohanas-landing-page-pic.jpg => Ohanas-landing-page-pic.jpg).
     */
    private function thumbnailName(?string $imageUrl): ?string
    {
        $path = parse_url(trim((string) $imageUrl), PHP_URL_PATH);

        return $path ? (rawurldecode(basename($path)) ?: null) : null;
    }

    /**
     * Copies <imageDir>/<file> into the Experience image folder. Returns false
     * when the file is not in the image folder (yet).
     */
    private function copyThumbnail(string $filename, string $imageDir): bool
    {
        $source = rtrim($imageDir, '/\\') . DIRECTORY_SEPARATOR . $filename;

        if (!is_file($source)) {
            return false;
        }

        $dirPath = PathConstant::IMAGES_EXPERIENCE_STORAGE_PUBLIC_PATH();

        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        if (!is_file($dirPath . $filename)) {
            copy($source, $dirPath . $filename);
        }

        return true;
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
