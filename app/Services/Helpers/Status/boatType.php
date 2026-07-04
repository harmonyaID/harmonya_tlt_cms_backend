<?php

if (!function_exists("errBoatTypeGet")) {
    function errBoatTypeGet($internalMsg = "")
    {
        error(404, "Boat type not found", $internalMsg);
    }
}

if (!function_exists("errBoatTypeSave")) {
    function errBoatTypeSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save boat type", $internalMsg);
    }
}

if (!function_exists("errBoatTypeUpdate")) {
    function errBoatTypeUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update boat type", $internalMsg);
    }
}

if (!function_exists("errBoatTypeDelete")) {
    function errBoatTypeDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete boat type", $internalMsg);
    }
}