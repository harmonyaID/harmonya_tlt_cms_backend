<?php

namespace App\Http\Controllers\Public\Experience;

use App\Http\Controllers\Controller;
use App\Models\Experience\ExperienceType;
use App\Parser\Experience\ExperienceTypeParser;
use Illuminate\Http\Request;

class ExperienceTypeController extends Controller
{
    public function get(Request $request)
    {
        $types = ExperienceType::filter($request)->with('blogs')->getOrPaginate($request);
        return success(ExperienceTypeParser::briefs($types), pagination: pagination($types));
    }

    public function detail($id)
    {
        $type = ExperienceType::with('seo', 'blogs')->find($id);
        if (!$type) {
            errExperienceTypeGet();
        }

        return success(ExperienceTypeParser::first($type));
    }
}
