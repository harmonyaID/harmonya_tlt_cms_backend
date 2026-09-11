<?php

namespace App\Http\Controllers\Public\Blog;

use App\Http\Controllers\Controller;
use App\Models\Blog\Blog;
use App\Models\Visitor\ContentVisitor;
use App\Parser\Blog\BlogParser;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function get(Request $request)
    {
        $request->merge(['isActive' => true]);

        $blogs = Blog::filter($request)->with(['category', 'tags', 'properties'])->orderBy('publishedAt', 'desc')->getOrPaginate($request);
        return success(BlogParser::briefs($blogs), pagination: pagination($blogs));
    }

    public function detail($idOrSlug, Request $request)
    {
        $blog = Blog::where('isActive', true)->bySlugOrId($idOrSlug)->with(['category', 'tags', 'properties', 'properties', 'seo', 'acf'])->first();
        if (!$blog) {
            errBlogGet();
        }

        ContentVisitor::recordVisit($blog, $request->ip(), $request->userAgent());

        return success(BlogParser::first($blog));
    }
}
