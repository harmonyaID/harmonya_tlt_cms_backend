<?php

namespace App\Http\Controllers\Public\IslandGuide;

use App\Http\Controllers\Controller;
use App\Models\IslandGuide\IslandGuideArea;
use App\Parser\IslandGuide\IslandGuideAreaParser;
use Illuminate\Http\Request;

class IslandGuideAreaController extends Controller
{
    public function get(Request $request)
    {
        $areas = IslandGuideArea::filter($request)->with('type', 'seo')->getOrPaginate($request);
        return success(IslandGuideAreaParser::briefs($areas), pagination: pagination($areas));
    }

    public function detail($id)
    {
        $area = IslandGuideArea::with('type', 'seo')->find($id);
        if (!$area) {
            errIslandGuideAreaGet();
        }

        return success(IslandGuideAreaParser::first($area));
    }
}
