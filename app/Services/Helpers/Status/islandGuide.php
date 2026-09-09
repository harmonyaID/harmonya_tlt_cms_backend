<?php

if (!function_exists("errIslandGuideGet")) {
    function errIslandGuideGet($internalMsg = "")
    {
        error(404, "IslandGuide not found", $internalMsg);
    }
}

if (!function_exists("errIslandGuideSave")) {
    function errIslandGuideSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save island guide", $internalMsg);
    }
}

if (!function_exists("errIslandGuideUpdate")) {
    function errIslandGuideUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update island guide", $internalMsg);
    }
}

if (!function_exists("errIslandGuideDelete")) {
    function errIslandGuideDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete island guide", $internalMsg);
    }
}

if (!function_exists("errIslandGuideAreaGet")) {
    function errIslandGuideAreaGet($internalMsg = "")
    {
        error(404, "IslandGuide area not found", $internalMsg);
    }
}

if (!function_exists("errIslandGuideAreaSave")) {
    function errIslandGuideAreaSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save island guide category", $internalMsg);
    }
}

if (!function_exists("errIslandGuideAreaUpdate")) {
    function errIslandGuideAreaUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update island guide category", $internalMsg);
    }
}

if (!function_exists("errIslandGuideAreaDelete")) {
    function errIslandGuideAreaDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete island guide category", $internalMsg);
    }
}

if (!function_exists("errIslandGuideTypeGet")) {
    function errIslandGuideTypeGet($internalMsg = "")
    {
        error(404, "IslandGuide type not found", $internalMsg);
    }
}

if (!function_exists("errIslandGuideTypeSave")) {
    function errIslandGuideTypeSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save island guide type", $internalMsg);
    }
}

if (!function_exists("errIslandGuideTypeUpdate")) {
    function errIslandGuideTypeUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update island guide type", $internalMsg);
    }
}

if (!function_exists("errIslandGuideTypeDelete")) {
    function errIslandGuideTypeDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete island guide type", $internalMsg);
    }
}

if (!function_exists("errIslandGuideInquiryFormGet")) {
    function errIslandGuideInquiryFormGet($internalMsg = "")
    {
        error(404, "Inquiry form not found", $internalMsg);
    }
}

if (!function_exists("errIslandGuideInquiryFormSave")) {
    function errIslandGuideInquiryFormSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save inquiry form", $internalMsg);
    }
}

if (!function_exists("errIslandGuideInquiryFormDelete")) {
    function errIslandGuideInquiryFormDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete inquiry form", $internalMsg);
    }
}