<?php

namespace App\Parser\Component;

use Logia\Core\Parser\BaseParser;

class ComponentParser extends BaseParser
{
    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        return parent::first($data);
    }
}
