<?php

if (!function_exists("errMenuGet")) {
    function errMenuGet($internalMsg = "")
    {
        error(404, "Menu not found", $internalMsg);
    }
}

if (!function_exists("errMenuSave")) {
    function errMenuSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save menu", $internalMsg);
    }
}

if (!function_exists("errMenuUpdate")) {
    function errMenuUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update menu", $internalMsg);
    }
}

if (!function_exists("errMenuDelete")) {
    function errMenuDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete menu", $internalMsg);
    }
}