<?php

namespace App\Parser\TltTestimonial;

use Logia\Core\Parser\BaseParser;

class TltTestimonialParser extends BaseParser
{
    public static function first($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'name' => $data->name,
            'position' => $data->position,
            'company' => $data->company,
            'testimonial' => $data->testimonial,
            'photo' => $data->photoUrl(),
            'order' => $data->order,
            'isActive' => $data->isActive,
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }

    public static function brief($data)
    {
        if (!$data) {
            return null;
        }

        return [
            'id' => $data->id,
            'name' => $data->name,
            'position' => $data->position,
            'company' => $data->company,
            'testimonial' => $data->testimonial,
            'photo' => $data->photoUrl(),
            'order' => $data->order,
            'isActive' => $data->isActive,
        ];
    }
}
