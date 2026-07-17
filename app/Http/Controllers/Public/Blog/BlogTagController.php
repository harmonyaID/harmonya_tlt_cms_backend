<?php

namespace App\Http\Controllers\Public\Blog;

use App\Http\Controllers\Controller;
use App\Models\Blog\BlogTag;
use App\Parser\Blog\BlogTagParser;
use Illuminate\Http\Request;

class BlogTagController extends Controller
{
    public function get(Request $request)
    {
        $tags = BlogTag::filter($request)->getOrPaginate($request);
        return success(BlogTagParser::briefs($tags), pagination: pagination($tags));
    }
}
