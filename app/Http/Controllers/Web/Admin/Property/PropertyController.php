<?php

namespace App\Http\Controllers\Web\Admin\Property;

use App\Algorithms\Property\PropertyAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Property\PropertyRequest;
use App\Models\Property\Property;
use App\Parser\Property\PropertyParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_DELETE);
                return $next($request);
            })->only(['delete']);

        }
    }

    public function get(Request $request)
    {
        $properties = Property::filter($request)
            ->with(['type', 'photos', 'addresses'])
            ->getOrPaginate($request);

        return success(PropertyParser::briefs($properties), pagination: pagination($properties));
    }

    public function detail($id)
    {
        $property = Property::with([
            'type', 'addresses', 'guestInfo', 'rooms.roomType', 'rooms.bedType',
            'availability', 'pricing', 'descriptions', 'photos', 'amenities', 'tags', 'seo', 'acf',
        ])->find($id);

        if (!$property) {
            errPropertyGet();
        }

        return success(PropertyParser::first($property));
    }

    public function create(PropertyRequest $request)
    {
        $algo = new PropertyAlgo();
        return $algo->create($request);
    }

    public function update($id, PropertyRequest $request)
    {
        $algo = new PropertyAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new PropertyAlgo((int)$id);
        return $algo->delete();
    }
}