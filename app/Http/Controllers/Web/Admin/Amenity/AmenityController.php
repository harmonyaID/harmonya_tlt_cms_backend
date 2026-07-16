<?php

namespace App\Http\Controllers\Web\Admin\Amenity;

use App\Algorithms\Amenity\AmenityAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Amenity\AmenityRequest;
use App\Models\Amenity\Amenity;
use App\Parser\Amenity\AmenityParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class AmenityController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            // VIEW
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_AMENITY_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            // CREATE
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_AMENITY_CREATE);
                return $next($request);
            })->only(['create']);

            // UPDATE
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_AMENITY_UPDATE);
                return $next($request);
            })->only(['update']);

            // DELETE
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_AMENITY_DELETE);
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
        $amenities = Amenity::filter($request)->getOrPaginate($request);
        return success(AmenityParser::briefs($amenities), pagination: pagination($amenities));
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function detail($id)
    {
        $amenity = Amenity::find($id);
        if (!$amenity) {
            errAmenityGet();
        }

        return success(AmenityParser::first($amenity));
    }

    /**
     * @param AmenityRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function create(AmenityRequest $request)
    {
        $algo = new AmenityAlgo();
        return $algo->create($request);
    }

    /**
     * @param $id
     * @param AmenityRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function update($id, AmenityRequest $request)
    {
        $algo = new AmenityAlgo((int)$id);
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
        $algo = new AmenityAlgo((int)$id);
        return $algo->delete();
    }
}