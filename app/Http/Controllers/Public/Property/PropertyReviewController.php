<?php

namespace App\Http\Controllers\Public\Property;

use App\Http\Controllers\Controller;
use App\Models\Property\PropertyReview;
use App\Parser\Property\PropertyReviewParser;
use Illuminate\Http\Request;

class PropertyReviewController extends Controller
{
    public function get(Request $request)
    {
        $request->merge(['isActive' => true]);

        $reviews = PropertyReview::filter($request)->with('photos')->getOrPaginate($request);
        return success(PropertyReviewParser::briefs($reviews), pagination: pagination($reviews));
    }
}
