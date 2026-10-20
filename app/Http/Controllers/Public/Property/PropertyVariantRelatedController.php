<?php

namespace App\Http\Controllers\Public\Property;

use App\Http\Controllers\Controller;
use App\Models\Property\PropertyVariant;
use App\Parser\Property\PropertyVariantParser;
use App\Services\Constant\Property\PropertyStatus;

class PropertyVariantRelatedController extends Controller
{
    public function get($propertyVariantId)
    {
        $property = PropertyVariant::where('statusId', PropertyStatus::ACTIVE_ID)
            ->with(['relatedPropertyVariants' => function ($query) {
                $query->where('statusId', PropertyStatus::ACTIVE_ID);
            }, 'relatedPropertyVariants.type', 'relatedPropertyVariants.photos', 'relatedPropertyVariants.addresses'])
            ->find($propertyVariantId);

        if (!$property) {
            errPropertyVariantGet();
        }

        return success(PropertyVariantParser::briefs($property->relatedPropertyVariants));
    }
}
