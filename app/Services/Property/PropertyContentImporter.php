<?php

namespace App\Services\Property;

use App\Models\Property\Property;
use App\Models\Property\PropertyDescription;
use App\Models\Property\PropertyTag;
use App\Models\SEO\ContentSeo;
use App\Models\Setting\SettingAmenity;
use App\Models\Setting\SettingPropertyFeature;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Applies a parsed property content document (PropertyContentDocParser) to the
 * Property with the same name: description, why you'll love this, SEO, tags
 * (collection), amenities, bedroom / bathroom / guest features and the resort
 * map as floorplan.
 *
 * Tags and amenities are mapped to the existing master data (case, punctuation,
 * plural and the aliases below); only names with no match are created.
 */
class PropertyContentImporter
{
    /** Document collection => existing tag name. */
    private const TAG_ALIASES = [
        'close to surf' => 'Close to the surf',
        'close to surf break' => 'Close to the surf',
        'couple' => 'Couples',
    ];

    /** Document amenity => amenity name (existing, or the one name used when it is created). */
    private const AMENITY_ALIASES = [
        'ocean view' => 'Sea view',
        'wi fi' => 'Wifi',
        'safety boxe' => 'Safe',
        'safety box' => 'Safe',
        'breakfast daily' => 'Breakfast',
        'daily breakfast' => 'Breakfast',
        'breakfast included' => 'Breakfast',
        'air conditioned' => 'Air conditioning',
        'air conditioned bedroom' => 'Air conditioning',
        'cot' => 'Crib',
        'cot available' => 'Crib',
        'kitchen facilitie' => 'Kitchen',
        'shared swimming pool' => 'Communal pool',
        '15m pool' => 'Pool',
        '2 pool' => 'Pool',
        '2 resort pool' => 'Pool',
        '3 resort pool' => 'Pool',
        'fridge in room' => 'Mini fridge',
        'tv in room' => 'Tv',
        'hot cold water' => 'Hot water',
        'beachfront' => 'Beach Front',
        'beachfront location' => 'Beach Front',
        'daily house keeping' => 'Daily housekeeping',
        'extra bed' => 'Extra beds available',
        'extra bed available' => 'Extra beds available',
        'king bed' => 'King size beds',
        'king sized bed' => 'King size beds',
        'king size bed' => 'King size beds',
        'king queen bed' => 'King size beds',
        'twin bedding' => 'Twin bedding option',
        'twin option' => 'Twin bedding option',
        'on site restaurant' => 'Restaurant',
        'on site bar lounge' => 'Bar / lounge',
    ];

    /** Lines in "Amenities" that describe the rooms, not an amenity. */
    private const AMENITY_IGNORED = ['2 bedroom', '2 bathroom', '1 bedroom glamping tent'];

    /** Feature master names for the "Features" lines. */
    private const FEATURES = ['bedroom' => 'Bedroom', 'bathroom' => 'Bathroom', 'guest' => 'Guest'];

    private ?Collection $properties = null;
    private ?array $tags = null;
    private ?array $amenities = null;
    private ?array $features = null;

    /** Names created (or that would be created on a dry run). */
    public array $createdTags = [];
    public array $createdAmenities = [];

    public function __construct(private bool $dryRun = false, private bool $onlyEmpty = false)
    {
    }

    /**
     * The Property with the same name as the document: exact name first, then
     * the name without words like villa / resort / the (only when it is unique).
     *
     * @return array{0: ?Property, 1: string} [property, reason when not found]
     */
    public function findProperty(string $title): array
    {
        $this->properties ??= Property::query()->get(['id', 'nickname', 'floorplanImage', 'occupancy', 'whyYoullLoveThis']);

        $exact = $this->properties->filter(fn (Property $p) => $this->exactKey($p->nickname) === $this->exactKey($title));

        if ($exact->count() === 1) {
            return [$exact->first(), ''];
        }

        $loose = $this->properties->filter(fn (Property $p) => $this->looseKey($p->nickname) === $this->looseKey($title));

        return match (true) {
            $exact->count() > 1 => [null, 'more than one Property named ' . $title . ': #' . $exact->pluck('id')->implode(', #')],
            $loose->count() === 1 => [$loose->first(), ''],
            $loose->count() > 1 => [null, 'more than one similar Property: ' . $loose->map(fn ($p) => "#{$p->id} {$p->nickname}")->implode(', ')],
            default => [null, 'no Property with this name'],
        };
    }

    /**
     * @return array<string,string> what was (or would be) changed, by field
     */
    public function apply(Property $property, array $doc, array $images = []): array
    {
        $changes = [];

        $run = function () use ($property, $doc, $images, &$changes) {
            $changes['description'] = $this->description($property, $doc);
            $changes['why'] = $this->why($property, $doc);
            $changes['seo'] = $this->seo($property, $doc);
            $changes['tags'] = $this->tags($property, $doc['collection']);
            $changes['amenities'] = $this->amenities($property, $doc['amenities']);
            $changes['features'] = $this->features($property, $doc['features']);
            $changes['floorplan'] = $this->floorplan($property, $images);
        };

        $this->dryRun ? $run() : DB::transaction($run);

        return array_filter($changes);
    }

