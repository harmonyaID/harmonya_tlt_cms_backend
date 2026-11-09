<?php

namespace App\Http\Controllers\Public\Property;

use App\Http\Controllers\Controller;
use App\Models\Property\PropertyType;
use App\Parser\Property\PropertyTypeParser;
use Illuminate\Http\Request;

class PropertyTypeController extends Controller
{
    public function get(Request $request)
    {
        $types = PropertyType::filter($request)->getOrPaginate($request);
        return success(PropertyTypeParser::briefs($types), pagination: pagination($types));
    }
}
