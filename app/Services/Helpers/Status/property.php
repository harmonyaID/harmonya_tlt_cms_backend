<?php

if (!function_exists("errPropertyTypeGet")) {
    function errPropertyTypeGet($internalMsg = "")
    {
        error(404, "Property type not found", $internalMsg);
    }
}

if (!function_exists("errPropertyTypeSave")) {
    function errPropertyTypeSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save property type", $internalMsg);
    }
}

if (!function_exists("errPropertyTypeUpdate")) {
    function errPropertyTypeUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update property type", $internalMsg);
    }
}

if (!function_exists("errPropertyTypeDelete")) {
    function errPropertyTypeDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete property type", $internalMsg);
    }
}

if (!function_exists("errPropertyRoomTypeGet")) {
    function errPropertyRoomTypeGet($internalMsg = "")
    {
        error(404, "Property room type not found", $internalMsg);
    }
}

if (!function_exists("errPropertyRoomTypeSave")) {
    function errPropertyRoomTypeSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save property room type", $internalMsg);
    }
}

if (!function_exists("errPropertyRoomTypeUpdate")) {
    function errPropertyRoomTypeUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update property room type", $internalMsg);
    }
}

if (!function_exists("errPropertyRoomTypeDelete")) {
    function errPropertyRoomTypeDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete property room type", $internalMsg);
    }
}

if (!function_exists("errPropertyBedTypeGet")) {
    function errPropertyBedTypeGet($internalMsg = "")
    {
        error(404, "Property bed type not found", $internalMsg);
    }
}

if (!function_exists("errPropertyBedTypeSave")) {
    function errPropertyBedTypeSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save property bed type", $internalMsg);
    }
}

if (!function_exists("errPropertyBedTypeUpdate")) {
    function errPropertyBedTypeUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update property bed type", $internalMsg);
    }
}

if (!function_exists("errPropertyBedTypeDelete")) {
    function errPropertyBedTypeDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete property bed type", $internalMsg);
    }
}