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
