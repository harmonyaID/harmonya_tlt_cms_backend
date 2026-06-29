<?php

if (!function_exists("errFaqGet")) {
    function errFaqGet($internalMsg = "")
    {
        error(404, "FAQ not found", $internalMsg);
    }
}

if (!function_exists("errFaqSave")) {
    function errFaqSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save FAQ", $internalMsg);
    }
}

if (!function_exists("errFaqUpdate")) {
    function errFaqUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update FAQ", $internalMsg);
    }
}

if (!function_exists("errFaqDelete")) {
    function errFaqDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete FAQ", $internalMsg);
    }
}