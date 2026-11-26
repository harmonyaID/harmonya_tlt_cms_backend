<?php

namespace App\Services\Property;

use App\Models\Property\PropertyArea;
use Illuminate\Support\Collection;

/**
 * Finds the area (Setting > Property Area) a free-text address is in.
 *
 * Guesty addresses are written in many ways ("Jungut Batu", "Jl. Jungutbatu",
 * "8FG2+PX6, Jungutbatu, ..."), so the area is found by keyword instead of
 * taking a part of the address as is. Null when no known area is mentioned.
 */
class PropertyAreaResolver
{
    /** Default areas => regex matched against the address (case insensitive). */
    public const AREAS = [
        'Jungutbatu' => '/jungut\s*batu/i',
        'Mushroom Bay' => '/mushroom/i',
        'Sandy Bay' => '/sandy\s*bay|dream\s*beach/i',
        'Tamarind Bay' => '/tamarind/i',
        'Nusa Ceningan' => '/ceningan/i',
    ];

    private ?Collection $areas = null;

    /** Name of the default area mentioned in the texts. */
    public function resolve(?string ...$texts): ?string
    {
        $text = implode(' ', array_filter($texts));

        foreach (self::AREAS as $area => $pattern) {
            if (preg_match($pattern, $text)) {
                return $area;
            }
        }

        return null;
    }

    /**
     * Id of the Property Area mentioned in the texts: a default area by keyword,
     * otherwise any area (also ones added in the admin) whose name is in the text.
     */
    public function resolveId(?string ...$texts): ?int
    {
        $this->areas ??= PropertyArea::get(['id', 'name']);
        $text = implode(' ', array_filter($texts));

        if ($name = $this->resolve($text)) {
            $area = $this->areas->first(fn ($area) => strcasecmp($area->name, $name) === 0);

            if ($area) {
                return $area->id;
            }
        }

        $key = fn (string $value) => preg_replace('/[^a-z0-9]+/', '', strtolower($value));
        $haystack = $key($text);

        $area = $this->areas
            ->filter(fn ($area) => $key($area->name) !== '' && str_contains($haystack, $key($area->name)))
            ->sortByDesc(fn ($area) => strlen($area->name))
            ->first();

        return $area?->id;
    }
}
