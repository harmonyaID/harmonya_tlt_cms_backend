<?php

namespace App\Http\Controllers\Web\Admin\Experience;

use App\Algorithms\Experience\ExperienceAlgo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasTrash;
use App\Http\Requests\Experience\ExperienceRequest;
use App\Models\Experience\Experience;
use App\Parser\Experience\ExperienceParser;
use App\Services\Constant\Access\AccessPermissionName;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    use HasTrash;

    public function __construct()
    {
        if (config('auth.with-permission')) {
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_EXPERIENCE_VIEW);
                return $next($request);
            })->only(['get', 'detail', 'trash']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_EXPERIENCE_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_EXPERIENCE_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_EXPERIENCE_DELETE);
                return $next($request);
            })->only(['delete', 'restore', 'forceDelete']);
        }
    }

    protected function trashModel(): string
    {
        return Experience::class;
    }

    protected function trashParser(): string
    {
        return ExperienceParser::class;
    }

    protected function trashActivityType(): string
    {
        return ActivityType::EXPERIENCE;
    }

    protected function trashLabel($item): string
    {
        return $item->name;
    }

    public function get(Request $request)
    {
        $experiences = Experience::filter($request)->with('type', 'area', 'photos', 'seo', 'acf')->getOrPaginate($request);
        return success(ExperienceParser::briefs($experiences), pagination: pagination($experiences));
    }

    public function detail($id)
    {
        $experience = Experience::with('type', 'area', 'photos', 'seo', 'acf')->find($id);
        if (!$experience) errExperienceGet();
        return success(ExperienceParser::first($experience));
    }

    public function create(ExperienceRequest $request)
    {
        return (new ExperienceAlgo())->create($request);
    }

    public function update($id, ExperienceRequest $request)
    {
        return (new ExperienceAlgo((int)$id))->update($request);
    }

    public function delete($id)
    {
        return (new ExperienceAlgo((int)$id))->delete();
    }
}