<?php

namespace App\Http\Controllers\Web\Admin\Experience;

use App\Algorithms\Experience\ExperienceAreaAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Experience\ExperienceAreaRequest;
use App\Models\Experience\ExperienceArea;
use App\Parser\Experience\ExperienceAreaParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class ExperienceAreaController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_EXPERIENCE_AREA_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_EXPERIENCE_AREA_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_EXPERIENCE_AREA_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_EXPERIENCE_AREA_DELETE);
                return $next($request);
            })->only(['delete']);
        }
    }

    public function get(Request $request)
    {
        $areas = ExperienceArea::filter($request)->with('type', 'seo')->getOrPaginate($request);
        return success(ExperienceAreaParser::briefs($areas), pagination: pagination($areas));
    }

    public function detail($id)
    {
        $area = ExperienceArea::with('type', 'seo')->find($id);
        if (!$area) errExperienceAreaGet();
        return success(ExperienceAreaParser::first($area));
    }

    public function create(ExperienceAreaRequest $request)
    {
        return (new ExperienceAreaAlgo())->create($request);
    }

    public function update($id, ExperienceAreaRequest $request)
    {
        return (new ExperienceAreaAlgo((int)$id))->update($request);
    }

    public function delete($id)
    {
        return (new ExperienceAreaAlgo((int)$id))->delete();
    }
}
