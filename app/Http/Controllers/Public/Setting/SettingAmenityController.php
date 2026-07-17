<?php

namespace App\Http\Controllers\Public\Setting;

use App\Http\Controllers\Controller;
use App\Models\Setting\SettingAmenity;
use App\Parser\Setting\SettingAmenityParser;
use Illuminate\Http\Request;

class SettingAmenityController extends Controller
{
    public function get(Request $request)
    {
        $amenities = SettingAmenity::filter($request)->getOrPaginate($request);
        return success(SettingAmenityParser::briefs($amenities), pagination: pagination($amenities));
    }
}
