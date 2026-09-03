<?php

namespace App\Http\Controllers\Web\Admin\Experience;

use App\Algorithms\Experience\ExperienceCategoryAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Experience\ExperienceCategoryRequest;
use App\Models\Experience\ExperienceCategory;
use App\Parser\Experience\ExperienceCategoryParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class ExperienceCategoryController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_EXPERIENCE_CATEGORY_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_EXPERIENCE_CATEGORY_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_EXPERIENCE_CATEGORY_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_EXPERIENCE_CATEGORY_DELETE);
                return $next($request);
            })->only(['delete']);
        }
    }

    public function get(Request $request)
    {
        $categories = ExperienceCategory::filter($request)->with('type')->getOrPaginate($request);
        return success(ExperienceCategoryParser::briefs($categories), pagination: pagination($categories));
    }

    public function detail($id)
    {
        $category = ExperienceCategory::with('type')->find($id);
        if (!$category) errExperienceCategoryGet();
        return success(ExperienceCategoryParser::first($category));
    }

    public function create(ExperienceCategoryRequest $request)
    {
        return (new ExperienceCategoryAlgo())->create($request);
    }

    public function update($id, ExperienceCategoryRequest $request)
    {
        return (new ExperienceCategoryAlgo((int)$id))->update($request);
    }

    public function delete($id)
    {
        return (new ExperienceCategoryAlgo((int)$id))->delete();
    }
}