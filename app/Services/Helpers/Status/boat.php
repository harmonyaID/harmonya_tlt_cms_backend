<?php

if (!function_exists("errBoatGet")) {
    function errBoatGet($internalMsg = "")
    {
        error(404, "Boat not found", $internalMsg);
    }
}

if (!function_exists("errBoatSave")) {
    function errBoatSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save boat", $internalMsg);
    }
}

if (!function_exists("errBoatUpdate")) {
    function errBoatUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update boat", $internalMsg);
    }
}

if (!function_exists("errBoatDelete")) {
    function errBoatDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete boat", $internalMsg);
    }
}