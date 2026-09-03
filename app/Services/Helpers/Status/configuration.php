<?php

if (!function_exists("errWebsiteInformationGet")) {
    function errWebsiteInformationGet($internalMsg = "")
    {
        error(404, "Website Information not found", $internalMsg);
    }
}

if (!function_exists("errWebsiteInformationSave")) {
    function errWebsiteInformationSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save website information", $internalMsg);
    }
}
