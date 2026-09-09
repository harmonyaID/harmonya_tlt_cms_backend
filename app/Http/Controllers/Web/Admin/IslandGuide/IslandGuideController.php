<?php

namespace App\Http\Controllers\Web\Admin\IslandGuide;

use App\Algorithms\IslandGuide\IslandGuideAlgo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasTrash;
use App\Http\Requests\IslandGuide\IslandGuideRequest;
use App\Models\IslandGuide\IslandGuide;
use App\Parser\IslandGuide\IslandGuideParser;
use App\Services\Constant\Access\AccessPermissionName;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;

class IslandGuideController extends Controller
{
    use HasTrash;

    public function __construct()
    {
        if (config('auth.with-permission')) {
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_ISLAND_GUIDE_VIEW);
                return $next($request);
            })->only(['get', 'detail', 'trash']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_ISLAND_GUIDE_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_ISLAND_GUIDE_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_ISLAND_GUIDE_DELETE);
                return $next($request);
            })->only(['delete', 'restore', 'forceDelete']);
        }
    }

    protected function trashModel(): string
    {
        return IslandGuide::class;
    }

    protected function trashParser(): string
    {
        return IslandGuideParser::class;
    }

    protected function trashActivityType(): string
    {
        return ActivityType::ISLAND_GUIDE;
    }

    protected function trashLabel($item): string
    {
        return $item->name;
    }

    public function get(Request $request)
    {
        $islandGuides = IslandGuide::filter($request)->with('type', 'area', 'photos', 'seo', 'acf')->getOrPaginate($request);
        return success(IslandGuideParser::briefs($islandGuides), pagination: pagination($islandGuides));
    }

    public function detail($id)
    {
        $islandGuide = IslandGuide::with('type', 'area', 'photos', 'seo', 'acf')->find($id);
        if (!$islandGuide) errIslandGuideGet();
        return success(IslandGuideParser::first($islandGuide));
    }

    public function create(IslandGuideRequest $request)
    {
        return (new IslandGuideAlgo())->create($request);
    }

    public function update($id, IslandGuideRequest $request)
    {
        return (new IslandGuideAlgo((int)$id))->update($request);
    }

    public function delete($id)
    {
        return (new IslandGuideAlgo((int)$id))->delete();
    }
}