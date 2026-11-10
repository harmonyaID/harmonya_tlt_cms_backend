<?php

namespace App\Services\Constant\Notification;

class NotificationType
{
    const BOAT_INQUIRY = 'boat_inquiry';
    const WEBSITE_CONTACT_FORM = 'website_contact_form';
    const PROPERTY_INQUIRY = 'property_inquiry';
    const PROPERTY_CONTACT_FORM = 'property_contact_form';
    const EXPERIENCE_INQUIRY = 'experience_inquiry';
    const PROPERTY_REVIEW = 'property_review';

    const OPTION = [
        self::BOAT_INQUIRY => 'Boat Inquiry',
        self::WEBSITE_CONTACT_FORM => 'Website Contact Form',
        self::PROPERTY_INQUIRY => 'Property Inquiry',
        self::PROPERTY_CONTACT_FORM => 'Property Contact Form',
        self::EXPERIENCE_INQUIRY => 'Experience Inquiry',
        self::PROPERTY_REVIEW => 'Property Review',
    ];
}
