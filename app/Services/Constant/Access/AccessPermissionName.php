<?php

namespace App\Services\Constant\Access;

use Illuminate\Support\Str;

class AccessPermissionName
{
    /** --- STAFF PERMISSIONS --- */

    // Setting
    const STAFF_SETTING = AccessGroup::STAFF . '.setting.*';
    const STAFF_SETTING_VIEW = AccessGroup::STAFF . '.setting.view';
    const STAFF_SETTING_CREATE = AccessGroup::STAFF . '.setting.create';
    const STAFF_SETTING_UPDATE = AccessGroup::STAFF . '.setting.update';
    const STAFF_SETTING_DELETE = AccessGroup::STAFF . '.setting.delete';

    // Staff
    const STAFF_STAFF = AccessGroup::STAFF . '.staff.*';
    const STAFF_STAFF_VIEW = AccessGroup::STAFF . '.staff.view';
    const STAFF_STAFF_CREATE = AccessGroup::STAFF . '.staff.create';
    const STAFF_STAFF_UPDATE = AccessGroup::STAFF . '.staff.update';
    const STAFF_STAFF_DELETE = AccessGroup::STAFF . '.staff.delete';
    const STAFF_PAGE= AccessGroup::STAFF . '.page.*';
    const STAFF_PAGE_VIEW = AccessGroup::STAFF . '.page.view';
    const STAFF_PAGE_CREATE = AccessGroup::STAFF . '.page.create';
    const STAFF_PAGE_UPDATE = AccessGroup::STAFF . '.page.update';
    const STAFF_PAGE_DELETE = AccessGroup::STAFF . '.page.delete';

    // Access
    const STAFF_ACCESS = AccessGroup::STAFF . '.access.*';
    const STAFF_ACCESS_VIEW = AccessGroup::STAFF . '.access.view';
    const STAFF_ACCESS_UPDATE = AccessGroup::STAFF . '.access.update';

    // Language
    const STAFF_LANGUAGE = AccessGroup::STAFF . '.language.*';
    const STAFF_LANGUAGE_VIEW = AccessGroup::STAFF . '.language.view';
    const STAFF_LANGUAGE_CREATE = AccessGroup::STAFF . '.language.create';
    const STAFF_LANGUAGE_UPDATE = AccessGroup::STAFF . '.language.update';
    const STAFF_LANGUAGE_DELETE = AccessGroup::STAFF . '.language.delete';

    // Configuration
    const STAFF_CONFIGURATION = AccessGroup::STAFF . '.configuration.*';
    const STAFF_CONFIGURATION_VIEW = AccessGroup::STAFF . '.configuration.view';
    const STAFF_CONFIGURATION_UPDATE = AccessGroup::STAFF . '.configuration.update';

    // Media Partner
    const STAFF_MEDIA_PARTNER = AccessGroup::STAFF . '.media-partner.*';
    const STAFF_MEDIA_PARTNER_VIEW = AccessGroup::STAFF . '.media-partner.view';
    const STAFF_MEDIA_PARTNER_CREATE = AccessGroup::STAFF . '.media-partner.create';
    const STAFF_MEDIA_PARTNER_UPDATE = AccessGroup::STAFF . '.media-partner.update';
    const STAFF_MEDIA_PARTNER_DELETE = AccessGroup::STAFF . '.media-partner.delete';

    // Website Contact Form
    const STAFF_WEBSITE_CONTACT_FORM = AccessGroup::STAFF . '.website-contact-form.*';
    const STAFF_WEBSITE_CONTACT_FORM_VIEW = AccessGroup::STAFF . '.website-contact-form.view';
    const STAFF_WEBSITE_CONTACT_FORM_CREATE = AccessGroup::STAFF . '.website-contact-form.create';
    const STAFF_WEBSITE_CONTACT_FORM_UPDATE = AccessGroup::STAFF . '.website-contact-form.update';
    const STAFF_WEBSITE_CONTACT_FORM_DELETE = AccessGroup::STAFF . '.website-contact-form.delete';

