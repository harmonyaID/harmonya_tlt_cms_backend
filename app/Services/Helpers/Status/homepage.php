<?php

if (!function_exists("errHomepageGet")) {
    function errHomepageGet($internalMsg = "")
    {
        error(404, "Homepage not found", $internalMsg);
    }
}

if (!function_exists("errHomepageSave")) {
    function errHomepageSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save homepage", $internalMsg);
    }
}

if (!function_exists("errHomepageUpdate")) {
    function errHomepageUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update homepage", $internalMsg);
    }
}
