<?php

namespace App\Http\Controllers\Public\Experience;

use App\Http\Controllers\Controller;
use App\Models\Experience\Experience;
use App\Parser\Experience\ExperienceParser;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    public function get(Request $request)
    {
        $request->merge(['isActive' => true]);

        $experiences = Experience::filter($request)->with(['type', 'area', 'photos', 'seo'])->getOrPaginate($request);
        return success(ExperienceParser::briefs($experiences), pagination: pagination($experiences));
    }

    public function detail($idOrSlug)
    {        dd("test");

        $experience = Experience::where('isActive', true)->bySlugOrId($idOrSlug)->with(['type', 'area', 'photos', 'seo','acf'])->first();
        if (!$experience) {
            errExperienceGet();
        }

        return success(ExperienceParser::first($experience));
    }
}
