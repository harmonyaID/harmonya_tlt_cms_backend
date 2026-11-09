<?php

namespace App\Services\Constant;

use Illuminate\Support\Str;

class BaseCodeName
{
    const OPTION = [];

    private const CAPITALS = [];

    public static function get(array|null $options = null): array
    {
        if ($options) {
            return collect($options)->map(function ($option) {
                return ['code' => $option, 'name' => static::display($option)];
            })->toArray();
        }

        $data = [];
        foreach (static::OPTION as $value) {
            $data[] = ['code' => $value, 'name' => static::display($value)];
        }

        return $data;
    }

    public static function display(string $value): string
    {
        $value = str_replace("_", " ", $value);
        $value = Str::title($value);

        foreach (self::CAPITALS as $CAPITAL) {
            $capitalTitle = Str::title($CAPITAL);
            if (stripos($value, $capitalTitle) !== false) {
                $value = str_replace($capitalTitle, $CAPITAL, $value);
            }
        }

        return $value;
    }

    public static function codeName(string|null $code = null): array|null
    {
        if (!$code) {
            return null;
        }

        return ['code' => $code, 'name' => static::display($code)];
    }

    public static function toCamelCase(string $value): string
    {
        return Str::camel($value);
    }
}
