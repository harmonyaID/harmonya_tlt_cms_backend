<?php

namespace App\Services\Property;

/**
 * Turns a free-text address into one of the known areas of the islands.
 *
 * Guesty addresses are written in many ways ("Jungut Batu", "Jl. Jungutbatu",
 * "8FG2+PX6, Jungutbatu, ..."), so the area is found by keyword instead of
 * taking a part of the address as is. Null when no known area is mentioned.
 */
class PropertyAreaResolver
{
    /** Area name => regex matched against the address (case insensitive). */
    public const AREAS = [
        'Jungutbatu' => '/jungut\s*batu/i',
        'Mushroom Bay' => '/mushroom/i',
        'Sandy Bay' => '/sandy\s*bay|dream\s*beach/i',
        'Tamarind Bay' => '/tamarind/i',
        'Nusa Ceningan' => '/ceningan/i',
    ];

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
}
