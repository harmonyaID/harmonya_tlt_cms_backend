<?php

namespace App\Parser\Acf;

class AcfParser
{
    /**
     * @param \Illuminate\Support\Collection|iterable $acfEntries
     *
     * @return array
     */
    public static function forContent($acfEntries): array
    {
        if (!$acfEntries) {
            return [];
        }

        $result = [];
        foreach ($acfEntries as $entry) {
            $result[] = [
                'id' => $entry->id,
                'key' => $entry->key,
                'label' => $entry->label,
                'value' => $entry->value,
                'order' => $entry->order,
            ];
        }

        return $result;
    }
}
