<?php

namespace App\Http\Controllers\Public\Faq;

use App\Http\Controllers\Controller;
use App\Models\Faq\Faq;
use App\Parser\Faq\FaqParser;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function get(Request $request)
    {
        $request->merge(['isActive' => true]);

        $faqs = Faq::filter($request)->with('type')->getOrPaginate($request);
        return success(FaqParser::briefs($faqs), pagination: pagination($faqs));
    }
}
