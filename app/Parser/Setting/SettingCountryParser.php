<?php

namespace App\Parser\Setting;

use Logia\Core\Parser\BaseParser;

class SettingCountryParser extends BaseParser
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
            'cca2' => $data->cca2,
            'cca3' => $data->cca3,
            'ccn3' => $data->ccn3,
            'name' => $data->name,
            'phoneCode' => $data->phoneCode,
            'flag' => $data->flag,
            'isActive' => $data->isActive,
            'isPopular' => $data->isPopular,
            'createdBy' => $data->createdByName,
            'createdAt' => $data->createdAt?->format('d/m/Y H:i'),
        ];
    }

}
