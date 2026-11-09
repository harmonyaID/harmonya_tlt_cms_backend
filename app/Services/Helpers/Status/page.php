<?php

if (!function_exists("errPageGet")) {
    function errPageGet($internalMsg = "")
    {
        error(404, "page not found", $internalMsg);
    }
}

if (!function_exists("errPageSave")) {
    function errPageSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save page", $internalMsg);
    }
}

if (!function_exists("errPageUpdate")) {
    function errPageUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update page", $internalMsg);
    }
}

if (!function_exists("errPageDelete")) {
    function errPageDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete page", $internalMsg);
    }
}
