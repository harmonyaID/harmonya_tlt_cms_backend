<?php

namespace App\Http\Controllers\Web\Admin\Setting;

use App\Algorithms\Setting\SettingAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\SettingRequest;
use App\Models\Setting\Setting;
use App\Parser\Setting\SettingParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_SETTING_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_SETTING_UPDATE);
                return $next($request);
            })->only(['update']);

        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function get(Request $request)
    {
        $settings = Setting::filter($request)->getOrPaginate($request);
        return success(SettingParser::briefs($settings), pagination: pagination($settings));
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function detail($id)
    {
        $setting = Setting::find($id);
        if (!$setting) {
            errSettingGet();
        }

        return success(SettingParser::first($setting));
    }

    /**
     * @param $id
     * @param SettingRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function update($id, SettingRequest $request)
    {
        $algo = new SettingAlgo((int)$id);
        return $algo->update($request);
    }
}
