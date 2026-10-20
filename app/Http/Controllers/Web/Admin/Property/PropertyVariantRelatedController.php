<?php

namespace App\Http\Controllers\Web\Admin\Property;

use App\Algorithms\Property\PropertyVariantRelatedAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Property\PropertyVariantRelatedRequest;
use App\Models\Property\PropertyVariant;
use App\Parser\Property\PropertyVariantParser;
use App\Services\Constant\Access\AccessPermissionName;

class PropertyVariantRelatedController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_VARIANT_VIEW);
                return $next($request);
            })->only(['get']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_VARIANT_UPDATE);
                return $next($request);
            })->only(['sync']);

        }
    }

    public function get($propertyVariantId)
    {
        $property = PropertyVariant::with('relatedPropertyVariants.type', 'relatedPropertyVariants.photos', 'relatedPropertyVariants.addresses')
            ->find($propertyVariantId);

        if (!$property) {
            errPropertyVariantGet();
        }

        return success(PropertyVariantParser::briefs($property->relatedPropertyVariants));
    }

    public function sync($propertyVariantId, PropertyVariantRelatedRequest $request)
    {
        $property = PropertyVariant::find($propertyVariantId);
        if (!$property) {
            errPropertyVariantGet();
        }

        $algo = new PropertyVariantRelatedAlgo($property);
        return $algo->sync($request);
    }
}
