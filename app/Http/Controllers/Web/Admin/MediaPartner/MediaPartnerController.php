<?php

namespace App\Http\Controllers\Web\Admin\MediaPartner;

use App\Algorithms\MediaPartner\MediaPartnerAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\MediaPartner\MediaPartnerRequest;
use App\Models\MediaPartner\MediaPartner;
use App\Parser\MediaPartner\MediaPartnerParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class MediaPartnerController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            // VIEW
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_MEDIA_PARTNER_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            // CREATE
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_MEDIA_PARTNER_CREATE);
                return $next($request);
            })->only(['create']);

            // UPDATE
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_MEDIA_PARTNER_UPDATE);
                return $next($request);
            })->only(['update']);

            // DELETE
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_MEDIA_PARTNER_DELETE);
                return $next($request);
            })->only(['delete']);

        }
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function get(Request $request)
    {
        $mediaPartners = MediaPartner::filter($request)->getOrPaginate($request);
        return success(MediaPartnerParser::briefs($mediaPartners), pagination: pagination($mediaPartners));
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function detail($id)
    {
        $mediaPartner = MediaPartner::find($id);
        if (!$mediaPartner) {
            errMediaPartnerGet();
        }

        return success(MediaPartnerParser::first($mediaPartner));
    }

    /**
     * @param MediaPartnerRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function create(MediaPartnerRequest $request)
    {
        $algo = new MediaPartnerAlgo();
        return $algo->create($request);
    }

    /**
     * @param $id
     * @param MediaPartnerRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function update($id, MediaPartnerRequest $request)
    {
        $algo = new MediaPartnerAlgo((int)$id);
        return $algo->update($request);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function delete($id)
    {
        $algo = new MediaPartnerAlgo((int)$id);
        return $algo->delete();
    }
}