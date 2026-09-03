<?php

if (!function_exists("errExperienceGet")) {
    function errExperienceGet($internalMsg = "")
    {
        error(404, "Experience not found", $internalMsg);
    }
}

if (!function_exists("errExperienceSave")) {
    function errExperienceSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save experience", $internalMsg);
    }
}

if (!function_exists("errExperienceUpdate")) {
    function errExperienceUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update experience", $internalMsg);
    }
}

if (!function_exists("errExperienceDelete")) {
    function errExperienceDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete experience", $internalMsg);
    }
}

if (!function_exists("errExperienceAreaGet")) {
    function errExperienceAreaGet($internalMsg = "")
    {
        error(404, "Experience area not found", $internalMsg);
    }
}

if (!function_exists("errExperienceAreaSave")) {
    function errExperienceAreaSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save experience category", $internalMsg);
    }
}

if (!function_exists("errExperienceAreaUpdate")) {
    function errExperienceAreaUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update experience category", $internalMsg);
    }
}

if (!function_exists("errExperienceAreaDelete")) {
    function errExperienceAreaDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete experience category", $internalMsg);
    }
}

if (!function_exists("errExperienceTypeGet")) {
    function errExperienceTypeGet($internalMsg = "")
    {
        error(404, "Experience type not found", $internalMsg);
    }
}

if (!function_exists("errExperienceTypeSave")) {
    function errExperienceTypeSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save experience type", $internalMsg);
    }
}

if (!function_exists("errExperienceTypeUpdate")) {
    function errExperienceTypeUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update experience type", $internalMsg);
    }
}

if (!function_exists("errExperienceTypeDelete")) {
    function errExperienceTypeDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete experience type", $internalMsg);
    }
}

if (!function_exists("errExperienceInquiryFormGet")) {
    function errExperienceInquiryFormGet($internalMsg = "")
    {
        error(404, "Inquiry form not found", $internalMsg);
    }
}

if (!function_exists("errExperienceInquiryFormSave")) {
    function errExperienceInquiryFormSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save inquiry form", $internalMsg);
    }
}

if (!function_exists("errExperienceInquiryFormDelete")) {
    function errExperienceInquiryFormDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete inquiry form", $internalMsg);
    }
}