<?php

namespace App\Models\Traits;

trait HasFeatureValue
{
    /**
     * Value of a feature (e.g. "Bedroom", "Bathroom") as a number, null when not set.
     * Uses the "features" relation.
     */
    public function featureValue(string $name): int|float|null
    {
        $value = $this->features->firstWhere('name', $name)?->pivot?->value;

        return is_numeric($value) ? $value + 0 : null;
    }
}
