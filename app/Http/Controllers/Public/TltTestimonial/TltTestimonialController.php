<?php

namespace App\Http\Controllers\Public\TltTestimonial;

use App\Http\Controllers\Controller;
use App\Models\TltTestimonial\TltTestimonial;
use App\Parser\TltTestimonial\TltTestimonialParser;
use Illuminate\Http\Request;

class TltTestimonialController extends Controller
{
    public function get(Request $request)
    {
        $request->merge(['isActive' => true]);

        $testimonials = TltTestimonial::filter($request)->getOrPaginate($request);
        return success(TltTestimonialParser::briefs($testimonials), pagination: pagination($testimonials));
    }
}
