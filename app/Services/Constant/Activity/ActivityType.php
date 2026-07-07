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
    const BOAT = 'boat';
    const BOAT_COMPONENT_TYPE = 'boat_component_type';
    const BOAT_CONTACT_FORM = 'boat_contact_form';
    const BLOG_CATEGORY = 'blog_category';
    const BLOG_TAG = 'blog_tag';
    const BLOG = 'blog';

    const OPTION = [
        self::ACCESS,
        self::STAFF,
        self::C_CONTACT_FORM_TYPE,
        self::MEDIA_PARTNER,
        self::WEBSITE_CONTACT_FORM,
        self::TLT_REVIEW,
        self::FAQ,
        self::BOAT,
        self::BOAT_COMPONENT_TYPE,
        self::BOAT_CONTACT_FORM,
        self::BLOG_CATEGORY,
        self::BLOG_TAG,
        self::BLOG

    ];
}
