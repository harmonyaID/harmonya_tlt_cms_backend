<?php

namespace App\Services\Constant;

use Illuminate\Support\Str;

class BaseIDName
{
    private const CAPITALS = [
        'NPWP',
        'NPWPD',
        'KTP',
        'PBB',
    ];

    const UNKNOWN = 'unknown';

    const OPTION = [
        0 => self::UNKNOWN
    ];


    /** --- FUNCTIONS --- */

    /**
     * @param array|null $options
     *
     * @return array
     */
    public static function get(array|null $options = null): array
    {
        if ($options) {
            return collect($options)->map(function ($option) {
                return ['id' => $option, 'name' => static::display($option)];
            })->toArray();
        }

        $data = [];
        foreach (static::OPTION as $key => $value) {
            $data[] = ['id' => $key, 'name' => $value];
        }

        return $data;
    }

    /**
     * @param int|null $id
     *
     * @return string
     */
    public static function display(int|null $id = null): string
    {
        if (isset(static::OPTION[$id])) {
            return static::OPTION[$id];
        }

        return self::UNKNOWN;
    }

    /**
     * @param int|null $id
     *
     * @return array|null
     */
    public static function idName(int|null $id = null): array|null
    {
        if (!$id) {
            return null;
        }

        return ['id' => $id, 'name' => static::display($id)];
    }

        /**
     * @param $const
     * @param $transKey
     *
     * @return mixed|string
     */
    public static function customDisplay($const, $transKey = null)
    {
        if ($transKey) {
            $translations = translations($transKey);
            if (count($translations) > 0) {

                if (array_key_exists(locale(), $translations)) {
                    $value = $translations[locale()];
                    if ($value) {
                        return $value;
                    }
                }

            }
        }

        $const = str_replace("_", " ", $const);
        return self::identify(Str::title($const));
    }

    

    /**
     * @param $text
     *
     * @return string
     */
    public static function identify($text)
    {
        foreach (self::CAPITALS as $CAPITAL) {

            $capitalTitle = Str::title($CAPITAL);
            if (stripos($text, $capitalTitle) !== false) {
                $text = str_replace($capitalTitle, $CAPITAL, $text);
            }

        }

        return $text;
    }

}
