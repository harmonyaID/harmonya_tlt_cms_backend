<?php

namespace App\Http\Controllers\Web\Admin\Boat;

use App\Algorithms\Boat\BoatComponentTypeAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Boat\BoatComponentTypeRequest;
use App\Http\Requests\Component\ComponentRequest;
use App\Models\Boat\BoatComponentType;
use App\Parser\Boat\BoatComponentTypeParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class BoatComponentTypeController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BOAT_COMPONENT_TYPE_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BOAT_COMPONENT_TYPE_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BOAT_COMPONENT_TYPE_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BOAT_COMPONENT_TYPE_DELETE);
                return $next($request);
            })->only(['delete']);

        }
    }

    public function get(Request $request)
    {
        $types = BoatComponentType::filter($request)->getOrPaginate($request);
        return success(BoatComponentTypeParser::briefs($types), pagination: pagination($types));
    }

    public function detail($id)
    {
        $type = BoatComponentType::find($id);
        if (!$type) {
            errBoatComponentTypeGet();
        }

        return success(BoatComponentTypeParser::first($type));
    }

    public function create(ComponentRequest $request)
    {
        $algo = new BoatComponentTypeAlgo();
        return $algo->create($request);
    }

    public function update($id, ComponentRequest $request)
    {
        $algo = new BoatComponentTypeAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new BoatComponentTypeAlgo((int)$id);
        return $algo->delete();
    }
}