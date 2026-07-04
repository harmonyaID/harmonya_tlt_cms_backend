<?php

namespace App\Http\Controllers\Web\Admin\Boat;

use App\Algorithms\Boat\BoatTypeAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Boat\BoatTypeRequest;
use App\Models\Boat\BoatType;
use App\Parser\Boat\BoatTypeParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class BoatTypeController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BOAT_TYPE_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BOAT_TYPE_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BOAT_TYPE_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BOAT_TYPE_DELETE);
                return $next($request);
            })->only(['delete']);

        }
    }

    public function get(Request $request)
    {
        $types = BoatType::filter($request)->with('boat')->getOrPaginate($request);
        return success(BoatTypeParser::briefs($types), pagination: pagination($types));
    }

    public function detail($id)
    {
        $type = BoatType::with('boat')->find($id);
        if (!$type) {
            errBoatTypeGet();
        }

        return success(BoatTypeParser::first($type));
    }

    public function create(BoatTypeRequest $request)
    {
        $algo = new BoatTypeAlgo();
        return $algo->create($request);
    }

    public function update($id, BoatTypeRequest $request)
    {
        $algo = new BoatTypeAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new BoatTypeAlgo((int)$id);
        return $algo->delete();
    }
}