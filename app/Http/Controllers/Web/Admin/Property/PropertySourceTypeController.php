<?php

namespace App\Http\Controllers\Web\Admin\Property;

use App\Algorithms\Property\PropertySourceTypeAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Component\ComponentRequest;
use App\Models\Property\PropertySourceType;
use App\Parser\Property\PropertySourceTypeParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class PropertySourceTypeController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_SOURCE_TYPE_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_SOURCE_TYPE_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_SOURCE_TYPE_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_SOURCE_TYPE_DELETE);
                return $next($request);
            })->only(['delete']);

        }
    }

    public function get(Request $request)
    {
        $types = PropertySourceType::filter($request)->getOrPaginate($request);
        return success(PropertySourceTypeParser::briefs($types), pagination: pagination($types));
    }

    public function detail($id)
    {
        $type = PropertySourceType::find($id);
        if (!$type) {
            errPropertySourceTypeGet();
        }

        return success(PropertySourceTypeParser::first($type));
    }

    public function create(ComponentRequest $request)
    {
        $algo = new PropertySourceTypeAlgo();
        return $algo->create($request);
    }

    public function update($id, ComponentRequest $request)
    {
        $algo = new PropertySourceTypeAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new PropertySourceTypeAlgo((int)$id);
        return $algo->delete();
    }
}
