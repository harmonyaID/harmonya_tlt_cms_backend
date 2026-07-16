<?php

namespace App\Http\Controllers\Web\Admin\Property;

use App\Algorithms\Property\PropertyTagAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Component\ComponentRequest;
use App\Models\Property\PropertyTag;
use App\Parser\Property\PropertyTagParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class PropertyTagController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_TAG_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

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
            })->only(['delete']);

        }
    }

    public function get(Request $request)
    {
        $types = PropertyTag::filter($request)->getOrPaginate($request);
        return success(PropertyTagParser::briefs($types), pagination: pagination($types));
    }

    public function detail($id)
    {
        $type = PropertyTag::find($id);
        if (!$type) {
            errPropertyTagGet();
        }

        return success(PropertyTagParser::first($type));
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
}