<?php

if (!function_exists("errRedirectionGet")) {
    function errRedirectionGet($internalMsg = "")
    {
        error(404, "Redirection not found", $internalMsg);
    }
}

if (!function_exists("errRedirectionSave")) {
    function errRedirectionSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save redirection", $internalMsg);
    }
}

if (!function_exists("errRedirectionUpdate")) {
    function errRedirectionUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update redirection", $internalMsg);
    }
}

if (!function_exists("errRedirectionDelete")) {
    function errRedirectionDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete redirection", $internalMsg);
    }
}