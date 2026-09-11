<?php

namespace App\Http\Controllers\Web\Admin\Offer;

use App\Algorithms\Offer\OfferCategoryAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Offer\OfferCategoryRequest;
use App\Models\Offer\OfferCategory;
use App\Parser\Offer\OfferCategoryParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class OfferCategoryController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_OFFER_CATEGORY_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_OFFER_CATEGORY_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_OFFER_CATEGORY_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_OFFER_CATEGORY_DELETE);
                return $next($request);
            })->only(['delete']);

        }
    }

    public function get(Request $request)
    {
        $categories = OfferCategory::filter($request)->getOrPaginate($request);
        return success(OfferCategoryParser::briefs($categories), pagination: pagination($categories));
    }

    public function detail($id)
    {
        $category = OfferCategory::find($id);
        if (!$category) {
            errOfferCategoryGet();
        }

        return success(OfferCategoryParser::first($category));
    }

    public function create(OfferCategoryRequest $request)
    {
        $algo = new OfferCategoryAlgo();
        return $algo->create($request);
    }

    public function update($id, OfferCategoryRequest $request)
    {
        $algo = new OfferCategoryAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new OfferCategoryAlgo((int)$id);
        return $algo->delete();
    }
}
