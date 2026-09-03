<?php

if (!function_exists("errLanguageGet")) {
    function errLanguageGet($internalMsg = "")
    {
        error(404, "Language not found", $internalMsg);
    }
}

if (!function_exists("errLanguageSave")) {
    function errLanguageSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save language", $internalMsg);
    }
}

if (!function_exists("errLanguageUpdate")) {
    function errLanguageUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update language", $internalMsg);
    }
}

if (!function_exists("errLanguageDelete")) {
    function errLanguageDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete language", $internalMsg);
    }
}

if (!function_exists('errLanguageAlreadyExists')) {
    function errLanguageAlreadyExists($internalMsg = "", $status = 500)
    {
        error($status, "language already exists", $internalMsg);
    }
}

if (!function_exists('errLanguageGroupIsExists')) {
    function errLanguageGroupIsExists($internalMsg = "", $status = 500)
    {
        error($status, "group already exists", $internalMsg);
    }
}

if (!function_exists("errLanguageGroupSave")) {
    function errLanguageGroupSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save language group", $internalMsg);
    }
}
