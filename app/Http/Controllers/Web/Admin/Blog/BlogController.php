<?php

namespace App\Http\Controllers\Web\Admin\Blog;

use App\Algorithms\Blog\BlogAlgo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasTrash;
use App\Http\Requests\Blog\BlogRequest;
use App\Models\Blog\Blog;
use App\Parser\Blog\BlogParser;
use App\Services\Constant\Access\AccessPermissionName;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    use HasTrash;

    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BLOG_VIEW);
                return $next($request);
            })->only(['get', 'detail', 'trash']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BLOG_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BLOG_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BLOG_DELETE);
                return $next($request);
            })->only(['delete', 'restore', 'forceDelete']);

        }
    }

    protected function trashModel(): string
    {
        return Blog::class;
    }

    protected function trashParser(): string
    {
        return BlogParser::class;
    }

    protected function trashActivityType(): string
    {
        return ActivityType::BLOG;
    }

    protected function trashLabel($item): string
    {
        return $item->title;
    }

    public function get(Request $request)
    {
        $blogs = Blog::filter($request)->with('category', 'tags', 'seo', 'acf')->getOrPaginate($request);
        return success(BlogParser::briefs($blogs), pagination: pagination($blogs));
    }

    public function detail($id)
    {
        $blog = Blog::with('category', 'tags', 'seo', 'acf')->find($id);
        if (!$blog) {
            errBlogGet();
        }

        return success(BlogParser::first($blog));
    }

    public function create(BlogRequest $request)
    {
        $algo = new BlogAlgo();
        return $algo->create($request);
    }

    public function update($id, BlogRequest $request)
    {
        $algo = new BlogAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new BlogAlgo((int)$id);
        return $algo->delete();
    }
}