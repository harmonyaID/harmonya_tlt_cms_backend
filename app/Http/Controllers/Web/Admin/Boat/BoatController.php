<?php

namespace App\Http\Controllers\Web\Admin\Boat;

use App\Algorithms\Boat\BoatAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Boat\BoatRequest;
use App\Models\Boat\Boat;
use App\Parser\Boat\BoatParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class BoatController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            // VIEW
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BOAT_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            // CREATE
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BOAT_CREATE);
                return $next($request);
            })->only(['create']);

            // UPDATE
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BOAT_UPDATE);
                return $next($request);
            })->only(['update']);

            // DELETE
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BOAT_DELETE);
                return $next($request);
            })->only(['delete']);
        }
    }

    public function get(Request $request)
    {
        $boats = Boat::filter($request)->with(['type', 'photos', 'customInformations', 'seo', 'acf'])->getOrPaginate($request);
        return success(BoatParser::briefs($boats), pagination: pagination($boats));
    }

    public function detail($id)
    {
        $boat = Boat::with(['type', 'photos', 'customInformations', 'seo', 'acf'])->find($id);
        if (!$boat) errBoatGet();

        return success(BoatParser::first($boat));
    }

    public function create(BoatRequest $request)
    {
        $algo = new BoatAlgo();
        return $algo->create($request);
    }

    public function update($id, BoatRequest $request)
    {
        $algo = new BoatAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new BoatAlgo((int)$id);
        return $algo->delete();
    }
}
