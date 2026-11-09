<?php

if (!function_exists("errOfferGet")) {
    function errOfferGet($internalMsg = "")
    {
        error(404, "Offer not found", $internalMsg);
    }
}

if (!function_exists("errOfferSave")) {
    function errOfferSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save offer", $internalMsg);
    }
}

if (!function_exists("errOfferUpdate")) {
    function errOfferUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update offer", $internalMsg);
    }
}

if (!function_exists("errOfferDelete")) {
    function errOfferDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete offer", $internalMsg);
    }
}

if (!function_exists("errOfferCategoryGet")) {
    function errOfferCategoryGet($internalMsg = "")
    {
        error(404, "Offer category not found", $internalMsg);
    }
}

if (!function_exists("errOfferCategorySave")) {
    function errOfferCategorySave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save offer category", $internalMsg);
    }
}

if (!function_exists("errOfferCategoryDelete")) {
    function errOfferCategoryDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete offer category", $internalMsg);
    }
}

if (!function_exists("errOfferTagGet")) {
    function errOfferTagGet($internalMsg = "")
    {
        error(404, "Offer tag not found", $internalMsg);
    }
}

if (!function_exists("errOfferTagSave")) {
    function errOfferTagSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save offer tag", $internalMsg);
    }
}

if (!function_exists("errOfferTagDelete")) {
    function errOfferTagDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete offer tag", $internalMsg);
    }
}
