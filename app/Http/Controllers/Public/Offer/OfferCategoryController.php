<?php

namespace App\Http\Controllers\Public\Offer;

use App\Http\Controllers\Controller;
use App\Models\Offer\OfferCategory;
use App\Parser\Offer\OfferCategoryParser;
use Illuminate\Http\Request;

class OfferCategoryController extends Controller
{
    public function get(Request $request)
    {
        $categories = OfferCategory::filter($request)->getOrPaginate($request);
        return success(OfferCategoryParser::briefs($categories), pagination: pagination($categories));
    }
}
