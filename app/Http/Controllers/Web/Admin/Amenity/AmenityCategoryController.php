<?php

namespace App\Http\Controllers\Web\Admin\Amenity;

use App\Algorithms\Amenity\AmenityCategoryAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Amenity\AmenityCategoryRequest;
use App\Models\Amenity\AmenityCategory;
use App\Parser\Amenity\AmenityCategoryParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class AmenityCategoryController extends Controller
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
        $categories = AmenityCategory::filter($request)->getOrPaginate($request);
        return success(AmenityCategoryParser::briefs($categories), pagination: pagination($categories));
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function detail($id)
    {
        $category = AmenityCategory::find($id);
        if (!$category) {
            errAmenityCategoryGet();
        }

        return success(AmenityCategoryParser::first($category));
    }

    /**
     * @param AmenityCategoryRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function create(AmenityCategoryRequest $request)
    {
        $algo = new AmenityCategoryAlgo();
        return $algo->create($request);
    }

    /**
     * @param $id
     * @param AmenityCategoryRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function update($id, AmenityCategoryRequest $request)
    {
        $algo = new AmenityCategoryAlgo((int)$id);
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
        $algo = new AmenityCategoryAlgo((int)$id);
        return $algo->delete();
    }
}