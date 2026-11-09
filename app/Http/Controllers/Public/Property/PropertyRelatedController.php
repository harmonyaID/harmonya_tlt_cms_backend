<?php

namespace App\Http\Controllers\Public\Property;

use App\Http\Controllers\Controller;
use App\Models\Property\Property;
use App\Parser\Property\PropertyParser;
use App\Services\Constant\Property\PropertyStatus;

class PropertyRelatedController extends Controller
{
    public function get($propertyId)
    {
        $property = Property::where('statusId', PropertyStatus::ACTIVE_ID)
            ->with(['relatedProperties' => function ($query) {
                $query->where('statusId', PropertyStatus::ACTIVE_ID);
            }, 'relatedProperties.type', 'relatedProperties.photos', 'relatedProperties.addresses'])
            ->find($propertyId);

        if (!$property) {
            errPropertyGet();
        }

        return success(PropertyParser::briefs($property->relatedProperties));
    }
}