    private function description(Property $property, array $doc): ?string
    {
        $html = $this->descriptionHtml($doc['description']);

        if ($html === '') {
            return null;
        }

        $description = PropertyDescription::where('propertyId', $property->id)
            ->orderByRaw("channel = 'primary' desc")
            ->orderBy('id')
            ->first();

        if ($this->onlyEmpty && trim(strip_tags((string) $description?->summary)) !== '') {
            return null;
        }

        if (!$this->dryRun) {
            $description
                ? $description->update(['summary' => $html])
                : PropertyDescription::create([
                    'propertyId' => $property->id,
                    'channel' => 'primary',
                    'language' => 'en',
                    'title' => $property->nickname,
                    'summary' => $html,
                ]);
        }

        return count($doc['description']) . ' paragraph(s)';
    }

    /** Paragraphs as <p>, consecutive list paragraphs as one <ul>. */
    private function descriptionHtml(array $paragraphs): string
    {
        $html = '';
        $list = [];

        $flush = function () use (&$html, &$list) {
            if ($list) {
                $html .= '<ul>' . implode('', array_map(fn ($item) => '<li>' . e($item) . '</li>', $list)) . '</ul>';
                $list = [];
            }
        };

        foreach ($paragraphs as $paragraph) {
            if ($paragraph['list']) {
                $list[] = $paragraph['text'];
                continue;
            }

            $flush();
            $html .= '<p>' . e($paragraph['text']) . '</p>';
        }

        $flush();

        return $html;
    }

    private function why(Property $property, array $doc): ?string
    {
        $items = collect($doc['why'])
            ->filter(fn ($item) => $item['title'])
            ->map(fn ($item) => ['title' => Str::limit($item['title'], 255, ''), 'value' => $item['value'] ?: null])
            ->values()
            ->all();

        if (!$items || ($this->onlyEmpty && !empty($property->whyYoullLoveThis))) {
            return null;
        }

        if (!$this->dryRun) {
            $property->update(['whyYoullLoveThis' => $items]);
        }

        return count($items) . ' item(s)';
    }

    private function seo(Property $property, array $doc): ?string
    {
        $data = array_filter(['title' => $doc['metaTitle'], 'description' => $doc['metaDesc']]);

        if (!$data) {
            return null;
        }

        $seo = $property->seo()->first();

        if ($seo && $this->onlyEmpty) {
            $data = array_filter($data, fn ($value, $field) => blank($seo->{$field}), ARRAY_FILTER_USE_BOTH);

            if (!$data) {
                return null;
            }
        }

        if (!$this->dryRun) {
            $seo
                ? $seo->update($data)
                : $property->seo()->create($data + [
                    'slug' => $this->uniqueSlug(Str::slug($property->nickname) ?: 'property-' . $property->id),
                    'robotIndex' => true,
                    'robotFollow' => true,
                ]);
        }

        return implode(' + ', array_keys($data));
    }

    private function tags(Property $property, array $names): ?string
    {
        if ($this->tags === null) {
            $this->tags = [];
            foreach (PropertyTag::orderBy('id')->get(['id', 'name']) as $tag) {
                $this->tags[$this->key($tag->name)] ??= $tag->id;
            }
        }

        $ids = [];

        foreach ($names as $name) {
            $name = self::TAG_ALIASES[$this->key($name)] ?? $name;
            $key = $this->key($name);

            if (!isset($this->tags[$key])) {
                $this->createdTags[$key] = $name;
                $this->tags[$key] = $this->dryRun ? 0 : PropertyTag::create(['name' => $name])->id;
            }

            $ids[] = $this->tags[$key];
        }

        return $this->attach($property, 'tags', $ids, count($names));
    }

    private function amenities(Property $property, array $names): ?string
    {
        if ($this->amenities === null) {
            $this->amenities = [];
            foreach (SettingAmenity::orderBy('id')->get(['id', 'name']) as $amenity) {
                $this->amenities[$this->key($amenity->name)] ??= $amenity->id;
            }
        }

        $ids = [];

        foreach ($names as $name) {
            if (in_array($this->key($name), self::AMENITY_IGNORED, true)) {
                continue;
            }

            $name = self::AMENITY_ALIASES[$this->key($name)] ?? $name;
            $key = $this->key($name);

            if (!isset($this->amenities[$key])) {
                $name = Str::ucfirst($name);
                $this->createdAmenities[$key] = $name;
                $this->amenities[$key] = $this->dryRun ? 0 : SettingAmenity::create(['name' => $name, 'isPublish' => true])->id;
            }

            $ids[] = $this->amenities[$key];
        }

        return $this->attach($property, 'amenities', $ids, count($ids));
    }

