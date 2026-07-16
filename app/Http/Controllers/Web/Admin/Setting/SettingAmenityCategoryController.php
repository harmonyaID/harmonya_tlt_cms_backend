<?php

namespace App\Http\Controllers\Web\Admin\Setting;

use App\Algorithms\Setting\SettingAmenityCategoryAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\SettingAmenityCategoryRequest;
use App\Models\Setting\SettingAmenityCategory;
use App\Parser\Setting\SettingAmenityCategoryParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class SettingAmenityCategoryController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            // VIEW
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_AMENITY_CATEGORY_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            // CREATE
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_AMENITY_CATEGORY_CREATE);
                return $next($request);
            })->only(['create']);

            // UPDATE
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_AMENITY_CATEGORY_UPDATE);
                return $next($request);
            })->only(['update']);

            // DELETE
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_AMENITY_CATEGORY_DELETE);
                return $next($request);
            })->only(['delete']);

        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function get(Request $request)
    {
        $categories = SettingAmenityCategory::filter($request)->getOrPaginate($request);
        return success(SettingAmenityCategoryParser::briefs($categories), pagination: pagination($categories));
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function detail($id)
    {
        $category = SettingAmenityCategory::find($id);
        if (!$category) {
            errAmenityCategoryGet();
        }

        return success(SettingAmenityCategoryParser::first($category));
    }

    /**
     * @param SettingAmenityCategoryRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function create(SettingAmenityCategoryRequest $request)
    {
        $algo = new SettingAmenityCategoryAlgo();
        return $algo->create($request);
    }

    /**
     * @param $id
     * @param SettingAmenityCategoryRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function update($id, SettingAmenityCategoryRequest $request)
    {
        $algo = new SettingAmenityCategoryAlgo((int)$id);
        return $algo->update($request);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function delete($id)
    {
        $algo = new SettingAmenityCategoryAlgo((int)$id);
        return $algo->delete();
    }
}