<?php

namespace App\Http\Controllers\Web\Admin\Offer;

use App\Algorithms\Offer\OfferTagAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Offer\OfferTagRequest;
use App\Models\Offer\OfferTag;
use App\Parser\Offer\OfferTagParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class OfferTagController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_OFFER_TAG_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_OFFER_TAG_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_OFFER_TAG_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_OFFER_TAG_DELETE);
                return $next($request);
            })->only(['delete']);

        }
    }

    public function get(Request $request)
    {
        $tags = OfferTag::filter($request)->getOrPaginate($request);
        return success(OfferTagParser::briefs($tags), pagination: pagination($tags));
    }

    public function detail($id)
    {
        $tag = OfferTag::find($id);
        if (!$tag) {
            errOfferTagGet();
        }

        return success(OfferTagParser::first($tag));
    }

    public function create(OfferTagRequest $request)
    {
        $algo = new OfferTagAlgo();
        return $algo->create($request);
    }

    public function update($id, OfferTagRequest $request)
    {
        $algo = new OfferTagAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new OfferTagAlgo((int)$id);
        return $algo->delete();
    }
}
