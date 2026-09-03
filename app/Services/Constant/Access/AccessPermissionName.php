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

    // Dashboard
    const STAFF_DASHBOARD = AccessGroup::STAFF . '.dashboard.*';
    const STAFF_DASHBOARD_VIEW = AccessGroup::STAFF . '.dashboard.view';

    // Staff
    const STAFF_STAFF = AccessGroup::STAFF . '.staff.*';
    const STAFF_STAFF_VIEW = AccessGroup::STAFF . '.staff.view';
    const STAFF_STAFF_CREATE = AccessGroup::STAFF . '.staff.create';
    const STAFF_STAFF_UPDATE = AccessGroup::STAFF . '.staff.update';
    const STAFF_STAFF_DELETE = AccessGroup::STAFF . '.staff.delete';
    const STAFF_PAGE = AccessGroup::STAFF . '.page.*';
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

    // FAQ Type
    const STAFF_FAQ_TYPE = AccessGroup::STAFF . '.faq-type.*';
    const STAFF_FAQ_TYPE_VIEW = AccessGroup::STAFF . '.faq-type.view';
    const STAFF_FAQ_TYPE_CREATE = AccessGroup::STAFF . '.faq-type.create';
    const STAFF_FAQ_TYPE_UPDATE = AccessGroup::STAFF . '.faq-type.update';
    const STAFF_FAQ_TYPE_DELETE = AccessGroup::STAFF . '.faq-type.delete';
    /** --- OPTIONS --- */

    // Boat
    const STAFF_BOAT = AccessGroup::STAFF . '.boat.*';
    const STAFF_BOAT_VIEW = AccessGroup::STAFF . '.boat.view';
    const STAFF_BOAT_CREATE = AccessGroup::STAFF . '.boat.create';
    const STAFF_BOAT_UPDATE = AccessGroup::STAFF . '.boat.update';
    const STAFF_BOAT_DELETE = AccessGroup::STAFF . '.boat.delete';

    // Boat Component Type
    const STAFF_BOAT_COMPONENT_TYPE = AccessGroup::STAFF . '.boat-component-type.*';
    const STAFF_BOAT_COMPONENT_TYPE_VIEW = AccessGroup::STAFF . '.boat-component-type.view';
    const STAFF_BOAT_COMPONENT_TYPE_CREATE = AccessGroup::STAFF . '.boat-component-type.create';
    const STAFF_BOAT_COMPONENT_TYPE_UPDATE = AccessGroup::STAFF . '.boat-component-type.update';
    const STAFF_BOAT_COMPONENT_TYPE_DELETE = AccessGroup::STAFF . '.boat-component-type.delete';

    // Boat Contact Form
    const STAFF_BOAT_CONTACT_FORM = AccessGroup::STAFF . '.boat-contact-form.*';
    const STAFF_BOAT_CONTACT_FORM_VIEW = AccessGroup::STAFF . '.boat-contact-form.view';
    const STAFF_BOAT_CONTACT_FORM_CREATE = AccessGroup::STAFF . '.boat-contact-form.create';
    const STAFF_BOAT_CONTACT_FORM_UPDATE = AccessGroup::STAFF . '.boat-contact-form.update';
    const STAFF_BOAT_CONTACT_FORM_DELETE = AccessGroup::STAFF . '.boat-contact-form.delete';

    // Blog Category
    const STAFF_BLOG_CATEGORY = AccessGroup::STAFF . '.blog-category.*';
    const STAFF_BLOG_CATEGORY_VIEW = AccessGroup::STAFF . '.blog-category.view';
    const STAFF_BLOG_CATEGORY_CREATE = AccessGroup::STAFF . '.blog-category.create';
    const STAFF_BLOG_CATEGORY_UPDATE = AccessGroup::STAFF . '.blog-category.update';
    const STAFF_BLOG_CATEGORY_DELETE = AccessGroup::STAFF . '.blog-category.delete';

    // Blog Tag
    const STAFF_BLOG_TAG = AccessGroup::STAFF . '.blog-tag.*';
    const STAFF_BLOG_TAG_VIEW = AccessGroup::STAFF . '.blog-tag.view';
    const STAFF_BLOG_TAG_CREATE = AccessGroup::STAFF . '.blog-tag.create';
    const STAFF_BLOG_TAG_UPDATE = AccessGroup::STAFF . '.blog-tag.update';
    const STAFF_BLOG_TAG_DELETE = AccessGroup::STAFF . '.blog-tag.delete';

    // Blog
    const STAFF_BLOG = AccessGroup::STAFF . '.blog.*';
    const STAFF_BLOG_VIEW = AccessGroup::STAFF . '.blog.view';
    const STAFF_BLOG_CREATE = AccessGroup::STAFF . '.blog.create';
    const STAFF_BLOG_UPDATE = AccessGroup::STAFF . '.blog.update';
    const STAFF_BLOG_DELETE = AccessGroup::STAFF . '.blog.delete';

    // Offer
    const STAFF_OFFER = AccessGroup::STAFF . '.offer.*';
    const STAFF_OFFER_VIEW = AccessGroup::STAFF . '.offer.view';
    const STAFF_OFFER_CREATE = AccessGroup::STAFF . '.offer.create';
    const STAFF_OFFER_UPDATE = AccessGroup::STAFF . '.offer.update';
    const STAFF_OFFER_DELETE = AccessGroup::STAFF . '.offer.delete';

    // Team Member
    const STAFF_TEAM_MEMBER = AccessGroup::STAFF . '.team-member.*';
    const STAFF_TEAM_MEMBER_VIEW = AccessGroup::STAFF . '.team-member.view';
    const STAFF_TEAM_MEMBER_CREATE = AccessGroup::STAFF . '.team-member.create';
    const STAFF_TEAM_MEMBER_UPDATE = AccessGroup::STAFF . '.team-member.update';
    const STAFF_TEAM_MEMBER_DELETE = AccessGroup::STAFF . '.team-member.delete';

    // TLT Testimonial
    const STAFF_TLT_TESTIMONIAL = AccessGroup::STAFF . '.tlt-testimonial.*';
    const STAFF_TLT_TESTIMONIAL_VIEW = AccessGroup::STAFF . '.tlt-testimonial.view';
    const STAFF_TLT_TESTIMONIAL_CREATE = AccessGroup::STAFF . '.tlt-testimonial.create';
    const STAFF_TLT_TESTIMONIAL_UPDATE = AccessGroup::STAFF . '.tlt-testimonial.update';
    const STAFF_TLT_TESTIMONIAL_DELETE = AccessGroup::STAFF . '.tlt-testimonial.delete';

    // Experience Type
    const STAFF_EXPERIENCE_TYPE = AccessGroup::STAFF . '.experience-type.*';
    const STAFF_EXPERIENCE_TYPE_VIEW = AccessGroup::STAFF . '.experience-type.view';
    const STAFF_EXPERIENCE_TYPE_CREATE = AccessGroup::STAFF . '.experience-type.create';
    const STAFF_EXPERIENCE_TYPE_UPDATE = AccessGroup::STAFF . '.experience-type.update';
    const STAFF_EXPERIENCE_TYPE_DELETE = AccessGroup::STAFF . '.experience-type.delete';

    // Experience Category
    const STAFF_EXPERIENCE_AREA = AccessGroup::STAFF . '.experience-area.*';
    const STAFF_EXPERIENCE_AREA_VIEW = AccessGroup::STAFF . '.experience-area.view';
    const STAFF_EXPERIENCE_AREA_CREATE = AccessGroup::STAFF . '.experience-area.create';
    const STAFF_EXPERIENCE_AREA_UPDATE = AccessGroup::STAFF . '.experience-area.update';
    const STAFF_EXPERIENCE_AREA_DELETE = AccessGroup::STAFF . '.experience-area.delete';

    // Experience
    const STAFF_EXPERIENCE = AccessGroup::STAFF . '.experience.*';
    const STAFF_EXPERIENCE_VIEW = AccessGroup::STAFF . '.experience.view';
    const STAFF_EXPERIENCE_CREATE = AccessGroup::STAFF . '.experience.create';
    const STAFF_EXPERIENCE_UPDATE = AccessGroup::STAFF . '.experience.update';
    const STAFF_EXPERIENCE_DELETE = AccessGroup::STAFF . '.experience.delete';

    const STAFF_EXPERIENCE_INQUIRY_FORM        = AccessGroup::STAFF . '.experience-inquiry-form.*';
    const STAFF_EXPERIENCE_INQUIRY_FORM_VIEW   = AccessGroup::STAFF . '.experience-inquiry-form.view';
    const STAFF_EXPERIENCE_INQUIRY_FORM_CREATE = AccessGroup::STAFF . '.experience-inquiry-form.create';
    const STAFF_EXPERIENCE_INQUIRY_FORM_DELETE = AccessGroup::STAFF . '.experience-inquiry-form.delete';

    // Menu
    const STAFF_MENU = AccessGroup::STAFF . '.menu.*';
    const STAFF_MENU_VIEW = AccessGroup::STAFF . '.menu.view';
    const STAFF_MENU_CREATE = AccessGroup::STAFF . '.menu.create';
    const STAFF_MENU_UPDATE = AccessGroup::STAFF . '.menu.update';
    const STAFF_MENU_DELETE = AccessGroup::STAFF . '.menu.delete';

    // Homepage
    const STAFF_HOMEPAGE = AccessGroup::STAFF . '.homepage.*';
    const STAFF_HOMEPAGE_VIEW = AccessGroup::STAFF . '.homepage.view';
    const STAFF_HOMEPAGE_UPDATE = AccessGroup::STAFF . '.homepage.update';

    // Amenity Category
    const STAFF_AMENITY_CATEGORY = AccessGroup::STAFF . '.amenity-category.*';
    const STAFF_AMENITY_CATEGORY_VIEW = AccessGroup::STAFF . '.amenity-category.view';
    const STAFF_AMENITY_CATEGORY_CREATE = AccessGroup::STAFF . '.amenity-category.create';
    const STAFF_AMENITY_CATEGORY_UPDATE = AccessGroup::STAFF . '.amenity-category.update';
    const STAFF_AMENITY_CATEGORY_DELETE = AccessGroup::STAFF . '.amenity-category.delete';

    // Amenity
    const STAFF_AMENITY = AccessGroup::STAFF . '.amenity.*';
    const STAFF_AMENITY_VIEW = AccessGroup::STAFF . '.amenity.view';
    const STAFF_AMENITY_CREATE = AccessGroup::STAFF . '.amenity.create';
    const STAFF_AMENITY_UPDATE = AccessGroup::STAFF . '.amenity.update';
    const STAFF_AMENITY_DELETE = AccessGroup::STAFF . '.amenity.delete';

    // Property Type
    const STAFF_PROPERTY_TYPE = AccessGroup::STAFF . '.property-type.*';
    const STAFF_PROPERTY_TYPE_VIEW = AccessGroup::STAFF . '.property-type.view';
    const STAFF_PROPERTY_TYPE_CREATE = AccessGroup::STAFF . '.property-type.create';
    const STAFF_PROPERTY_TYPE_UPDATE = AccessGroup::STAFF . '.property-type.update';
    const STAFF_PROPERTY_TYPE_DELETE = AccessGroup::STAFF . '.property-type.delete';

    // Property Source Type
    const STAFF_PROPERTY_SOURCE_TYPE = AccessGroup::STAFF . '.property-source-type.*';
    const STAFF_PROPERTY_SOURCE_TYPE_VIEW = AccessGroup::STAFF . '.property-source-type.view';
    const STAFF_PROPERTY_SOURCE_TYPE_CREATE = AccessGroup::STAFF . '.property-source-type.create';
    const STAFF_PROPERTY_SOURCE_TYPE_UPDATE = AccessGroup::STAFF . '.property-source-type.update';
    const STAFF_PROPERTY_SOURCE_TYPE_DELETE = AccessGroup::STAFF . '.property-source-type.delete';

    // Property Room Type
    const STAFF_PROPERTY_ROOM_TYPE = AccessGroup::STAFF . '.property-room-type.*';
    const STAFF_PROPERTY_ROOM_TYPE_VIEW = AccessGroup::STAFF . '.property-room-type.view';
    const STAFF_PROPERTY_ROOM_TYPE_CREATE = AccessGroup::STAFF . '.property-room-type.create';
    const STAFF_PROPERTY_ROOM_TYPE_UPDATE = AccessGroup::STAFF . '.property-room-type.update';
    const STAFF_PROPERTY_ROOM_TYPE_DELETE = AccessGroup::STAFF . '.property-room-type.delete';

    // Property Bed Type
    const STAFF_PROPERTY_BED_TYPE = AccessGroup::STAFF . '.property-bed-type.*';
    const STAFF_PROPERTY_BED_TYPE_VIEW = AccessGroup::STAFF . '.property-bed-type.view';
    const STAFF_PROPERTY_BED_TYPE_CREATE = AccessGroup::STAFF . '.property-bed-type.create';
    const STAFF_PROPERTY_BED_TYPE_UPDATE = AccessGroup::STAFF . '.property-bed-type.update';
    const STAFF_PROPERTY_BED_TYPE_DELETE = AccessGroup::STAFF . '.property-bed-type.delete';

    // Property Tag
    const STAFF_PROPERTY_TAG = AccessGroup::STAFF . '.property-tag.*';
    const STAFF_PROPERTY_TAG_VIEW = AccessGroup::STAFF . '.property-tag.view';
    const STAFF_PROPERTY_TAG_CREATE = AccessGroup::STAFF . '.property-tag.create';
    const STAFF_PROPERTY_TAG_UPDATE = AccessGroup::STAFF . '.property-tag.update';
    const STAFF_PROPERTY_TAG_DELETE = AccessGroup::STAFF . '.property-tag.delete';

    // Property Contact Form
    const STAFF_PROPERTY_CONTACT_FORM = AccessGroup::STAFF . '.property-contact-form.*';
    const STAFF_PROPERTY_CONTACT_FORM_VIEW = AccessGroup::STAFF . '.property-contact-form.view';
    const STAFF_PROPERTY_CONTACT_FORM_CREATE = AccessGroup::STAFF . '.property-contact-form.create';
    const STAFF_PROPERTY_CONTACT_FORM_UPDATE = AccessGroup::STAFF . '.property-contact-form.update';
    const STAFF_PROPERTY_CONTACT_FORM_DELETE = AccessGroup::STAFF . '.property-contact-form.delete';

    // Property Inquiry Form
    const STAFF_PROPERTY_INQUIRY_FORM_VIEW = AccessGroup::STAFF . '.property-inquiry-form.view';
    const STAFF_PROPERTY_INQUIRY_FORM_CREATE = AccessGroup::STAFF . '.property-inquiry-form.create';
    const STAFF_PROPERTY_INQUIRY_FORM_UPDATE = AccessGroup::STAFF . '.property-inquiry-form.update';
    const STAFF_PROPERTY_INQUIRY_FORM_DELETE = AccessGroup::STAFF . '.property-inquiry-form.delete';

    // Property
    const STAFF_PROPERTY = AccessGroup::STAFF . '.property.*';
    const STAFF_PROPERTY_VIEW = AccessGroup::STAFF . '.property.view';
    const STAFF_PROPERTY_CREATE = AccessGroup::STAFF . '.property.create';
    const STAFF_PROPERTY_UPDATE = AccessGroup::STAFF . '.property.update';
    const STAFF_PROPERTY_DELETE = AccessGroup::STAFF . '.property.delete';

    // Property Review
    const STAFF_PROPERTY_REVIEW = AccessGroup::STAFF . '.property-review.*';
    const STAFF_PROPERTY_REVIEW_VIEW = AccessGroup::STAFF . '.property-review.view';
    const STAFF_PROPERTY_REVIEW_CREATE = AccessGroup::STAFF . '.property-review.create';
    const STAFF_PROPERTY_REVIEW_UPDATE = AccessGroup::STAFF . '.property-review.update';
    const STAFF_PROPERTY_REVIEW_DELETE = AccessGroup::STAFF . '.property-review.delete';

    // API Configuration (Open API Features)
    const STAFF_API_CONFIGURATION = AccessGroup::STAFF . '.api-configuration.*';
    const STAFF_API_CONFIGURATION_VIEW = AccessGroup::STAFF . '.api-configuration.view';
    const STAFF_API_CONFIGURATION_CREATE = AccessGroup::STAFF . '.api-configuration.create';
    const STAFF_API_CONFIGURATION_UPDATE = AccessGroup::STAFF . '.api-configuration.update';
    const STAFF_API_CONFIGURATION_DELETE = AccessGroup::STAFF . '.api-configuration.delete';

    // System Information
    const STAFF_SYSTEM_INFORMATION_VIEW = AccessGroup::STAFF . '.system-information.view';

    // System Cache
    const STAFF_SYSTEM_CACHE_RUN = AccessGroup::STAFF . '.system-cache.run';

    const STAFF_OPTION = [
        self::STAFF_DASHBOARD,
        self::STAFF_DASHBOARD_VIEW,

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

        self::STAFF_FAQ_TYPE,
        self::STAFF_FAQ_TYPE_VIEW,
        self::STAFF_FAQ_TYPE_CREATE,
        self::STAFF_FAQ_TYPE_UPDATE,
        self::STAFF_FAQ_TYPE_DELETE,

        self::STAFF_BOAT,
        self::STAFF_BOAT_VIEW,
        self::STAFF_BOAT_CREATE,
        self::STAFF_BOAT_UPDATE,
        self::STAFF_BOAT_DELETE,

        self::STAFF_BOAT_COMPONENT_TYPE,
        self::STAFF_BOAT_COMPONENT_TYPE_VIEW,
        self::STAFF_BOAT_COMPONENT_TYPE_CREATE,
        self::STAFF_BOAT_COMPONENT_TYPE_UPDATE,
        self::STAFF_BOAT_COMPONENT_TYPE_DELETE,

        self::STAFF_BLOG,
        self::STAFF_BLOG_VIEW,
        self::STAFF_BLOG_CREATE,
        self::STAFF_BLOG_UPDATE,
        self::STAFF_BLOG_DELETE,

        self::STAFF_OFFER,
        self::STAFF_OFFER_VIEW,
        self::STAFF_OFFER_CREATE,
        self::STAFF_OFFER_UPDATE,
        self::STAFF_OFFER_DELETE,

        self::STAFF_TEAM_MEMBER,
        self::STAFF_TEAM_MEMBER_VIEW,
        self::STAFF_TEAM_MEMBER_CREATE,
        self::STAFF_TEAM_MEMBER_UPDATE,
        self::STAFF_TEAM_MEMBER_DELETE,

        self::STAFF_TLT_TESTIMONIAL,
        self::STAFF_TLT_TESTIMONIAL_VIEW,
        self::STAFF_TLT_TESTIMONIAL_CREATE,
        self::STAFF_TLT_TESTIMONIAL_UPDATE,
        self::STAFF_TLT_TESTIMONIAL_DELETE,

        self::STAFF_BLOG_CATEGORY,
        self::STAFF_BLOG_CATEGORY_VIEW,
        self::STAFF_BLOG_CATEGORY_CREATE,
        self::STAFF_BLOG_CATEGORY_UPDATE,
        self::STAFF_BLOG_CATEGORY_DELETE,

        self::STAFF_BLOG_TAG,
        self::STAFF_BLOG_TAG_VIEW,
        self::STAFF_BLOG_TAG_CREATE,
        self::STAFF_BLOG_TAG_UPDATE,
        self::STAFF_BLOG_TAG_DELETE,

        self::STAFF_EXPERIENCE_TYPE,
        self::STAFF_EXPERIENCE_TYPE_VIEW,
        self::STAFF_EXPERIENCE_TYPE_CREATE,
        self::STAFF_EXPERIENCE_TYPE_UPDATE,
        self::STAFF_EXPERIENCE_TYPE_DELETE,

        self::STAFF_EXPERIENCE_AREA,
        self::STAFF_EXPERIENCE_AREA_VIEW,
        self::STAFF_EXPERIENCE_AREA_CREATE,
        self::STAFF_EXPERIENCE_AREA_UPDATE,
        self::STAFF_EXPERIENCE_AREA_DELETE,

        self::STAFF_EXPERIENCE,
        self::STAFF_EXPERIENCE_VIEW,
        self::STAFF_EXPERIENCE_CREATE,
        self::STAFF_EXPERIENCE_UPDATE,
        self::STAFF_EXPERIENCE_DELETE,

        self::STAFF_EXPERIENCE_INQUIRY_FORM,
        self::STAFF_EXPERIENCE_INQUIRY_FORM_VIEW,
        self::STAFF_EXPERIENCE_INQUIRY_FORM_CREATE,
        self::STAFF_EXPERIENCE_INQUIRY_FORM_DELETE,

        self::STAFF_MENU,
        self::STAFF_MENU_VIEW,
        self::STAFF_MENU_CREATE,
        self::STAFF_MENU_UPDATE,

        self::STAFF_HOMEPAGE,
        self::STAFF_HOMEPAGE_VIEW,
        self::STAFF_HOMEPAGE_UPDATE,

        self::STAFF_AMENITY_CATEGORY,
        self::STAFF_AMENITY_CATEGORY_VIEW,
        self::STAFF_AMENITY_CATEGORY_CREATE,
        self::STAFF_AMENITY_CATEGORY_UPDATE,
        self::STAFF_AMENITY_CATEGORY_DELETE,

        self::STAFF_AMENITY,
        self::STAFF_AMENITY_VIEW,
        self::STAFF_AMENITY_CREATE,
        self::STAFF_AMENITY_UPDATE,
        self::STAFF_AMENITY_DELETE,

        self::STAFF_PROPERTY_TYPE,
        self::STAFF_PROPERTY_TYPE_VIEW,
        self::STAFF_PROPERTY_TYPE_CREATE,
        self::STAFF_PROPERTY_TYPE_UPDATE,
        self::STAFF_PROPERTY_TYPE_DELETE,

        self::STAFF_PROPERTY_SOURCE_TYPE,
        self::STAFF_PROPERTY_SOURCE_TYPE_VIEW,
        self::STAFF_PROPERTY_SOURCE_TYPE_CREATE,
        self::STAFF_PROPERTY_SOURCE_TYPE_UPDATE,
        self::STAFF_PROPERTY_SOURCE_TYPE_DELETE,

        self::STAFF_PROPERTY_ROOM_TYPE,
        self::STAFF_PROPERTY_ROOM_TYPE_VIEW,
        self::STAFF_PROPERTY_ROOM_TYPE_CREATE,
        self::STAFF_PROPERTY_ROOM_TYPE_UPDATE,
        self::STAFF_PROPERTY_ROOM_TYPE_DELETE,

        self::STAFF_PROPERTY_BED_TYPE,
        self::STAFF_PROPERTY_BED_TYPE_VIEW,
        self::STAFF_PROPERTY_BED_TYPE_CREATE,
        self::STAFF_PROPERTY_BED_TYPE_UPDATE,
        self::STAFF_PROPERTY_BED_TYPE_DELETE,

        self::STAFF_PROPERTY_TAG,
        self::STAFF_PROPERTY_TAG_VIEW,
        self::STAFF_PROPERTY_TAG_CREATE,
        self::STAFF_PROPERTY_TAG_UPDATE,
        self::STAFF_PROPERTY_TAG_DELETE,

        self::STAFF_PROPERTY,
        self::STAFF_PROPERTY_VIEW,
        self::STAFF_PROPERTY_CREATE,
        self::STAFF_PROPERTY_UPDATE,
        self::STAFF_PROPERTY_DELETE,

        self::STAFF_PROPERTY_REVIEW,
        self::STAFF_PROPERTY_REVIEW_VIEW,
        self::STAFF_PROPERTY_REVIEW_CREATE,
        self::STAFF_PROPERTY_REVIEW_UPDATE,
        self::STAFF_PROPERTY_REVIEW_DELETE,

        self::STAFF_PROPERTY_CONTACT_FORM,
        self::STAFF_PROPERTY_CONTACT_FORM_VIEW,
        self::STAFF_PROPERTY_CONTACT_FORM_CREATE,
        self::STAFF_PROPERTY_CONTACT_FORM_UPDATE,
        self::STAFF_PROPERTY_CONTACT_FORM_DELETE,

        self::STAFF_PROPERTY_INQUIRY_FORM_VIEW,
        self::STAFF_PROPERTY_INQUIRY_FORM_CREATE,
        self::STAFF_PROPERTY_INQUIRY_FORM_UPDATE,
        self::STAFF_PROPERTY_INQUIRY_FORM_DELETE,

        self::STAFF_API_CONFIGURATION,
        self::STAFF_API_CONFIGURATION_VIEW,
        self::STAFF_API_CONFIGURATION_CREATE,
        self::STAFF_API_CONFIGURATION_UPDATE,
        self::STAFF_API_CONFIGURATION_DELETE,

        self::STAFF_SYSTEM_INFORMATION_VIEW,

        self::STAFF_SYSTEM_CACHE_RUN,

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
