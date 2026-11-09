<?php

namespace App\Http\Controllers\Public\Faq;

use App\Http\Controllers\Controller;
use App\Models\Faq\FaqType;
use App\Parser\Faq\FaqTypeParser;
use Illuminate\Http\Request;

class FaqTypeController extends Controller
{
    public function get(Request $request)
    {
        $types = FaqType::filter($request)->getOrPaginate($request);
        return success(FaqTypeParser::briefs($types), pagination: pagination($types));
    }
}
