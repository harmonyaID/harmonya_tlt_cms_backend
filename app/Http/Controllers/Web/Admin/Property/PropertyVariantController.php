<?php

namespace App\Http\Controllers\Web\Admin\Property;

use App\Algorithms\Property\PropertyVariantAlgo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasTrash;
use App\Http\Requests\Property\PropertyVariantRequest;
use App\Models\Property\PropertyVariant;
use App\Parser\Property\PropertyVariantParser;
use App\Services\Constant\Access\AccessPermissionName;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;

class PropertyVariantController extends Controller
{
    use HasTrash;

    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_VARIANT_VIEW);
                return $next($request);
            })->only(['get', 'detail', 'trash']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_VARIANT_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_VARIANT_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_VARIANT_DELETE);
                return $next($request);
            })->only(['delete', 'restore', 'forceDelete']);

        }
    }

    protected function trashModel(): string
    {
        return PropertyVariant::class;
    }

    protected function trashParser(): string
    {
        return PropertyVariantParser::class;
    }

    protected function trashActivityType(): string
    {
        return ActivityType::PROPERTY_VARIANT;
    }

    protected function trashLabel($item): string
    {
        return $item->nickname;
    }

    public function get(Request $request)
    {
        $properties = PropertyVariant::filter($request)
            ->with(['parent', 'type', 'photos', 'addresses', 'features'])
            ->getOrPaginate($request);

        return success(PropertyVariantParser::briefs($properties), pagination: pagination($properties));
    }

    public function detail($id)
    {
        $property = PropertyVariant::with([
            'parent', 'type', 'sourceType', 'addresses', 'guestInfo', 'rooms.roomType', 'rooms.bedType',
            'availability', 'pricing', 'descriptions', 'photos', 'amenities', 'tags', 'features', 'seo', 'acf',
        ])->find($id);

        if (!$property) {
            errPropertyVariantGet();
        }

        return success(PropertyVariantParser::first($property));
    }

    public function create(PropertyVariantRequest $request)
    {
        $algo = new PropertyVariantAlgo();
        return $algo->create($request);
    }

    public function update($id, PropertyVariantRequest $request)
    {
        $algo = new PropertyVariantAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new PropertyVariantAlgo((int)$id);
        return $algo->delete();
    }
}