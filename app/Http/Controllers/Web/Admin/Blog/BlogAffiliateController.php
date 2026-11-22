<?php

namespace App\Http\Controllers\Web\Admin\Blog;

use App\Algorithms\Blog\BlogAffiliateAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\BlogAffiliateRequest;
use App\Models\Blog\BlogAffiliate;
use App\Parser\Blog\BlogAffiliateParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class BlogAffiliateController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BLOG_AFFILIATE_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BLOG_AFFILIATE_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BLOG_AFFILIATE_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BLOG_AFFILIATE_DELETE);
                return $next($request);
            })->only(['delete']);

        }
    }

    /**
     * ?isAppliedToAll=0 returns the affiliates that can be assigned to a blog.
     */
    public function get(Request $request)
    {
        $affiliates = BlogAffiliate::filter($request)->getOrPaginate($request);
        return success(BlogAffiliateParser::briefs($affiliates), pagination: pagination($affiliates));
    }

    public function detail($id)
    {
        $affiliate = BlogAffiliate::find($id);
        if (!$affiliate) {
            errBlogAffiliateGet();
        }

        return success(BlogAffiliateParser::first($affiliate));
    }

    public function create(BlogAffiliateRequest $request)
    {
        $algo = new BlogAffiliateAlgo();
        return $algo->create($request);
    }

    public function update($id, BlogAffiliateRequest $request)
    {
        $algo = new BlogAffiliateAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new BlogAffiliateAlgo((int)$id);
        return $algo->delete();
    }
}
