<?php

namespace App\Http\Controllers\Web\Admin\Experience;

use App\Algorithms\Experience\ExperienceTypeAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Experience\ExperienceTypeRequest;
use App\Models\Experience\ExperienceType;
use App\Parser\Experience\ExperienceTypeParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class ExperienceTypeController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {
            $this->middleware(fn($req, $next) => tap($next($req), fn() => has_permission_staff(AccessPermissionName::STAFF_EXPERIENCE_TYPE_VIEW)))->only(['get', 'detail']);
            $this->middleware(fn($req, $next) => tap($next($req), fn() => has_permission_staff(AccessPermissionName::STAFF_EXPERIENCE_TYPE_CREATE)))->only(['create']);
            $this->middleware(fn($req, $next) => tap($next($req), fn() => has_permission_staff(AccessPermissionName::STAFF_EXPERIENCE_TYPE_UPDATE)))->only(['update']);
            $this->middleware(fn($req, $next) => tap($next($req), fn() => has_permission_staff(AccessPermissionName::STAFF_EXPERIENCE_TYPE_DELETE)))->only(['delete']);
        }
    }

    public function get(Request $request)
    {
        $types = ExperienceType::filter($request)->getOrPaginate($request);
        return success(ExperienceTypeParser::briefs($types), pagination: pagination($types));
    }

    public function detail($id)
    {
        $type = ExperienceType::with('seo')->find($id);
        if (!$type) errExperienceTypeGet();
        return success(ExperienceTypeParser::first($type));
    }

    public function create(ExperienceTypeRequest $request)
    {
        return (new ExperienceTypeAlgo())->create($request);
    }

    public function update($id, ExperienceTypeRequest $request)
    {
        return (new ExperienceTypeAlgo((int)$id))->update($request);
    }

    public function delete($id)
    {
        return (new ExperienceTypeAlgo((int)$id))->delete();
    }
}