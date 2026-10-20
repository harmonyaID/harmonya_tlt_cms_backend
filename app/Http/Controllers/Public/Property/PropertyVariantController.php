<?php

namespace App\Http\Controllers\Public\Property;

use App\Http\Controllers\Controller;
use App\Models\Property\PropertyVariant;
use App\Models\Setting\Setting;
use App\Parser\Property\PropertyVariantParser;
use App\Services\Constant\Property\PropertyStatus;
use Illuminate\Http\Request;

class PropertyVariantController extends Controller
{
    public function get(Request $request)
    {
        $request->merge(['statusId' => PropertyStatus::ACTIVE_ID]);

        $properties = PropertyVariant::filter($request)
            ->with(['parent', 'type', 'photos', 'addresses','seo'])
            ->getOrPaginate($request);

        return success(PropertyVariantParser::briefs($properties), pagination: pagination($properties));
    }

    public function detail($idOrSlug)
    {
        $property = PropertyVariant::where('statusId', PropertyStatus::ACTIVE_ID)
        ->bySlugOrId($idOrSlug)    
        ->with([
                'parent', 'type', 'sourceType', 'addresses', 'guestInfo', 'rooms.roomType', 'rooms.bedType',
                'availability', 'pricing', 'descriptions', 'photos', 'amenities', 'tags', 'seo', 'acf',
            ])
            ->first();

        if (!$property) {
            errPropertyVariantGet();
        }

        return success(PropertyVariantParser::first($property));
    }

    /**
     * Find other properties near the given property, based on lat/lng distance.
     * Radius & result limit are configurable via the `settings` table.
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function nearby($id)
    {
        $property = PropertyVariant::where('statusId', PropertyStatus::ACTIVE_ID)->with('addresses')->find($id);
        if (!$property) {
            errPropertyVariantGet();
        }

        $origin = $property->addresses->first();
        if (!$origin || !$origin->latitude || !$origin->longitude) {
            return success([]);
        }

        $radiusKm = (float)(Setting::ofName('property_nearby_radius_km')->value('value') ?? 5);
        $limit = (int)(Setting::ofName('property_nearby_limit')->value('value') ?? 10);

        $haversine = "(6371 * acos(cos(radians($origin->latitude))
            * cos(radians(property_variant_addresses.latitude))
            * cos(radians(property_variant_addresses.longitude) - radians($origin->longitude))
            + sin(radians($origin->latitude)) * sin(radians(property_variant_addresses.latitude))))";

        $nearby = PropertyVariant::query()
            ->select('property_variants.*')
            ->selectRaw("$haversine AS distanceKm")
            ->join('property_variant_addresses', 'property_variant_addresses.propertyVariantId', '=', 'property_variants.id')
            ->where('property_variants.id', '!=', $property->id)
            ->where('property_variants.statusId', PropertyStatus::ACTIVE_ID)
            ->whereNotNull('property_variant_addresses.latitude')
            ->whereNotNull('property_variant_addresses.longitude')
            ->havingRaw("$haversine <= ?", [$radiusKm])
            ->orderBy('distanceKm')
            ->limit($limit)
            ->with(['type', 'photos', 'addresses'])
            ->get();

        return success($nearby->map(function ($item) {
            $brief = PropertyVariantParser::brief($item);
            $brief['distanceKm'] = round($item->distanceKm, 2);
            return $brief;
        }));
    }
}
