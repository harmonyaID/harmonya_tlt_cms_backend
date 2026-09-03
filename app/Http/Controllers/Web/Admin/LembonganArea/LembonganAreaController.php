<?php

namespace App\Http\Controllers\Web\Admin\LembonganArea;

use App\Algorithms\LembonganArea\LembonganAreaAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\LembonganArea\LembonganAreaRequest;
use App\Models\LembonganArea\LembonganArea;
use App\Parser\LembonganArea\LembonganAreaParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class LembonganAreaController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_LEMBONGAN_AREA_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_LEMBONGAN_AREA_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_LEMBONGAN_AREA_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_LEMBONGAN_AREA_DELETE);
                return $next($request);
            })->only(['delete']);

        }
    }

    public function get(Request $request)
    {
        $areas = LembonganArea::filter($request)->getOrPaginate($request);
        return success(LembonganAreaParser::briefs($areas), pagination: pagination($areas));
    }

    public function detail($id)
    {
        $area = LembonganArea::find($id);
        if (!$area) {
            errLembonganAreaGet();
        }

        return success(LembonganAreaParser::first($area));
    }

    public function create(LembonganAreaRequest $request)
    {
        $algo = new LembonganAreaAlgo();
        return $algo->create($request);
    }

    public function update($id, LembonganAreaRequest $request)
    {
        $algo = new LembonganAreaAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new LembonganAreaAlgo((int)$id);
        return $algo->delete();
    }
}
