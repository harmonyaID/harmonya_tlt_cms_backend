<?php

namespace App\Http\Controllers\Public\Property;

use App\Http\Controllers\Controller;
use App\Models\Property\Property;
use App\Models\Setting\Setting;
use App\Parser\Property\PropertyParser;
use App\Services\Constant\Property\PropertyStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyController extends Controller
{
    public function get(Request $request)
    {
        $request->merge(['statusId' => PropertyStatus::ACTIVE_ID]);

        $properties = Property::filter($request)
            ->with([
                'type', 'photos', 'variants.photos', 'addresses', 'seo', 'features',
                'tags', 'amenities', 'rooms.roomType', 'rooms.bedType',
            ])
            ->getOrPaginate($request);

        return success(PropertyParser::briefs($properties), pagination: pagination($properties));
    }

    public function detail($idOrSlug)
    {
        $property = Property::where('statusId', PropertyStatus::ACTIVE_ID)
        ->bySlugOrId($idOrSlug)    
        ->with([
                'type', 'sourceType', 'addresses', 'guestInfo', 'rooms.roomType', 'rooms.bedType',
                'availability', 'pricing', 'descriptions', 'photos', 'amenities', 'tags', 'seo', 'acf', 'features',
                'variants.photos',
            ])
            ->first();

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

        $latitude = (float) $origin->latitude;
        $longitude = (float) $origin->longitude;

        // LEAST/GREATEST keep acos() inside [-1, 1]: float rounding can push it just above 1
        // for an address at the exact same spot, which makes acos() return NULL.
        $haversine = "6371 * acos(LEAST(1, GREATEST(-1,
            cos(radians(?)) * cos(radians(latitude))
            * cos(radians(longitude) - radians(?))
            + sin(radians(?)) * sin(radians(latitude)))))";

        // One row per property (its closest address), so a property with several
        // addresses is not listed more than once.
        $distances = DB::table('property_addresses')
            ->selectRaw("propertyId, MIN($haversine) AS distanceKm", [$latitude, $longitude, $latitude])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->groupBy('propertyId');

        $nearby = Property::query()
            ->select('properties.*', 'nearby.distanceKm')
            ->joinSub($distances, 'nearby', 'nearby.propertyId', '=', 'properties.id')
            ->where('properties.id', '!=', $property->id)
            ->where('properties.statusId', PropertyStatus::ACTIVE_ID)
            ->where('nearby.distanceKm', '<=', $radiusKm)
            ->orderBy('nearby.distanceKm')
            ->limit($limit)
            ->with([
                'type', 'photos', 'variants.photos', 'addresses', 'seo', 'features',
                'tags', 'amenities', 'rooms.roomType', 'rooms.bedType',
            ])
            ->get();

        return success($nearby->map(function ($item) {
            $brief = PropertyParser::brief($item);
            $brief['distanceKm'] = round($item->distanceKm, 2);
            return $brief;
        }));
    }
}
