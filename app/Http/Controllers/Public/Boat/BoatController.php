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

        $boats = Boat::filter($request)->with(['photos', 'type'])->getOrPaginate($request);
        return success(BoatParser::briefs($boats), pagination: pagination($boats));
    }

    public function detail($id)
    {
        $boat = Boat::where('isActive', true)->with(['photos', 'type', 'customInformations'])->find($id);
        if (!$boat) {
            errBoatGet();
        }

        return success(BoatParser::first($boat));
    }
}
