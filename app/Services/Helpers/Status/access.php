<?php

if (!function_exists("errAccessRoleGet")) {
    function errAccessRoleGet($internalMsg = "")
    {
        error(404, "Role not found", $internalMsg);
    }
}

if (!function_exists("errAccessRoleUpdate")) {
    function errAccessRoleUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update role", $internalMsg);
    }
}

if (!function_exists("errAccessRolePermissionMapping")) {
    function errAccessRolePermissionMapping($internalMsg = "", $status = 500)
    {
        error($status, "Unable to mapping role & permission", $internalMsg);
    }
}
