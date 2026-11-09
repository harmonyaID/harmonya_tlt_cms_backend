<?php

namespace App\Http\Controllers\Public\Boat;

use App\Http\Controllers\Controller;
use App\Models\Boat\Boat;
use App\Parser\Boat\BoatParser;
use Illuminate\Http\Request;

class BoatController extends Controller
{
    public function get(Request $request)
    {
        $request->merge(['isActive' => true]);

        if ($request->has('limit') && !$request->has('page')) {
            $request->merge(['page' => 1]);
        }

        $boats = Boat::filter($request)->with(['photos', 'type', 'seo'])->getOrPaginate($request);
        return success(BoatParser::briefs($boats), pagination: pagination($boats));
    }

    public function detail($idOrSlug)
    {
        $boat = Boat::where('isActive', true)->bySlugOrId($idOrSlug)->with(['photos', 'type', 'customInformations', 'seo', 'acf'])->first();
        if (!$boat) {
            errBoatGet();
        }

        return success(BoatParser::first($boat));
    }
}
