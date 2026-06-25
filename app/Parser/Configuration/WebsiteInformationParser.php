<?php

namespace App\Parser\Configuration;

use Logia\Core\Parser\BaseParser;

class WebsiteInformationParser extends BaseParser
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

        return [
            'id' => $data->id,
            'title' => $data->title,
            'email' => $data->email,
            'phone' => $data->phone,
            'fax' => $data->fax,
            'whatsapp' => $data->whatsapp,
            'country' => optional($data->getCountry)->only('code', 'name'),
            'postalCode' => $data->postalCode,
            'address' => $data->address,
            'mapEmbed' => $data->mapEmbed,
            'socialMedia' => $data->socialMedia,
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

        return [
            'id' => $data->id,
            'title' => $data->title,
            'email' => $data->email,
            'phone' => $data->phone,
            'fax' => $data->fax,
            'whatsapp' => $data->whatsapp,
            'country' => optional($data->getCountry)->only('code', 'name'),
            'postalCode' => $data->postalCode,
            'address' => $data->address,
            'mapEmbed' => $data->mapEmbed,
            'socialMedia' => $data->socialMedia,
        ];
    }
}
