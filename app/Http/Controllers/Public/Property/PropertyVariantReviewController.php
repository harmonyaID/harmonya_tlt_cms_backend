<?php

namespace App\Http\Controllers\Public\Property;

use App\Http\Controllers\Controller;
use App\Models\Property\PropertyVariantReview;
use App\Parser\Property\PropertyVariantReviewParser;
use Illuminate\Http\Request;

class PropertyVariantReviewController extends Controller
{
    public function get(Request $request)
    {
        $request->merge(['isActive' => true]);

        $reviews = PropertyVariantReview::filter($request)->with('photos')->getOrPaginate($request);
        return success(PropertyVariantReviewParser::briefs($reviews), pagination: pagination($reviews));
    }
}
