<?php

namespace App\Http\Controllers\Public\IslandGuide;

use App\Http\Controllers\Controller;
use App\Models\IslandGuide\IslandGuideType;
use App\Parser\IslandGuide\IslandGuideTypeParser;
use Illuminate\Http\Request;

class IslandGuideTypeController extends Controller
{
    public function get(Request $request)
    {
        $types = IslandGuideType::filter($request)->getOrPaginate($request);
        return success(IslandGuideTypeParser::briefs($types), pagination: pagination($types));
    }

    public function detail($id)
    {
        $type = IslandGuideType::with('seo')->find($id);
        if (!$type) {
            errIslandGuideTypeGet();
        }

        return success(IslandGuideTypeParser::first($type));
    }
}
