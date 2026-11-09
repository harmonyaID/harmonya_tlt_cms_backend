<?php

namespace App\Http\Controllers\Web\Admin\Setting;

use App\Algorithms\Setting\SettingAmenityAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\SettingAmenityIconRequest;
use App\Http\Requests\Setting\SettingAmenityRequest;
use App\Models\Setting\SettingAmenity;
use App\Parser\Setting\SettingAmenityParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class SettingAmenityController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_AMENITY_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_AMENITY_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_AMENITY_UPDATE);
                return $next($request);
            })->only(['update', 'updateIcon']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_AMENITY_DELETE);
                return $next($request);
            })->only(['delete']);
        }
    }

    public function get(Request $request)
    {
        $amenities = SettingAmenity::with('category')->filter($request)->getOrPaginate($request);
        return success(SettingAmenityParser::briefs($amenities), pagination: pagination($amenities));
    }

    public function detail($id)
    {
        $amenity = SettingAmenity::find($id);
        if (!$amenity) {
            errAmenityGet();
        }

        return success(SettingAmenityParser::first($amenity));
    }

    public function create(SettingAmenityRequest $request)
    {
        $algo = new SettingAmenityAlgo();
        return $algo->create($request);
    }

    public function update($id, SettingAmenityRequest $request)
    {
        $algo = new SettingAmenityAlgo((int)$id);
        return $algo->update($request);
    }

    public function updateIcon($id, SettingAmenityIconRequest $request)
    {
        $algo = new SettingAmenityAlgo((int)$id);
        return $algo->updateIcon($request);
    }

    public function delete($id)
    {
        $algo = new SettingAmenityAlgo((int)$id);
        return $algo->delete();
    }
}