    // TLT Review
    const STAFF_TLT_REVIEW = AccessGroup::STAFF . '.tlt-review.*';
    const STAFF_TLT_REVIEW_VIEW = AccessGroup::STAFF . '.tlt-review.view';
    const STAFF_TLT_REVIEW_CREATE = AccessGroup::STAFF . '.tlt-review.create';
    const STAFF_TLT_REVIEW_UPDATE = AccessGroup::STAFF . '.tlt-review.update';
    const STAFF_TLT_REVIEW_DELETE = AccessGroup::STAFF . '.tlt-review.delete';

    // FAQ
    const STAFF_FAQ        = AccessGroup::STAFF . '.faq.*';
    const STAFF_FAQ_VIEW   = AccessGroup::STAFF . '.faq.view';
    const STAFF_FAQ_CREATE = AccessGroup::STAFF . '.faq.create';
    const STAFF_FAQ_UPDATE = AccessGroup::STAFF . '.faq.update';
    const STAFF_FAQ_DELETE = AccessGroup::STAFF . '.faq.delete';
    /** --- OPTIONS --- */

    const STAFF_OPTION = [
        self::STAFF_SETTING,
        self::STAFF_SETTING_VIEW,
        self::STAFF_SETTING_CREATE,
        self::STAFF_SETTING_UPDATE,
        self::STAFF_SETTING_DELETE,
        self::STAFF_STAFF,
        self::STAFF_STAFF_VIEW,
        self::STAFF_STAFF_CREATE,
        self::STAFF_STAFF_UPDATE,
        self::STAFF_STAFF_DELETE,
        self::STAFF_PAGE,
        self::STAFF_PAGE_VIEW,
        self::STAFF_PAGE_CREATE,
        self::STAFF_PAGE_UPDATE,
        self::STAFF_PAGE_DELETE,

        self::STAFF_ACCESS,
        self::STAFF_ACCESS_VIEW,
        self::STAFF_ACCESS_UPDATE,

        self::STAFF_LANGUAGE,
        self::STAFF_LANGUAGE_VIEW,
        self::STAFF_LANGUAGE_CREATE,
        self::STAFF_LANGUAGE_UPDATE,
        self::STAFF_LANGUAGE_DELETE,

        self::STAFF_CONFIGURATION,
        self::STAFF_CONFIGURATION_VIEW,
        self::STAFF_CONFIGURATION_UPDATE,

        self::STAFF_MEDIA_PARTNER,
        self::STAFF_MEDIA_PARTNER_VIEW,
        self::STAFF_MEDIA_PARTNER_CREATE,
        self::STAFF_MEDIA_PARTNER_UPDATE,
        self::STAFF_MEDIA_PARTNER_DELETE,

        self::STAFF_WEBSITE_CONTACT_FORM,
        self::STAFF_WEBSITE_CONTACT_FORM_VIEW,
        self::STAFF_WEBSITE_CONTACT_FORM_CREATE,
        self::STAFF_WEBSITE_CONTACT_FORM_UPDATE,
        self::STAFF_WEBSITE_CONTACT_FORM_DELETE,

        self::STAFF_TLT_REVIEW,
        self::STAFF_TLT_REVIEW_VIEW,
        self::STAFF_TLT_REVIEW_CREATE,
        self::STAFF_TLT_REVIEW_UPDATE,
        self::STAFF_TLT_REVIEW_DELETE,

        self::STAFF_FAQ,
        self::STAFF_FAQ_VIEW,
        self::STAFF_FAQ_CREATE,
        self::STAFF_FAQ_UPDATE,
        self::STAFF_FAQ_DELETE,


    ];

    /** --- FUNCTIONS --- */

    public static function getStaff()
    {
        $options = self::STAFF_OPTION;

        $results = [];
        foreach ($options as $option) {
            $results[] = [
                'name' => $option,
                'display' => self::display($option)
            ];
        }

        return $results;
    }

    /** --- UNEXPORTED FUNCTIONS */

    protected static function display($permission)
    {
        $display = '';

        $names = explode('.', $permission);
        foreach ($names as $key => $name) {
            if ($key == 0) {
                continue;
            } elseif ($name == '*') {
                $display .= ' All';
                continue;
            } elseif ($key > 1) {
                $display .= " ";
            }

            $display .= Str::title(str_replace('-', ' ', $name));
        }

        return $display;
    }

}
