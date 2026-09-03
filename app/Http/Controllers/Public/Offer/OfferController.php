<?php

namespace App\Http\Controllers\Public\Offer;

use App\Http\Controllers\Controller;
use App\Models\Offer\Offer;
use App\Parser\Offer\OfferParser;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function get(Request $request)
    {
        $request->merge(['isActive' => true]);

        $offers = Offer::filter($request)->with('properties')->getOrPaginate($request);
        return success(OfferParser::briefs($offers), pagination: pagination($offers));
    }

    public function detail($idOrSlug)
    {
        $offer = Offer::where('isActive', true)->bySlugOrId($idOrSlug)->with('properties', 'seo')->first();
        if (!$offer) {
            errOfferGet();
        }

        return success(OfferParser::first($offer));
    }
}
