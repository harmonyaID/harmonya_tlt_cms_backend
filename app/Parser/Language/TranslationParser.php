<?php

namespace App\Parser\Language;

use App\Misc\Model\ModelBaseParser;
use App\Models\Language\Language;
use Logia\Core\Parser\BaseParser;

class TranslationParser extends BaseParser
{
    public static $keyName = 'translation';

    /**
     * @var array
     */
    protected static $languages;


    /**
     * @inheritDoc
     */
    public static function get($data)
    {
        self::$languages = Language::all();

        $results = [];
        foreach ($data as $translation) {
            $results[] = self::first($translation);
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

        self::$languages = self::$languages ?: Language::all();

        $translations = [];
        foreach (self::$languages as $language) {
            $translations[$language->code] = isset($data->translations[$language->code]) ? $data->translations[$language->code] ?: null : null;
        }

        return [
            'id' => $data->id,
            'key' => $data->key,
            'translated' => $data->translated,
            'translations' => $translations,
        ];
    }
}
