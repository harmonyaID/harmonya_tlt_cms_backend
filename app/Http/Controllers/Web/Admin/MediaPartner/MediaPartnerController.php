<?php

namespace App\Http\Controllers\Web\Admin\MediaPartner;

use App\Algorithms\MediaPartner\MediaPartnerAlgo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasTrash;
use App\Http\Requests\MediaPartner\MediaPartnerRequest;
use App\Models\MediaPartner\MediaPartner;
use App\Parser\MediaPartner\MediaPartnerParser;
use App\Services\Constant\Access\AccessPermissionName;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;

class MediaPartnerController extends Controller
{
    use HasTrash;

    public function __construct()
    {
        if (config('auth.with-permission')) {
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_MEDIA_PARTNER_VIEW);
                return $next($request);
            })->only(['get', 'detail', 'trash']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_MEDIA_PARTNER_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_MEDIA_PARTNER_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_MEDIA_PARTNER_DELETE);
                return $next($request);
            })->only(['delete', 'restore', 'forceDelete']);
        }
    }

    public function get(Request $request)
    {
        $mediaPartners = MediaPartner::filter($request)->getOrPaginate($request);
        return success(MediaPartnerParser::briefs($mediaPartners), pagination: pagination($mediaPartners));
    }

    public function detail($id)
    {
        $mediaPartner = MediaPartner::find($id);
        if (!$mediaPartner) {
            errMediaPartnerGet();
        }

        return success(MediaPartnerParser::first($mediaPartner));
    }

    public function create(MediaPartnerRequest $request)
    {
        $algo = new MediaPartnerAlgo();
        return $algo->create($request);
    }

    public function update($id, MediaPartnerRequest $request)
    {
        $algo = new MediaPartnerAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new MediaPartnerAlgo((int)$id);
        return $algo->delete();
    }

    protected function trashModel(): string
    {
        return MediaPartner::class;
    }

    protected function trashParser(): string
    {
        return MediaPartnerParser::class;
    }

    protected function trashActivityType(): string
    {
        return ActivityType::MEDIA_PARTNER;
    }

    protected function trashLabel($item): string
    {
        return (string) ($item->name ?: $item->description);
    }
}
