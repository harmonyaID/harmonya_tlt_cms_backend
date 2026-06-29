<?php

namespace App\Parser\WebsiteContactForm;

use Logia\Core\Parser\BaseParser;

class WebsiteContactFormParser extends BaseParser
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
            'formType' => optional($data->getFormType)->only('id', 'name'),
            'name' => $data->name,
            'email' => $data->email,
            'phone' => $data->phone,
            'subject' => $data->subject,
            'message' => $data->message,
            'isRead' => $data->isRead,
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
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
            'formType' => optional($data->getFormType)->only('id', 'name'),
            'name' => $data->name,
            'email' => $data->email,
            'phone' => $data->phone,
            'subject' => $data->subject,
            'isRead' => $data->isRead,
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }
}