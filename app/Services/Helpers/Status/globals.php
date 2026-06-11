<?php

if (!function_exists("errDefault")) {
    function errDefault($internalMsg = "")
    {
        error(500, "An error occurred!", $internalMsg);
    }
}

if (!function_exists("errThirdPartyPrivateAPIClientInvalid")) {
    function errThirdPartyPrivateAPIClientInvalid($internalMsg = "")
    {
        error(500, "Calling external api is invalid!", $internalMsg);
    }
}
