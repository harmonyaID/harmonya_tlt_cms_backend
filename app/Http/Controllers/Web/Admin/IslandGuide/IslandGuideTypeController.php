<?php

namespace App\Http\Controllers\Web\Admin\IslandGuide;

use App\Algorithms\IslandGuide\IslandGuideTypeAlgo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasTrash;
use App\Http\Requests\IslandGuide\IslandGuideTypeRequest;
use App\Models\IslandGuide\IslandGuideType;
use App\Parser\IslandGuide\IslandGuideTypeParser;
use App\Services\Constant\Access\AccessPermissionName;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;

class IslandGuideTypeController extends Controller
{
    use HasTrash;

    public function __construct()
    {
        if (config('auth.with-permission')) {
            $this->middleware(fn($req, $next) => tap($next($req), fn() => has_permission_staff(AccessPermissionName::STAFF_ISLAND_GUIDE_TYPE_VIEW)))->only(['get', 'detail']);
            $this->middleware(fn($req, $next) => tap($next($req), fn() => has_permission_staff(AccessPermissionName::STAFF_ISLAND_GUIDE_TYPE_CREATE)))->only(['create']);
            $this->middleware(fn($req, $next) => tap($next($req), fn() => has_permission_staff(AccessPermissionName::STAFF_ISLAND_GUIDE_TYPE_UPDATE)))->only(['update']);
            $this->middleware(fn($req, $next) => tap($next($req), fn() => has_permission_staff(AccessPermissionName::STAFF_ISLAND_GUIDE_TYPE_DELETE)))->only(['delete']);
        }
    }

    protected function trashModel(): string
    {
        return IslandGuideType::class;
    }

    protected function trashParser(): string
    {
        return IslandGuideTypeParser::class;
    }

    protected function trashActivityType(): string
    {
        return ActivityType::ISLAND_GUIDE_TYPE;
    }

    protected function trashLabel($item): string
    {
        return $item->name;
    }

    public function get(Request $request)
    {
        $types = IslandGuideType::filter($request)->getOrPaginate($request);
        return success(IslandGuideTypeParser::briefs($types), pagination: pagination($types));
    }

    public function detail($id)
    {
        $type = IslandGuideType::with('seo')->find($id);
        if (!$type) errIslandGuideTypeGet();
        return success(IslandGuideTypeParser::first($type));
    }

    public function create(IslandGuideTypeRequest $request)
    {
        return (new IslandGuideTypeAlgo())->create($request);
    }

    public function update($id, IslandGuideTypeRequest $request)
    {
        return (new IslandGuideTypeAlgo((int)$id))->update($request);
    }

    public function delete($id)
    {
        return (new IslandGuideTypeAlgo((int)$id))->delete();
    }
}
