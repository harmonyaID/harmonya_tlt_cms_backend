<?php

namespace App\Parser\Language;

use Logia\Core\Parser\BaseParser;

class LanguageGroupParser extends BaseParser
{
    public static $keyName = 'group';

    /**
     * @inheritDoc
     */
    public static function get($data)
    {
        $results = [];
        foreach ($data as $group) {
            $results[] = self::first($group);
        }

        return $results;
    }

    /**
     * @inheritDoc
     */
    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        return $data->only('id', 'path');
    }
}
