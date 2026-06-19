<?php

namespace App\Parser\Staff;

use App\Services\Constant\Setting\Gender;
use Logia\Core\Parser\BaseParser;

class StaffParser extends BaseParser
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
            'fullName' => $data->fullName,
            'email' => $data->user?->email,
            'phone' => $data->phone,
            'gender' => Gender::idName($data->genderId),
            'country' => $data->country?->only('id', 'name'),
            'address' => $data->address,
            'isActive' => $data->isActive,
            'isSuperadmin' => $data->isSuperadmin,
            'createdBy' => $data->createdByName,
            'createdAt' => $data->createdAt?->format('d/m/Y H:i')
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
            'fullName' => $data->fullName,
            'email' => $data->user?->email,
            'phone' => $data->phone,
            'gender' => Gender::idName($data->genderId),
            'country' => $data->country?->only('id', 'name'),
            'address' => $data->address,
            'isActive' => $data->isActive,
        ];
    }

}
