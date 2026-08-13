<?php

namespace App\Http\Controllers\Public\Property;

use App\Http\Controllers\Controller;
use App\Models\Property\PropertySourceType;
use App\Parser\Property\PropertySourceTypeParser;
use Illuminate\Http\Request;

class PropertySourceTypeController extends Controller
{
    public function get(Request $request)
    {
        $types = PropertySourceType::filter($request)->getOrPaginate($request);
        return success(PropertySourceTypeParser::briefs($types), pagination: pagination($types));
    }
}
