<?php

namespace App\Services\Constant\Activity;

use App\Services\Constant\BaseCodeName;

class ActivityType extends BaseCodeName
{
    const ACCESS = 'access';
    const STAFF = 'staff';
    const C_CONTACT_FORM_TYPE = 'component_contact_form_type';
    const MEDIA_PARTNER = 'media_partner';
    const WEBSITE_CONTACT_FORM = 'website_contact_form';
    const TLT_REVIEW = 'tlt_review';
    const FAQ = 'faq';

    const OPTION = [
        self::ACCESS,
        self::STAFF,
        self::C_CONTACT_FORM_TYPE,
        self::MEDIA_PARTNER,
        self::WEBSITE_CONTACT_FORM,
        self::TLT_REVIEW,
        self::FAQ


    ];
}
