<?php

namespace App\Parser\Language;

use Logia\Core\Parser\BaseParser;

class LanguageParser extends BaseParser
{

    /**
     * @param $data
     *
     * @return array|null
     */
    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        return $data->only('id', 'code', 'country', 'main') + [
            'image' => $data->imageUrl()
        ];
    }

    /**
     * @param $data
     *
     * @return array|null
     */
    public static function brief($data)
    {
        if (!$data) {
            return null;
        }

        return $data->only('id', 'code', 'country', 'main') + [
            'image' => $data->imageUrl()
        ];
    }
}
