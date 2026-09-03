<?php

namespace App\Http\Controllers\Public\Blog;

use App\Http\Controllers\Controller;
use App\Models\Blog\Blog;
use App\Parser\Blog\BlogParser;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function get(Request $request)
    {
        $request->merge(['isActive' => true]);

        $blogs = Blog::filter($request)->with(['category', 'tags'])->getOrPaginate($request);
        return success(BlogParser::briefs($blogs), pagination: pagination($blogs));
    }

    public function detail($idOrSlug)
    {
        $blog = Blog::where('isActive', true)->bySlugOrId($idOrSlug)->with(['category', 'tags', 'seo', 'acf'])->first();
        if (!$blog) {
            errBlogGet();
        }

        return success(BlogParser::first($blog));
    }
}
