<?php

namespace App\Http\Controllers\Web\Admin\Property;

use App\Algorithms\Property\PropertyRelatedAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Property\PropertyRelatedRequest;
use App\Models\Property\Property;
use App\Parser\Property\PropertyParser;
use App\Services\Constant\Access\AccessPermissionName;

class PropertyRelatedController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_VIEW);
                return $next($request);
            })->only(['get']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_UPDATE);
                return $next($request);
            })->only(['sync']);

        }
    }

    public function get($propertyId)
    {
        $property = Property::with('relatedProperties.type', 'relatedProperties.photos', 'relatedProperties.addresses')
            ->find($propertyId);

        if (!$property) {
            errPropertyGet();
        }

        return success(PropertyParser::briefs($property->relatedProperties));
    }

    public function sync($propertyId, PropertyRelatedRequest $request)
    {
        $property = Property::find($propertyId);
        if (!$property) {
            errPropertyGet();
        }

        $algo = new PropertyRelatedAlgo($property);
        return $algo->sync($request);
    }
}
