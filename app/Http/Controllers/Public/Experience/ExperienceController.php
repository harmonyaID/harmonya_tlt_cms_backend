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

        $experiences = Experience::filter($request)->with(['type', 'area', 'photos'])->getOrPaginate($request);
        return success(ExperienceParser::briefs($experiences), pagination: pagination($experiences));
    }

    public function detail($id)
    {
        $experience = Experience::where('isActive', true)->with(['type', 'area', 'photos'])->find($id);
        if (!$experience) {
            errExperienceGet();
        }

        return success(ExperienceParser::first($experience));
    }
}
