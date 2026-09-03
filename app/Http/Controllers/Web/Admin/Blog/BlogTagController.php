<?php

namespace App\Http\Controllers\Web\Admin\Blog;

use App\Algorithms\Blog\BlogTagAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\BlogTagRequest;
use App\Models\Blog\BlogTag;
use App\Parser\Blog\BlogTagParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class BlogTagController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BLOG_TAG_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BLOG_TAG_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BLOG_TAG_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BLOG_TAG_DELETE);
                return $next($request);
            })->only(['delete']);

        }
    }

    public function get(Request $request)
    {
        $tags = BlogTag::filter($request)->getOrPaginate($request);
        return success(BlogTagParser::briefs($tags), pagination: pagination($tags));
    }

    public function detail($id)
    {
        $tag = BlogTag::find($id);
        if (!$tag) {
            errBlogTagGet();
        }

        return success(BlogTagParser::first($tag));
    }

    public function create(BlogTagRequest $request)
    {
        $algo = new BlogTagAlgo();
        return $algo->create($request);
    }

    public function update($id, BlogTagRequest $request)
    {
        $algo = new BlogTagAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new BlogTagAlgo((int)$id);
        return $algo->delete();
    }
}