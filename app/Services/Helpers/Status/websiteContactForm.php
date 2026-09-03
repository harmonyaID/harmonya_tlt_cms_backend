<?php

if (!function_exists("errWebsiteContactFormGet")) {
    function errWebsiteContactFormGet($internalMsg = "")
    {
        error(404, "Contact form not found", $internalMsg);
    }
}

if (!function_exists("errWebsiteContactFormSave")) {
    function errWebsiteContactFormSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save contact form", $internalMsg);
    }
}

if (!function_exists("errWebsiteContactFormUpdate")) {
    function errWebsiteContactFormUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update contact form", $internalMsg);
    }
}

if (!function_exists("errWebsiteContactFormDelete")) {
    function errWebsiteContactFormDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete contact form", $internalMsg);
    }
}