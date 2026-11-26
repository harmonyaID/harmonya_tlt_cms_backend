<?php

namespace App\Http\Controllers\Public\Property;

use App\Http\Controllers\Controller;
use App\Models\Property\PropertyArea;
use App\Parser\Property\PropertyAreaParser;
use Illuminate\Http\Request;

class PropertyAreaController extends Controller
{
    public function get(Request $request)
    {
        $areas = PropertyArea::filter($request)->getOrPaginate($request);
        return success(PropertyAreaParser::briefs($areas), pagination: pagination($areas));
    }
}
