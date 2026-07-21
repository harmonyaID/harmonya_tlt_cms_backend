<?php

if (!function_exists("errLembonganAreaGet")) {
    function errLembonganAreaGet($internalMsg = "")
    {
        error(404, "Lembongan area not found", $internalMsg);
    }
}

if (!function_exists("errLembonganAreaSave")) {
    function errLembonganAreaSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save Lembongan area", $internalMsg);
    }
}

if (!function_exists("errLembonganAreaUpdate")) {
    function errLembonganAreaUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update Lembongan area", $internalMsg);
    }
}

if (!function_exists("errLembonganAreaDelete")) {
    function errLembonganAreaDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete Lembongan area", $internalMsg);
    }
}
