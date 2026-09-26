<?php

namespace App\Http\Controllers\Web\Admin\Setting;

use App\Algorithms\Setting\SettingAnalyticsAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\SettingAnalyticsRequest;
use App\Models\Setting\SettingAnalytic;
use App\Parser\Setting\SettingAnalyticsParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class SettingAnalyticsController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_SETTING_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_SETTING_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_SETTING_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_SETTING_DELETE);
                return $next($request);
            })->only(['delete']);
        }
    }

    public function get(Request $request)
    {
        $analytics = SettingAnalytic::filter($request)
            ->getOrPaginate($request);

        return success(
            SettingAnalyticsParser::briefs($analytics),
            pagination: pagination($analytics)
        );
    }

    public function detail($id)
    {
        $analytics = SettingAnalytic::find($id);

        if (!$analytics) {
            errSettingAnalyticsGet();
        }

        return success(
            SettingAnalyticsParser::first($analytics)
        );
    }

    public function create(SettingAnalyticsRequest $request)
    {
        $algo = new SettingAnalyticsAlgo();

        return $algo->create($request);
    }

    public function update($id, SettingAnalyticsRequest $request)
    {
        $algo = new SettingAnalyticsAlgo((int) $id);

        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new SettingAnalyticsAlgo((int) $id);

        return $algo->delete();
    }
}