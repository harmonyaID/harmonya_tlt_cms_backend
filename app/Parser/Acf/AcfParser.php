<?php

namespace App\Parser\Acf;

class AcfParser
{
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
