<?php

namespace App\Http\Controllers\Web\Admin\Property;

use App\Algorithms\Property\PropertyBedTypeAlgo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasTrash;
use App\Http\Requests\Component\ComponentRequest;
use App\Models\Property\PropertyBedType;
use App\Parser\Property\PropertyBedTypeParser;
use App\Services\Constant\Access\AccessPermissionName;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;

class PropertyBedTypeController extends Controller
{
    use HasTrash;

    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_BED_TYPE_VIEW);
                return $next($request);
            })->only(['get', 'detail', 'trash']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_BED_TYPE_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_BED_TYPE_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_BED_TYPE_DELETE);
                return $next($request);
            })->only(['delete', 'restore', 'forceDelete']);

        }
    }

    public function get(Request $request)
    {
        $types = PropertyBedType::filter($request)->getOrPaginate($request);
        return success(PropertyBedTypeParser::briefs($types), pagination: pagination($types));
    }

    public function detail($id)
    {
        $type = PropertyBedType::find($id);
        if (!$type) {
            errPropertyBedTypeGet();
        }

        return success(PropertyBedTypeParser::first($type));
    }

    public function create(ComponentRequest $request)
    {
        $algo = new PropertyBedTypeAlgo();
        return $algo->create($request);
    }

    public function update($id, ComponentRequest $request)
    {
        $algo = new PropertyBedTypeAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new PropertyBedTypeAlgo((int)$id);
        return $algo->delete();
    }

    protected function trashModel(): string
    {
        return PropertyBedType::class;
    }

    protected function trashParser(): string
    {
        return PropertyBedTypeParser::class;
    }

    protected function trashActivityType(): string
    {
        return ActivityType::PROPERTY_BED_TYPE;
    }

    protected function trashLabel($item): string
    {
        return $item->name;
    }
}
