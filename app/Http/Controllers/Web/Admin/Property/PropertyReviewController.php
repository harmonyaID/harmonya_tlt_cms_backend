<?php

namespace App\Http\Controllers\Web\Admin\Property;

use App\Algorithms\Property\PropertyReviewAlgo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasTrash;
use App\Http\Requests\Property\PropertyReviewRequest;
use App\Models\Property\PropertyReview;
use App\Parser\Property\PropertyReviewParser;
use App\Services\Constant\Access\AccessPermissionName;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;

class PropertyReviewController extends Controller
{
    use HasTrash;

    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_REVIEW_VIEW);
                return $next($request);
            })->only(['get', 'detail', 'trash']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_REVIEW_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_REVIEW_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_REVIEW_DELETE);
                return $next($request);
            })->only(['delete', 'restore', 'forceDelete']);

        }
    }

    protected function trashModel(): string
    {
        return PropertyReview::class;
    }

    protected function trashParser(): string
    {
        return PropertyReviewParser::class;
    }

    protected function trashActivityType(): string
    {
        return ActivityType::PROPERTY_REVIEW;
    }

    protected function trashLabel($item): string
    {
        return $item->name;
    }

    public function get(Request $request)
    {
        $reviews = PropertyReview::filter($request)->with('photos')->getOrPaginate($request);
        return success(PropertyReviewParser::briefs($reviews), pagination: pagination($reviews));
    }

    public function detail($id)
    {
        $review = PropertyReview::with(['photos', 'property'])->find($id);
        if (!$review) {
            errPropertyReviewGet();
        }

        return success(PropertyReviewParser::first($review));
    }

    public function create(PropertyReviewRequest $request)
    {
        $algo = new PropertyReviewAlgo();
        return $algo->create($request);
    }

    public function update($id, PropertyReviewRequest $request)
    {
        $algo = new PropertyReviewAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new PropertyReviewAlgo((int)$id);
        return $algo->delete();
    }
}
