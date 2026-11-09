<?php

namespace App\Services\Number;

use App\Services\Number\Contract\NumberGenerator;
use Illuminate\Database\Eloquent\Model;

class BaseNumber implements NumberGenerator
{
    /**
     * @var string
     */
    protected static string $prefix = "GX";

    /**
     * @var Model|string|null
     */
    protected Model|string|null $model = null;

    public static function generate(): string
    {
        $static = new static();

        $date = now();
        $number = $date->format('myd');

        if (!$static->model) {
            $number .= $date->format('Hi');
        } else {
            $increment = static::getIncrementNumber();
            $number .= str_pad($increment, 4, '0', STR_PAD_LEFT);
        }

        $number .= rand(111, 999);

        return strtoupper(static::$prefix . $number);
    }

    protected static function getIncrementNumber(bool $checkMonth = true): int
    {
        try {
            return (new static())->model::withTrashed()
                    ->where(function ($query) use ($checkMonth) {
                        if ($checkMonth) {
                            $date = now();

                            $query->whereMonth('createdAt', $date->month)
                                ->whereYear('createdAt', $date->year);
                        }
                    })->count() + 1;
        } catch (\Exception $exception) {
            exception($exception);
        }
    }
}
