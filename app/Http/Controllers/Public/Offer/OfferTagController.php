<?php

namespace App\Http\Controllers\Public\Offer;

use App\Http\Controllers\Controller;
use App\Models\Offer\OfferTag;
use App\Parser\Offer\OfferTagParser;
use Illuminate\Http\Request;

class OfferTagController extends Controller
{
    public function get(Request $request)
    {
        $tags = OfferTag::filter($request)->getOrPaginate($request);
        return success(OfferTagParser::briefs($tags), pagination: pagination($tags));
    }
}
