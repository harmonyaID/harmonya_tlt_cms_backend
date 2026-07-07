<?php

namespace App\Services\Constant\Storage;

class PathConstant
{
    /*
     |--------------------------------------------------------------------------
     | BASE PATHS
     |-------------------------------------------------------------------------
     */
    const STORAGE_BASE = 'app/';
    const STORAGE_PUBLIC_BASE = 'app/public/';
    const STORAGE_PUBLIC_TMP = 'app/public/tmp/';
    const STORAGE_PUBLIC_TMP_PROPERTY = 'app/public/tmp/properties/';


    /*
     |--------------------------------------------------------------------------
     | LANGUAGE
     |-------------------------------------------------------------------------
     */
    const LANG = 'lang/';


    /*
     |--------------------------------------------------------------------------
     | IMAGES
     |-------------------------------------------------------------------------
     */
    const IMAGES_LANGUAGE = 'images/languages/';
    const IMAGES_CURRENCY = 'images/currencies/';
    const IMAGES_STAFF = 'images/staffs/';
    const IMAGES_CONTACT = 'images/contacts/';
    const IMAGES_CONTACT_IDENTITY = 'images/contacts/identities/';
    const IMAGES_CUSTOMER = 'images/customers/';
    const IMAGES_CUSTOMER_IDENTITY = 'images/customers/identities/';
    const IMAGES_FLAG = 'images/flags/';
    const IMAGES_PROPERTY_BUILDING = 'images/properties/buildings/';
    const IMAGES_PROPERTY_BUILDING_IMB = 'images/properties/buildings/IMB/';
    const IMAGES_PROPERTY_BUILDING_FLOOR = 'images/properties/buildings/floors/';
    const IMAGES_PROPERTY_LAND = 'images/properties/lands/';
    const IMAGES_PROPERTY_DOCUMENT = 'images/properties/documents/';
    const IMAGES_PROPERTY_SEO = 'images/properties/SEO/';
    const IMAGES_PROPERTY_AREA = 'images/properties/areas/';
    const IMAGES_PROPERTY_PUBLIC_PLACE = 'images/properties/publicPlaces/';
    const IMAGES_PROPERTY_STATUS = 'images/properties/statuses/';
    const IMAGES_BLOG = 'images/blogs/';
    const IMAGES_PAGE = 'images/pages/';
    const IMAGES_HOMEPAGE = 'images/homepages/';
    const IMAGES_PIPELINE = 'images/pipelines/';

    const IMAGES_MEDIA_PARTNER = 'images/media-partners/';
    const IMAGES_TLT_REVIEW = 'images/tlt-reviews/';
    const IMAGES_BOAT = 'images/boats/';
    const IMAGES_BOAT_PROMO = 'images/boats/promos/';
    const FILES_BOAT        = 'files/boats/';

    const IMAGES_EXPERIENCE = 'images/experiences/';
    const PDF_EXPERIENCE = 'PDFs/experiences/';

    /*
     |--------------------------------------------------------------------------
     | IMAGES
     |-------------------------------------------------------------------------
     */
    const PDF_PROPERTY = 'PDFs/properties/';
    const PDF_PROPERTY_AGREEMENT = 'PDFs/properties/agreements/';

    /*
     |--------------------------------------------------------------------------
     | IMAGE PATH
     |-------------------------------------------------------------------------
     */

    public static function IMAGES_LANGUAGE_STORAGE_PUBLIC_PATH()
    {
        return storage_path(self::STORAGE_PUBLIC_BASE . self::IMAGES_LANGUAGE);
    }

    public static function IMAGES_MEDIA_PARTNER_STORAGE_PUBLIC_PATH()
    {
        return storage_path(self::STORAGE_PUBLIC_BASE . self::IMAGES_MEDIA_PARTNER);
    }

    public static function IMAGES_TLT_REVIEW_STORAGE_PUBLIC_PATH()
    {
        return storage_path(self::STORAGE_PUBLIC_BASE . self::IMAGES_TLT_REVIEW);
    }

    public static function IMAGES_BOAT_STORAGE_PUBLIC_PATH()
    {
        return storage_path(self::STORAGE_PUBLIC_BASE . self::IMAGES_BOAT);
    }

    public static function IMAGES_BOAT_PROMO_STORAGE_PUBLIC_PATH()
    {
        return storage_path(self::STORAGE_PUBLIC_BASE . self::IMAGES_BOAT_PROMO);
    }

    public static function FILES_BOAT_STORAGE_PUBLIC_PATH()
    {
        return storage_path(self::STORAGE_PUBLIC_BASE . self::FILES_BOAT);
    }
    public static function IMAGES_BLOG_STORAGE_PUBLIC_PATH()
    {
        return storage_path(self::STORAGE_PUBLIC_BASE . self::IMAGES_BLOG);
    }


    public static function IMAGES_EXPERIENCE_STORAGE_PUBLIC_PATH()
    {
        return storage_path(self::STORAGE_PUBLIC_BASE . self::IMAGES_EXPERIENCE);
    }

    public static function PDF_EXPERIENCE_STORAGE_PUBLIC_PATH()
    {
        return storage_path(self::STORAGE_PUBLIC_BASE . self::PDF_EXPERIENCE);
    }
    /*
     |--------------------------------------------------------------------------
     | LANGUAGE PATH
     |-------------------------------------------------------------------------
     */
    public static function LANG_STORAGE_PUBLIC_PATH($locale)
    {
        return storage_path(self::STORAGE_PUBLIC_BASE . self::LANG . $locale . '/');
    }
}
