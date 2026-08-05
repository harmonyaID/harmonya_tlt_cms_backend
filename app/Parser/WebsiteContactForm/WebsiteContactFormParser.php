<?php

namespace App\Parser\WebsiteContactForm;

use App\Services\Constant\Global\MailStatus;
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

        $status = MailStatus::idName($data->statusId);

        return [
            'id' => $data->id,
            'formType' => optional($data->getFormType)->only('id', 'name'),
            'name' => $data->name,
            'status' => $status,
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

        $status = MailStatus::idName($data->statusId);

        return [
            'id' => $data->id,
            'formType' => optional($data->getFormType)->only('id', 'name'),
            'name' => $data->name,
            'status' => $status,
            'email' => $data->email,
            'phone' => $data->phone,
            'subject' => $data->subject,
            'isRead' => $data->isRead,
            'message' => $data->message,
            'createdAt' => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }
}
