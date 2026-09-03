<?php

if (!function_exists("errAmenityCategoryGet")) {
    function errAmenityCategoryGet($internalMsg = "")
    {
        error(404, "Amenity category not found", $internalMsg);
    }
}

if (!function_exists("errAmenityCategorySave")) {
    function errAmenityCategorySave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save amenity category", $internalMsg);
    }
}

if (!function_exists("errAmenityCategoryUpdate")) {
    function errAmenityCategoryUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update amenity category", $internalMsg);
    }
}

if (!function_exists("errAmenityCategoryDelete")) {
    function errAmenityCategoryDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete amenity category", $internalMsg);
    }
}

if (!function_exists("errAmenityGet")) {
    function errAmenityGet($internalMsg = "")
    {
        error(404, "Amenity not found", $internalMsg);
    }
}

if (!function_exists("errAmenitySave")) {
    function errAmenitySave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save amenity", $internalMsg);
    }
}

if (!function_exists("errAmenityUpdate")) {
    function errAmenityUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update amenity", $internalMsg);
    }
}

if (!function_exists("errAmenityDelete")) {
    function errAmenityDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete amenity", $internalMsg);
    }
}