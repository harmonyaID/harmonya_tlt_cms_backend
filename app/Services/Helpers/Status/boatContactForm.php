<?php

if (!function_exists("errBoatContactFormGet")) {
    function errBoatContactFormGet($internalMsg = "")
    {
        error(404, "Boat contact form not found", $internalMsg);
    }
}

if (!function_exists("errBoatContactFormSave")) {
    function errBoatContactFormSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save boat contact form", $internalMsg);
    }
}

if (!function_exists("errBoatContactFormDelete")) {
    function errBoatContactFormDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete boat contact form", $internalMsg);
    }
}