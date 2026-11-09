<?php

if (!function_exists("errStaffGet")) {
    function errStaffGet($internalMsg = "")
    {
        error(404, "Staff not found", $internalMsg);
    }
}

if (!function_exists("errStaffSave")) {
    function errStaffSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save staff", $internalMsg);
    }
}

if (!function_exists("errStaffUpdate")) {
    function errStaffUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update staff", $internalMsg);
    }
}

if (!function_exists("errStaffDelete")) {
    function errStaffDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete staff", $internalMsg);
    }
}

if (!function_exists("errStaffUserSave")) {
    function errStaffUserSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save staff user login", $internalMsg);
    }
}

if (!function_exists("errStaffUserDelete")) {
    function errStaffUserDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete staff user login", $internalMsg);
    }
}
