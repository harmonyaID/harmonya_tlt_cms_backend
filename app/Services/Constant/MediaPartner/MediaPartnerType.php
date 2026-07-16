<?php

namespace App\Services\Constant\MediaPartner;

use App\Services\Constant\BaseIDName;

class MediaPartnerType extends BaseIDName
{
    const MEDIA_PARTNER_TYPE_BASIC = 'Press';
    const MEDIA_PARTNER_TYPE_BASIC_ID= 1;
    const  MEDIA_PARTNER_TYPE_PRESS= 'Basic';
    const MEDIA_PARTNER_TYPE_PRESS_ID= 2;

    const OPTION = [
        self::MEDIA_PARTNER_TYPE_BASIC => self::MEDIA_PARTNER_TYPE_BASIC_ID,
        self::MEDIA_PARTNER_TYPE_PRESS => self::MEDIA_PARTNER_TYPE_PRESS_ID,
    ];
}
