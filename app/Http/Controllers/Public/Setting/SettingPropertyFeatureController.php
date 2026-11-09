<?php

namespace App\Http\Controllers\Public\Setting;

use App\Http\Controllers\Controller;
use App\Models\Setting\SettingPropertyFeature;
use App\Parser\Setting\SettingPropertyFeatureParser;
use Illuminate\Http\Request;

class SettingPropertyFeatureController extends Controller
{
    public function get(Request $request)
    {
        $features = SettingPropertyFeature::filter($request)->getOrPaginate($request);
        return success(SettingPropertyFeatureParser::briefs($features), pagination: pagination($features));
    }
}
