<?php

if (!function_exists("errTeamMemberGet")) {
    function errTeamMemberGet($internalMsg = "")
    {
        error(404, "Team member not found", $internalMsg);
    }
}

if (!function_exists("errTeamMemberSave")) {
    function errTeamMemberSave($internalMsg = "", $status = 500)
    {
        error($status, "Unable to save team member", $internalMsg);
    }
}

if (!function_exists("errTeamMemberUpdate")) {
    function errTeamMemberUpdate($internalMsg = "", $status = 500)
    {
        error($status, "Unable to update team member", $internalMsg);
    }
}

if (!function_exists("errTeamMemberDelete")) {
    function errTeamMemberDelete($internalMsg = "", $status = 500)
    {
        error($status, "Unable to delete team member", $internalMsg);
    }
}
