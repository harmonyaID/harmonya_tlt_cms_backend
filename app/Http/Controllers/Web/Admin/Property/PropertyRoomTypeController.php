<?php

namespace App\Http\Controllers\Web\Admin\Property;

use App\Algorithms\Property\PropertyRoomTypeAlgo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasTrash;
use App\Http\Requests\Component\ComponentRequest;
use App\Models\Property\PropertyRoomType;
use App\Parser\Property\PropertyRoomTypeParser;
use App\Services\Constant\Access\AccessPermissionName;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;

class PropertyRoomTypeController extends Controller
{
    use HasTrash;

    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_ROOM_TYPE_VIEW);
                return $next($request);
            })->only(['get', 'detail', 'trash']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_ROOM_TYPE_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_ROOM_TYPE_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_ROOM_TYPE_DELETE);
                return $next($request);
            })->only(['delete', 'restore', 'forceDelete']);

        }
    }

    public function get(Request $request)
    {
        $types = PropertyRoomType::filter($request)->getOrPaginate($request);
        return success(PropertyRoomTypeParser::briefs($types), pagination: pagination($types));
    }

    public function detail($id)
    {
        $type = PropertyRoomType::find($id);
        if (!$type) {
            errPropertyRoomTypeGet();
        }

        return success(PropertyRoomTypeParser::first($type));
    }

    public function create(ComponentRequest $request)
    {
        $algo = new PropertyRoomTypeAlgo();
        return $algo->create($request);
    }

    public function update($id, ComponentRequest $request)
    {
        $algo = new PropertyRoomTypeAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new PropertyRoomTypeAlgo((int)$id);
        return $algo->delete();
    }

    protected function trashModel(): string
    {
        return PropertyRoomType::class;
    }

    protected function trashParser(): string
    {
        return PropertyRoomTypeParser::class;
    }

    protected function trashActivityType(): string
    {
        return ActivityType::PROPERTY_ROOM_TYPE;
    }

    protected function trashLabel($item): string
    {
        return $item->name;
    }
}
