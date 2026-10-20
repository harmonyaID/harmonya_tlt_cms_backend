<?php

namespace App\Http\Controllers\Web\Admin\Property;

use App\Algorithms\Property\PropertyVariantReviewAlgo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasTrash;
use App\Http\Requests\Property\PropertyVariantReviewRequest;
use App\Models\Property\PropertyVariantReview;
use App\Parser\Property\PropertyVariantReviewParser;
use App\Services\Constant\Access\AccessPermissionName;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;

class PropertyVariantReviewController extends Controller
{
    use HasTrash;

    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_VARIANT_REVIEW_VIEW);
                return $next($request);
            })->only(['get', 'detail', 'trash']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_VARIANT_REVIEW_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_VARIANT_REVIEW_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_VARIANT_REVIEW_DELETE);
                return $next($request);
            })->only(['delete', 'restore', 'forceDelete']);

        }
    }

    protected function trashModel(): string
    {
        return PropertyVariantReview::class;
    }

    protected function trashParser(): string
    {
        return PropertyVariantReviewParser::class;
    }

    protected function trashActivityType(): string
    {
        return ActivityType::PROPERTY_VARIANT_REVIEW;
    }

    protected function trashLabel($item): string
    {
        return $item->name;
    }

    public function get(Request $request)
    {
        $reviews = PropertyVariantReview::filter($request)->with('photos')->getOrPaginate($request);
        return success(PropertyVariantReviewParser::briefs($reviews), pagination: pagination($reviews));
    }

    public function detail($id)
    {
        $review = PropertyVariantReview::with(['photos', 'propertyVariant'])->find($id);
        if (!$review) {
            errPropertyVariantReviewGet();
        }

        return success(PropertyVariantReviewParser::first($review));
    }

    public function create(PropertyVariantReviewRequest $request)
    {
        $algo = new PropertyVariantReviewAlgo();
        return $algo->create($request);
    }

    public function update($id, PropertyVariantReviewRequest $request)
    {
        $algo = new PropertyVariantReviewAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new PropertyVariantReviewAlgo((int)$id);
        return $algo->delete();
    }
}
