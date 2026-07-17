<?php

namespace App\Http\Controllers\Public\TltReview;

use App\Http\Controllers\Controller;
use App\Models\TltReview\TltReview;
use App\Parser\TltReview\TltReviewParser;
use Illuminate\Http\Request;

class TltReviewController extends Controller
{
    public function get(Request $request)
    {
        $request->merge(['isActive' => true]);

        $reviews = TltReview::filter($request)->with('photos')->getOrPaginate($request);
        return success(TltReviewParser::briefs($reviews), pagination: pagination($reviews));
    }
}
