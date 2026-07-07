<?php

namespace App\Http\Controllers\Web\Admin\Blog;

use App\Algorithms\Blog\BlogCategoryAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\BlogCategoryRequest;
use App\Models\Blog\BlogCategory;
use App\Parser\Blog\BlogCategoryParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class BlogCategoryController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BLOG_CATEGORY_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BLOG_CATEGORY_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BLOG_CATEGORY_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BLOG_CATEGORY_DELETE);
                return $next($request);
            })->only(['delete']);

        }
    }

    public function get(Request $request)
    {
        $categories = BlogCategory::filter($request)->getOrPaginate($request);
        return success(BlogCategoryParser::briefs($categories), pagination: pagination($categories));
    }

    public function detail($id)
    {
        $category = BlogCategory::find($id);
        if (!$category) {
            errBlogCategoryGet();
        }

        return success(BlogCategoryParser::first($category));
    }

    public function create(BlogCategoryRequest $request)
    {
        $algo = new BlogCategoryAlgo();
        return $algo->create($request);
    }

    public function update($id, BlogCategoryRequest $request)
    {
        $algo = new BlogCategoryAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new BlogCategoryAlgo((int)$id);
        return $algo->delete();
    }
}