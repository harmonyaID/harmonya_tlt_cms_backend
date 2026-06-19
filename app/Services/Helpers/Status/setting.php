<?php

if (!function_exists("errSettingGet")) {
    function errSettingGet($internalMsg = "")
    {
        error(404, "Setting not found", $internalMsg);
    }
}

if (!function_exists("errSettingUpdate")) {
    function errSettingUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update country", $internalMsg);
    }
}

if (!function_exists("errSettingCountryGet")) {
    function errSettingCountryGet($internalMsg = "")
    {
        error(404, "Country not found", $internalMsg);
    }
}

if (!function_exists("errSettingCountrySave")) {
    function errSettingCountrySave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save country", $internalMsg);
    }
}

if (!function_exists("errSettingCountryUpdate")) {
    function errSettingCountryUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update country", $internalMsg);
    }
}

if (!function_exists("errSettingCountryDelete")) {
    function errSettingCountryDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete country", $internalMsg);
    }
}
