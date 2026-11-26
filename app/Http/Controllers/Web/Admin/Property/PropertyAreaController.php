<?php

namespace App\Http\Controllers\Web\Admin\Property;

use App\Algorithms\Property\PropertyAreaAlgo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasTrash;
use App\Http\Requests\Component\ComponentRequest;
use App\Models\Property\PropertyArea;
use App\Parser\Property\PropertyAreaParser;
use App\Services\Constant\Access\AccessPermissionName;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;

class PropertyAreaController extends Controller
{
    use HasTrash;

    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_AREA_VIEW);
                return $next($request);
            })->only(['get', 'detail', 'trash']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_AREA_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_AREA_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_AREA_DELETE);
                return $next($request);
            })->only(['delete', 'restore', 'forceDelete']);

        }
    }

    public function get(Request $request)
    {
        $areas = PropertyArea::filter($request)->getOrPaginate($request);
        return success(PropertyAreaParser::briefs($areas), pagination: pagination($areas));
    }

    public function detail($id)
    {
        $area = PropertyArea::find($id);
        if (!$area) {
            errPropertyAreaGet();
        }

        return success(PropertyAreaParser::first($area));
    }

    public function create(ComponentRequest $request)
    {
        $algo = new PropertyAreaAlgo();
        return $algo->create($request);
    }

    public function update($id, ComponentRequest $request)
    {
        $algo = new PropertyAreaAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new PropertyAreaAlgo((int)$id);
        return $algo->delete();
    }

    protected function trashModel(): string
    {
        return PropertyArea::class;
    }

    protected function trashParser(): string
    {
        return PropertyAreaParser::class;
    }

    protected function trashActivityType(): string
    {
        return ActivityType::PROPERTY_AREA;
    }

    protected function trashLabel($item): string
    {
        return $item->name;
    }
}
