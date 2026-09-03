<?php

namespace App\Http\Controllers\Web\Admin\Component;

use App\Http\Controllers\Controller;
use App\Models\Setting\SettingCountry;
use Illuminate\Http\Request;

class ComponentCountryController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function get(Request $request)
    {
        $countries = SettingCountry::filter($request)->orderBy('name', 'ASC')->get();
        return success($countries);
    }

}
