<?php

namespace App\Http\Controllers\Web\Admin\Property;

use App\Algorithms\Property\PropertyTagAlgo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasTrash;
use App\Http\Requests\Component\ComponentRequest;
use App\Models\Property\PropertyTag;
use App\Parser\Property\PropertyTagParser;
use App\Services\Constant\Access\AccessPermissionName;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;

class PropertyTagController extends Controller
{
    use HasTrash;

    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_TAG_VIEW);
                return $next($request);
            })->only(['get', 'detail', 'trash']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_TAG_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_TAG_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_TAG_DELETE);
                return $next($request);
            })->only(['delete', 'restore', 'forceDelete']);

        }
    }

    public function get(Request $request)
    {
        $tags = PropertyTag::filter($request)->getOrPaginate($request);
        return success(PropertyTagParser::briefs($tags), pagination: pagination($tags));
    }

    public function detail($id)
    {
        $tag = PropertyTag::find($id);
        if (!$tag) {
            errPropertyTagGet();
        }

        return success(PropertyTagParser::first($tag));
    }

    public function create(ComponentRequest $request)
    {
        $algo = new PropertyTagAlgo();
        return $algo->create($request);
    }

    public function update($id, ComponentRequest $request)
    {
        $algo = new PropertyTagAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new PropertyTagAlgo((int)$id);
        return $algo->delete();
    }

    protected function trashModel(): string
    {
        return PropertyTag::class;
    }

    protected function trashParser(): string
    {
        return PropertyTagParser::class;
    }

    protected function trashActivityType(): string
    {
        return ActivityType::PROPERTY_TAG;
    }

    protected function trashLabel($item): string
    {
        return $item->name;
    }
}
