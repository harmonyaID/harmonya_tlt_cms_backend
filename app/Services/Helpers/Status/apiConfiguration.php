<?php

if (!function_exists("errApiConfigurationGet")) {
    function errApiConfigurationGet($internalMsg = "")
    {
        error(404, "API configuration not found", $internalMsg);
    }
}

if (!function_exists("errApiConfigurationSave")) {
    function errApiConfigurationSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save API configuration", $internalMsg);
    }
}

if (!function_exists("errApiConfigurationUpdate")) {
    function errApiConfigurationUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update API configuration", $internalMsg);
    }
}

if (!function_exists("errApiConfigurationDelete")) {
    function errApiConfigurationDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete API configuration", $internalMsg);
    }
}
