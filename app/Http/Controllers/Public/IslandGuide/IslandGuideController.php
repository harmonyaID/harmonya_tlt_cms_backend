<?php

namespace App\Http\Controllers\Public\IslandGuide;

use App\Http\Controllers\Controller;
use App\Models\IslandGuide\IslandGuide;
use App\Parser\IslandGuide\IslandGuideParser;
use Illuminate\Http\Request;

class IslandGuideController extends Controller
{
    public function get(Request $request)
    {
        $request->merge(['isActive' => true]);

        $islandGuides = IslandGuide::filter($request)->with(['type', 'area', 'photos'])->getOrPaginate($request);
        return success(IslandGuideParser::briefs($islandGuides), pagination: pagination($islandGuides));
    }

    public function detail($id)
    {
        $islandGuide = IslandGuide::where('isActive', true)->with(['type', 'area', 'photos', 'acf'])->find($id);
        if (!$islandGuide) {
            errIslandGuideGet();
        }

        return success(IslandGuideParser::first($islandGuide));
    }
}