    /** Adds the ids, keeping what the Property already has. */
    private function attach(Property $property, string $relation, array $ids, int $count): ?string
    {
        $ids = array_values(array_unique($ids));

        if (!$ids) {
            return null;
        }

        if ($this->dryRun) {
            $existing = $property->{$relation}()->pluck($property->{$relation}()->getRelated()->getTable() . '.id')->all();
            $new = count(array_diff($ids, $existing)) ;
        } else {
            $new = count($property->{$relation}()->syncWithoutDetaching($ids)['attached']);
        }

        return count($ids) . " (+{$new} new on this property)";
    }

    /** "4 bedroom", "2 & 3 bathroom", "Up to 4 & 8 guests" => Bedroom / Bathroom / Guest values + occupancy. */
    private function features(Property $property, array $lines): ?string
    {
        if ($this->features === null) {
            $this->features = [];
            foreach (SettingPropertyFeature::get(['id', 'name']) as $feature) {
                $this->features[strtolower($feature->name)] ??= $feature->id;
            }
        }

        $values = [];

        foreach ($lines as $line) {
            if (preg_match('/^(?:up to\s+)?([\d\s,&and]+?)\s*(bedroom|bathroom|guest)s?$/i', trim($line), $m)) {
                $values[self::FEATURES[strtolower($m[2])]] = $this->numbers($m[1]);
            }
        }

        $sync = [];
        $existing = $property->features()->get()->keyBy('id');

        foreach ($values as $name => $value) {
            $id = $this->features[strtolower($name)] ?? null;

            if ($id && !($this->onlyEmpty && filled($existing->get($id)?->pivot?->value))) {
                $sync[$id] = ['value' => $value];
            }
        }

        $occupancy = isset($values['Guest']) ? max(array_map('intval', preg_split('/\D+/', $values['Guest'], -1, PREG_SPLIT_NO_EMPTY))) : null;
        $setOccupancy = $occupancy && !($this->onlyEmpty && $property->occupancy);

        if (!$sync && !$setOccupancy) {
            return null;
        }

        if (!$this->dryRun) {
            $property->features()->syncWithoutDetaching($sync);

            if ($setOccupancy) {
                $property->update(['occupancy' => $occupancy]);
            }
        }

        return collect($values)->map(fn ($value, $name) => "{$name} {$value}")->implode(', ')
            . ($setOccupancy ? ", occupancy {$occupancy}" : '');
    }

    /** "2,3 and 4" => "2, 3 & 4" */
    private function numbers(string $text): string
    {
        $numbers = preg_split('/\D+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $last = array_pop($numbers);

        return $numbers ? implode(', ', $numbers) . ' & ' . $last : (string) $last;
    }

    /** The resort map / site plan image as the floorplan. */
    private function floorplan(Property $property, array $images): ?string
    {
        $map = collect($images)->first(fn ($path) => preg_match('/map|site\s*plan|siteplan/i', basename($path)));

        if (!$map || ($this->onlyEmpty && $property->floorplanImage)) {
            return null;
        }

        if (!$this->dryRun) {
            $dir = PathConstant::IMAGES_PROPERTY_FLOORPLAN_STORAGE_PUBLIC_PATH();
            File::ensureDirectoryExists($dir);

            $filename = Str::random(20) . '-' . Str::slug($property->nickname) . '.' . strtolower(pathinfo($map, PATHINFO_EXTENSION));
            File::copy($map, $dir . $filename);

            $old = $property->floorplanImage;
            $property->update(['floorplanImage' => $filename]);

            if ($old && $old !== $filename) {
                File::delete($dir . $old);
            }
        }

        return basename($map);
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

    /** Lower case words, without punctuation / "&" / "and", plural "s" removed. */
    private function key(string $name): string
    {
        $words = preg_split('/[^a-z0-9]+/', strtolower(html_entity_decode($name)), -1, PREG_SPLIT_NO_EMPTY);
        $words = array_diff($words, ['and']);
        $words = array_map(fn ($word) => strlen($word) > 3 ? rtrim($word, 's') : $word, $words);

        return implode(' ', $words);
    }

    private function exactKey(?string $name): string
    {
        return preg_replace('/[^a-z0-9]+/', '', strtolower(html_entity_decode((string) $name)));
    }

    private function looseKey(?string $name): string
    {
        $words = preg_split('/[^a-z0-9]+/', strtolower(html_entity_decode((string) $name)), -1, PREG_SPLIT_NO_EMPTY);
        $words = array_unique(array_diff($words, ['villa', 'villas', 'resort', 'resorts', 'the', 'and', 'lembongan', 'nusa', 'private']));
        sort($words);

        return implode(' ', $words);
    }
}
