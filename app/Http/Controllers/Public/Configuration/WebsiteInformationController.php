<?php

namespace App\Http\Controllers\Public\Configuration;

use App\Http\Controllers\Controller;
use App\Models\Configuration\WebsiteInformation;
use App\Parser\Configuration\WebsiteInformationParser;

class WebsiteInformationController extends Controller
{
    public function get()
    {
        $information = WebsiteInformation::first();
        if (!$information) {
            errWebsiteInformationGet();
        }

        return success(WebsiteInformationParser::first($information));
    }
}
