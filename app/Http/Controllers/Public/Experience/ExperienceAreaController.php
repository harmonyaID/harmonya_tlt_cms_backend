<?php

namespace App\Http\Controllers\Public\Experience;

use App\Http\Controllers\Controller;
use App\Models\Experience\ExperienceArea;
use App\Parser\Experience\ExperienceAreaParser;
use Illuminate\Http\Request;

class ExperienceAreaController extends Controller
{
    public function get(Request $request)
    {
        $areas = ExperienceArea::filter($request)->with('type', 'seo')->getOrPaginate($request);
        return success(ExperienceAreaParser::briefs($areas), pagination: pagination($areas));
    }

    public function detail($id)
    {
        $area = ExperienceArea::with('type', 'seo')->find($id);
        if (!$area) {
            errExperienceAreaGet();
        }

        return success(ExperienceAreaParser::first($area));
    }
}
