<?php

namespace App\Http\Controllers\Web\Admin\IslandGuide;

use App\Algorithms\IslandGuide\IslandGuideAreaAlgo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasTrash;
use App\Http\Requests\IslandGuide\IslandGuideAreaRequest;
use App\Models\IslandGuide\IslandGuideArea;
use App\Parser\IslandGuide\IslandGuideAreaParser;
use App\Services\Constant\Access\AccessPermissionName;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;

class IslandGuideAreaController extends Controller
{
    use HasTrash;

    public function __construct()
    {
        if (config('auth.with-permission')) {
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_ISLAND_GUIDE_AREA_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_ISLAND_GUIDE_AREA_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_ISLAND_GUIDE_AREA_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_ISLAND_GUIDE_AREA_DELETE);
                return $next($request);
            })->only(['delete']);
        }
    }
    protected function trashModel(): string
    {
        return IslandGuideArea::class;
    }

    protected function trashParser(): string
    {
        return IslandGuideAreaParser::class;
    }

    protected function trashActivityType(): string
    {
        return ActivityType::ISLAND_GUIDE_AREA;
    }

    protected function trashLabel($item): string
    {
        return $item->name;
    }

    public function get(Request $request)
    {
        $areas = IslandGuideArea::filter($request)->with('type', 'seo')->getOrPaginate($request);
        return success(IslandGuideAreaParser::briefs($areas), pagination: pagination($areas));
    }

    public function detail($id)
    {
        $area = IslandGuideArea::with('type', 'seo')->find($id);
        if (!$area) errIslandGuideAreaGet();
        return success(IslandGuideAreaParser::first($area));
    }

    public function create(IslandGuideAreaRequest $request)
    {
        return (new IslandGuideAreaAlgo())->create($request);
    }

    public function update($id, IslandGuideAreaRequest $request)
    {
        return (new IslandGuideAreaAlgo((int)$id))->update($request);
    }

    public function delete($id)
    {
        return (new IslandGuideAreaAlgo((int)$id))->delete();
    }
}
