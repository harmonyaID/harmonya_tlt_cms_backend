<?php

namespace App\Http\Controllers\Public\Property;

use App\Http\Controllers\Controller;
use App\Models\Property\Property;
use App\Models\Setting\Setting;
use App\Parser\Property\PropertyParser;
use App\Services\Constant\Property\PropertyStatus;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function get(Request $request)
    {
        $request->merge(['statusId' => PropertyStatus::ACTIVE_ID]);

        $properties = Property::filter($request)
            ->with(['type', 'photos', 'addresses'])
            ->getOrPaginate($request);

        return success(PropertyParser::briefs($properties), pagination: pagination($properties));
    }

    public function detail($id)
    {
        $property = Property::where('statusId', PropertyStatus::ACTIVE_ID)
            ->with([
                'type', 'addresses', 'guestInfo', 'rooms.roomType', 'rooms.bedType',
                'availability', 'pricing', 'descriptions', 'photos', 'amenities', 'tags', 'seo',
            ])
            ->find($id);

        if (!$property) {
            errPropertyGet();
        }

        return success(PropertyParser::first($property));
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
        $property = Property::where('statusId', PropertyStatus::ACTIVE_ID)->with('addresses')->find($id);
        if (!$property) {
            errPropertyGet();
        }

        $origin = $property->addresses->first();
        if (!$origin || !$origin->latitude || !$origin->longitude) {
            return success([]);
        }

        $radiusKm = (float)(Setting::ofName('property_nearby_radius_km')->value('value') ?? 5);
        $limit = (int)(Setting::ofName('property_nearby_limit')->value('value') ?? 10);

        // Haversine formula to compute distance (in km) between two lat/lng points
        $haversine = "(6371 * acos(cos(radians($origin->latitude))
            * cos(radians(property_addresses.latitude))
            * cos(radians(property_addresses.longitude) - radians($origin->longitude))
            + sin(radians($origin->latitude)) * sin(radians(property_addresses.latitude))))";

        $nearby = Property::query()
            ->select('properties.*')
            ->selectRaw("$haversine AS distanceKm")
            ->join('property_addresses', 'property_addresses.propertyId', '=', 'properties.id')
            ->where('properties.id', '!=', $property->id)
            ->where('properties.statusId', PropertyStatus::ACTIVE_ID)
            ->whereNotNull('property_addresses.latitude')
            ->whereNotNull('property_addresses.longitude')
            ->havingRaw("$haversine <= ?", [$radiusKm])
            ->orderBy('distanceKm')
            ->limit($limit)
            ->with(['type', 'photos', 'addresses'])
            ->get();

        return success($nearby->map(function ($item) {
            $brief = PropertyParser::brief($item);
            $brief['distanceKm'] = round($item->distanceKm, 2);
            return $brief;
        }));
    }
}
