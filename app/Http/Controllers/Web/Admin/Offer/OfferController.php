<?php

namespace App\Http\Controllers\Web\Admin\Offer;

use App\Algorithms\Offer\OfferAlgo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasTrash;
use App\Http\Requests\Offer\OfferRequest;
use App\Models\Offer\Offer;
use App\Parser\Offer\OfferParser;
use App\Services\Constant\Access\AccessPermissionName;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    use HasTrash;

    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_OFFER_VIEW);
                return $next($request);
            })->only(['get', 'detail', 'trash']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_OFFER_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_OFFER_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_OFFER_DELETE);
                return $next($request);
            })->only(['delete', 'restore', 'forceDelete']);
        }
    }

    public function get(Request $request)
    {
        $offers = Offer::filter($request)->with('properties', 'seo')->getOrPaginate($request);
        return success(OfferParser::briefs($offers), pagination: pagination($offers));
    }

    public function detail($id)
    {
        $offer = Offer::with('properties', 'seo', 'acf')->find($id);
        if (!$offer) {
            errOfferGet();
        }

        return success(OfferParser::first($offer));
    }

    public function create(OfferRequest $request)
    {
        $algo = new OfferAlgo();
        return $algo->create($request);
    }

    public function update($id, OfferRequest $request)
    {
        $algo = new OfferAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new OfferAlgo((int)$id);
        return $algo->delete();
    }

    protected function trashModel(): string
    {
        return Offer::class;
    }

    protected function trashParser(): string
    {
        return OfferParser::class;
    }

    protected function trashActivityType(): string
    {
        return ActivityType::OFFER;
    }

    protected function trashLabel($item): string
    {
        return $item->title;
    }
}
