<?php

namespace App\Http\Controllers\Public\Property;

use App\Http\Controllers\Controller;
use App\Models\Property\PropertyTag;
use App\Parser\Property\PropertyTagParser;
use Illuminate\Http\Request;

class PropertyTagController extends Controller
{
    public function get(Request $request)
    {
        $tags = PropertyTag::filter($request)->getOrPaginate($request);
        return success(PropertyTagParser::briefs($tags), pagination: pagination($tags));
    }
}
