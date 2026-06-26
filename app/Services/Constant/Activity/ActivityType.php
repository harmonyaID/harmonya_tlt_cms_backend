<?php

namespace App\Services\Constant\Activity;

use App\Services\Constant\BaseCodeName;

class ActivityType extends BaseCodeName
{
    const ACCESS = 'access';
    const STAFF = 'staff';
    const C_CONTACT_FORM_TYPE = 'component_contact_form_type';

    const OPTION = [
        self::ACCESS,
        self::STAFF,
        self::C_CONTACT_FORM_TYPE
    ];

}
