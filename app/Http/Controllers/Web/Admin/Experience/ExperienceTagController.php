<?php

namespace App\Http\Controllers\Web\Admin\Experience;

use App\Algorithms\Experience\ExperienceTagAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Experience\ExperienceTagRequest;
use App\Models\Experience\ExperienceTag;
use App\Parser\Experience\ExperienceTagParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class ExperienceTagController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(
                    AccessPermissionName::STAFF_EXPERIENCE_TAG_VIEW
                );

                return $next($request);
            })->only([
                'get',
                'detail'
            ]);

            $this->middleware(function ($request, $next) {
                has_permission_staff(
                    AccessPermissionName::STAFF_EXPERIENCE_TAG_CREATE
                );

                return $next($request);
            })->only([
                'create'
            ]);

            $this->middleware(function ($request, $next) {
                has_permission_staff(
                    AccessPermissionName::STAFF_EXPERIENCE_TAG_UPDATE
                );

                return $next($request);
            })->only([
                'update'
            ]);

            $this->middleware(function ($request, $next) {
                has_permission_staff(
                    AccessPermissionName::STAFF_EXPERIENCE_TAG_DELETE
                );

                return $next($request);
            })->only([
                'delete'
            ]);
        }
    }

    public function get(Request $request)
    {
        $tags = ExperienceTag::filter($request)
            ->getOrPaginate($request);

        return success(
            ExperienceTagParser::briefs($tags),
            pagination: pagination($tags)
        );
    }

    public function detail($id)
    {
        $tag = ExperienceTag::find($id);

        if (!$tag) {
            errExperienceTagGet();
        }

        return success(
            ExperienceTagParser::first($tag)
        );
    }

    public function create(ExperienceTagRequest $request)
    {
        $algo = new ExperienceTagAlgo();

        return $algo->create($request);
    }

    public function update(
        $id,
        ExperienceTagRequest $request
    ) {
        $algo = new ExperienceTagAlgo((int) $id);

        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new ExperienceTagAlgo((int) $id);

        return $algo->delete();
    }
}