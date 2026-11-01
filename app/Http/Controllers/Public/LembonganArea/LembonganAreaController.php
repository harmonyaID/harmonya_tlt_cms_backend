<?php

namespace App\Http\Controllers\Public\LembonganArea;

use App\Http\Controllers\Controller;
use App\Models\LembonganArea\LembonganArea;
use App\Parser\LembonganArea\LembonganAreaParser;
use Illuminate\Http\Request;

class LembonganAreaController extends Controller
{
    public function get(Request $request)
    {
        $areas = LembonganArea::filter($request)->where('isActive', true)->with('seo')->getOrPaginate($request);
        return success(LembonganAreaParser::briefs($areas), pagination: pagination($areas));
    }

    public function detail($idOrSlug)
    {
        $area = LembonganArea::where('isActive', true)->bySlugOrId($idOrSlug)->with('seo')->first();
        if (!$area) {
            errLembonganAreaGet();
        }

        return success(LembonganAreaParser::first($area));
    }
}
