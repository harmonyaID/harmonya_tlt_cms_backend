<?php

if (!function_exists("errTltReviewGet")) {
    function errTltReviewGet($internalMsg = "")
    {
        error(404, "Review not found", $internalMsg);
    }
}

if (!function_exists("errTltReviewSave")) {
    function errTltReviewSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save review", $internalMsg);
    }
}

if (!function_exists("errTltReviewUpdate")) {
    function errTltReviewUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update review", $internalMsg);
    }
}

if (!function_exists("errTltReviewDelete")) {
    function errTltReviewDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete review", $internalMsg);
    }
}