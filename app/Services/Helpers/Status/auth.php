<?php

if (!function_exists("errAuthChangePassword")) {
    function errAuthChangePassword($internalMsg = "")
    {
        error(500, "Unable to change password!", $internalMsg);
    }
}

if (!function_exists("errAuthForgotPasswordGet")) {
    function errAuthForgotPasswordGet($internalMsg = "")
    {
        error(400, "Your token is invalid or expired!", $internalMsg);
    }
}

if (!function_exists("errAuthForgotPassword")) {
    function errAuthForgotPassword($internalMsg = "")
    {
        error(500, "Unable to generate forgot password token!", $internalMsg);
    }
}

if (!function_exists("errAuthUserGet")) {
    function errAuthUserGet($internalMsg = "")
    {
        error(404, "User not found", $internalMsg);
    }
}

if (!function_exists("errAuthAccountGet")) {
    function errAuthAccountGet($internalMsg = "")
    {
        error(404, "Account not found", $internalMsg);
    }
}

if (!function_exists("errSocialMediaUnauthenticated")) {
    function errSocialMediaUnauthenticated($internalMsg = "")
    {
        error(400, "Your social media authorization is invalid. Please re-login!", $internalMsg);
    }
}

if (!function_exists("errSocialMediaTokenGenerator")) {
    function errSocialMediaTokenGenerator($internalMsg = "")
    {
        error(500, "Failed while creating token!!", $internalMsg);
    }
}
