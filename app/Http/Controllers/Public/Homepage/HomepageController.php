<?php

namespace App\Http\Controllers\Public\Homepage;

use App\Http\Controllers\Controller;
use App\Models\Homepage\Homepage;
use App\Parser\Homepage\HomepageParser;
use Illuminate\Http\Request;

class HomepageController extends Controller
{

    public function get(Request $request)
    {
        $homepage = Homepage::filter($request)->with('seo')->first();
        if (!$homepage) {
            errHomepageGet();
        }

        return success(HomepageParser::first($homepage));
    }

    public function detail($id)
    {
        $homepage = Homepage::with('seo')->find($id);
        if (!$homepage) {
            errHomepageGet();
        }

        return success(HomepageParser::first($homepage));
    }
}
