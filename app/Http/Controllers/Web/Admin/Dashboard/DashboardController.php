<?php

namespace App\Http\Controllers\Web\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Blog\Blog;
use App\Models\Property\Property;
use App\Services\Constant\Access\AccessPermissionName;
use App\Services\Constant\Property\PropertySourceType;

class DashboardController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_DASHBOARD_VIEW);
                return $next($request);
            })->only(['metrics']);

        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function metrics()
    {
        return success([
            'totalBlogPost' => Blog::count(),
            'totalPropertyGuesty' => Property::where('sourceTypeId', PropertySourceType::GUESTY_ID)->count(),
            'totalPropertyBookeasy' => Property::where('sourceTypeId', PropertySourceType::BOOKEASY_ID)->count(),
        ]);
    }
}