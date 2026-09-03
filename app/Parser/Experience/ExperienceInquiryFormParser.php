<?php

namespace App\Parser\Experience;

use App\Services\Constant\Global\MailStatus;
use Logia\Core\Parser\BaseParser;

class ExperienceInquiryFormParser extends BaseParser
{
    public static function first($data)
    {
        if (!$data) return null;

        $status = MailStatus::idName($data->statusId);
        return [
            'id'                   => $data->id,
            'experienceId'         => $data->experienceId,
            'experience'           => $data->experience ? [
                'id'   => $data->experience->id,
                'name' => $data->experience->name,
            ] : null,
            'fullName'             => $data->fullName,
            'status' => $status,
            'phone'                => $data->phone,
            'email'                => $data->email,
            'eventDate'            => optional($data->eventDate)->format('d/m/Y'),
            'totalGuests'          => $data->totalGuests,
            'countryOfResidence'   => $data->countryOfResidence,
            'mealStyle'            => $data->mealStyle,
            'weddingLocation'      => $data->weddingLocation,
            'ceremonyType'         => $data->ceremonyType,
            'accommodationNights'  => $data->accommodationNights,
            'maxNightlyBudget'     => $data->maxNightlyBudget,
            'notes'                => $data->notes,
            'createdAt'            => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }

    public static function brief($data)
    {
        if (!$data) return null;

        $status = MailStatus::idName($data->statusId);
        
        return [
            'id'                 => $data->id,
            'experienceId'       => $data->experienceId,
            'experience'         => $data->experience ? [
                'id'   => $data->experience->id,
                'name' => $data->experience->name,
            ] : null,
            'fullName'           => $data->fullName,
            'status' => $status,
            'phone'              => $data->phone,
            'email'              => $data->email,
            'eventDate'          => optional($data->eventDate)->format('d/m/Y'),
            'totalGuests'        => $data->totalGuests,
            'countryOfResidence' => $data->countryOfResidence,
            'createdAt'          => optional($data->createdAt)->format('d/m/Y H:i'),
        ];
    }
}
