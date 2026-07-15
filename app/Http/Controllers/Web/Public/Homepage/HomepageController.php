<?php

namespace App\Http\Controllers\Web\Public\Homepage;

use App\Algorithms\Homepage\HomepageAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Homepage\HomepageRequest;
use App\Models\Homepage\Homepage;
use App\Parser\Homepage\HomepageParser;
use App\Services\Constant\Access\AccessPermissionName;
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
