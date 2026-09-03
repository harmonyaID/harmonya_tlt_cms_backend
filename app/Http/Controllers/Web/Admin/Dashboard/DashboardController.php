<?php

namespace App\Http\Controllers\Web\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Blog\Blog;
use App\Models\Property\Property;
use App\Services\Constant\Access\AccessPermissionName;

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
        // Generic breakdown - automatically includes any source type admins add later
        $bySourceType = Property::query()
            ->join('property_source_types', 'property_source_types.id', '=', 'properties.sourceTypeId')
            ->select('property_source_types.id', 'property_source_types.name')
            ->selectRaw('count(properties.id) as total')
            ->groupBy('property_source_types.id', 'property_source_types.name')
            ->get();

        return success([
            'totalBlogPost' => Blog::count(),

            // kept for backward compatibility with existing dashboard widgets
            'totalPropertyGuesty' => (int)optional($bySourceType->firstWhere('name', 'Guesty'))->total,
            'totalPropertyBookeasy' => (int)optional($bySourceType->firstWhere('name', 'Bookeasy'))->total,

            'propertiesBySourceType' => $bySourceType->map(fn ($row) => [
                'id' => $row->id,
                'name' => $row->name,
                'total' => (int)$row->total,
            ]),
        ]);
    }
}
