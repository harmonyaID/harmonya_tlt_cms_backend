<?php

namespace App\Http\Controllers\Web\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Blog\Blog;
use App\Models\Boat\BoatContactForm;
use App\Models\Property\PropertyVariantReview;
use App\Models\Visitor\ContentVisitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Property\PropertyVariant;
use App\Services\Constant\Access\AccessPermissionName;

class DashboardController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_DASHBOARD_VIEW);
                return $next($request);
            })->only(['metrics', 'analytics']);

        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function metrics()
    {
        // Generic breakdown - automatically includes any source type admins add later
        $bySourceType = PropertyVariant::query()
            ->join('property_source_types', 'property_source_types.id', '=', 'property_variants.sourceTypeId')
            ->select('property_source_types.id', 'property_source_types.name')
            ->selectRaw('count(property_variants.id) as total')
            ->groupBy('property_source_types.id', 'property_source_types.name')
            ->get();

        return success([
            'totalBlogPost' => Blog::count(),
            'totalPropertyGuesty' => (int)optional($bySourceType->firstWhere('name', 'Guesty'))->total,
            'totalPropertyBookeasy' => (int)optional($bySourceType->firstWhere('name', 'Bookeasy'))->total,

            'propertiesBySourceType' => $bySourceType->map(fn ($row) => [
                'id' => $row->id,
                'name' => $row->name,
                'total' => (int)$row->total,
            ]),
        ]);
    }

    public function analytics(Request $request)
    {
        $request->validate([
            'limit' => 'nullable|integer|min:1|max:50',
            'startDate' => 'nullable|date',
            'endDate' => 'nullable|date|after_or_equal:startDate',
        ]);

        $limit = (int) $request->input('limit', 10);
        $start = $request->startDate ? $request->date('startDate')->startOfDay() : null;
        $end = $request->endDate ? $request->date('endDate')->endOfDay() : null;

        $inRange = fn ($query, string $column) => $query
            ->when($start, fn ($q) => $q->where($column, '>=', $start))
            ->when($end, fn ($q) => $q->where($column, '<=', $end));

        $visits = fn (string $type) => $inRange(
            ContentVisitor::query()
                ->where('contentableType', $type)
                ->select('contentableId', DB::raw('count(*) as total'))
                ->groupBy('contentableId'),
            'createdAt'
        );

        $topPropertyVariantViews = $visits(PropertyVariant::class)->orderByDesc('total')->limit($limit)->get();
        $variantNames = PropertyVariant::whereIn('id', $topPropertyVariantViews->pluck('contentableId'))->pluck('nickname', 'id');

        $topBlogViews = $visits(Blog::class)->orderByDesc('total')->limit($limit)->get();
        $blogs = Blog::whereIn('id', $topBlogViews->pluck('contentableId'))->get(['id', 'title', 'slug'])->keyBy('id');

        $topBoatInquiries = $inRange(
            BoatContactForm::query()
                ->join('boats', 'boats.id', '=', 'boat_contact_forms.boatId')
                ->whereNull('boat_contact_forms.deletedAt')
                ->select('boats.id', 'boats.name', DB::raw('count(boat_contact_forms.id) as total'))
                ->groupBy('boats.id', 'boats.name'),
            'boat_contact_forms.createdAt'
        )->orderByDesc('total')->limit($limit)->get();

        $reviews = fn (string $direction) => $inRange(
            PropertyVariantReview::query()
                ->join('property_variants', 'property_variants.id', '=', 'property_variant_reviews.propertyVariantId')
                ->where('property_variant_reviews.isActive', true)
                ->whereNull('property_variant_reviews.deletedAt')
                ->select(
                    'property_variants.id',
                    'property_variants.nickname',
                    DB::raw('round(avg(property_variant_reviews.rating), 2) as averageRating'),
                    DB::raw('count(property_variant_reviews.id) as totalReviews')
                )
                ->groupBy('property_variants.id', 'property_variants.nickname'),
            'property_variant_reviews.createdAt'
        )->orderBy('averageRating', $direction)->orderByDesc('totalReviews')->limit($limit)->get()
            ->map(fn ($row) => [
                'id' => $row->id,
                'nickname' => $row->nickname,
                'averageRating' => (float) $row->averageRating,
                'totalReviews' => (int) $row->totalReviews,
            ]);

        return success([
            'topPropertyVariantViews' => $topPropertyVariantViews->map(fn ($row) => [
                'id' => $row->contentableId,
                'nickname' => $variantNames[$row->contentableId] ?? null,
                'totalViews' => (int) $row->total,
            ])->values(),
            'topBlogViews' => $topBlogViews->map(fn ($row) => [
                'id' => $row->contentableId,
                'title' => $blogs[$row->contentableId]->title ?? null,
                'slug' => $blogs[$row->contentableId]->slug ?? null,
                'totalViews' => (int) $row->total,
            ])->values(),
            'topBoatInquiries' => $topBoatInquiries->map(fn ($row) => [
                'id' => $row->id,
                'name' => $row->name,
                'totalInquiries' => (int) $row->total,
            ])->values(),
            'lowestRatedPropertyVariants' => $reviews('asc'),
            'highestRatedPropertyVariants' => $reviews('desc'),
        ]);
    }
}
