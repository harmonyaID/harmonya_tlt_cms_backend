<?php

if (!function_exists("errMediaPartnerGet")) {
    function errMediaPartnerGet($internalMsg = "")
    {
        error(404, "Media partner not found", $internalMsg);
    }
}

if (!function_exists("errMediaPartnerSave")) {
    function errMediaPartnerSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save media partner", $internalMsg);
    }
}

if (!function_exists("errMediaPartnerUpdate")) {
    function errMediaPartnerUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update media partner", $internalMsg);
    }
}

if (!function_exists("errMediaPartnerDelete")) {
    function errMediaPartnerDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete media partner", $internalMsg);
    }
}