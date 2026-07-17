<?php

namespace App\Http\Controllers\Public\Blog;

use App\Http\Controllers\Controller;
use App\Models\Blog\BlogCategory;
use App\Parser\Blog\BlogCategoryParser;
use Illuminate\Http\Request;

class BlogCategoryController extends Controller
{
    public function get(Request $request)
    {
        $categories = BlogCategory::filter($request)->getOrPaginate($request);
        return success(BlogCategoryParser::briefs($categories), pagination: pagination($categories));
    }
}
