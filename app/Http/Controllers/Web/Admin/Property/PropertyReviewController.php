<?php

namespace App\Http\Controllers\Web\Admin\Property;

use App\Algorithms\Property\PropertyReviewAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Property\PropertyReviewRequest;
use App\Models\Property\PropertyReview;
use App\Parser\Property\PropertyReviewParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class PropertyReviewController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_REVIEW_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

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
            })->only(['delete']);

        }
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
