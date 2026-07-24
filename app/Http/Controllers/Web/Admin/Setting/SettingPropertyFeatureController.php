<?php

namespace App\Http\Controllers\Web\Admin\Setting;

use App\Algorithms\Setting\SettingPropertyFeatureAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\SettingPropertyFeatureRequest;
use App\Models\Setting\SettingPropertyFeature;
use App\Parser\Setting\SettingPropertyFeatureParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class SettingPropertyFeatureController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_FEATURE_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_FEATURE_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_FEATURE_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_FEATURE_DELETE);
                return $next($request);
            })->only(['delete']);
        }
    }

    public function get(Request $request)
    {
        $features = SettingPropertyFeature::filter($request)->getOrPaginate($request);
        return success(SettingPropertyFeatureParser::briefs($features), pagination: pagination($features));
    }

    public function detail($id)
    {
        $feature = SettingPropertyFeature::find($id);
        if (!$feature) {
            errPropertyFeatureGet();
        }

        return success(SettingPropertyFeatureParser::first($feature));
    }

    public function create(SettingPropertyFeatureRequest $request)
    {
        $algo = new SettingPropertyFeatureAlgo();
        return $algo->create($request);
    }

    public function update($id, SettingPropertyFeatureRequest $request)
    {
        $algo = new SettingPropertyFeatureAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new SettingPropertyFeatureAlgo((int)$id);
        return $algo->delete();
    }
}
