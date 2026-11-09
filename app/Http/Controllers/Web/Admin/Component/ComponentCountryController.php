<?php

namespace App\Http\Controllers\Web\Admin\Component;

use App\Http\Controllers\Controller;
use App\Models\Setting\SettingCountry;
use Illuminate\Http\Request;

class ComponentCountryController extends Controller
{
    public function get(Request $request)
    {
        $countries = SettingCountry::filter($request)->orderBy('name', 'ASC')->get();
        return success($countries);
    }
}
