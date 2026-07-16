<?php

namespace App\Http\Controllers\Web\Admin\Property;

use App\Algorithms\Property\PropertyPhotoAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Property\PropertyPhotoRequest;
use App\Models\Property\Property;
use App\Models\Property\PropertyPhoto;
use App\Services\Constant\Access\AccessPermissionName;

class PropertyPhotoController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_UPDATE);
                return $next($request);
            })->only(['upload', 'delete']);

        }
    }

    public function upload($propertyId, PropertyPhotoRequest $request)
    {
        $property = Property::find($propertyId);
        if (!$property) {
            errPropertyGet();
        }

        $algo = new PropertyPhotoAlgo($property);
        return $algo->upload($request);
    }

    public function delete($propertyId, $photoId)
    {
        $property = Property::find($propertyId);
        if (!$property) {
            errPropertyGet();
        }

        $photo = PropertyPhoto::where('propertyId', $propertyId)->find($photoId);
        if (!$photo) {
            errPropertyPhotoGet();
        }

        $algo = new PropertyPhotoAlgo($property);
        return $algo->delete($photo);
    }
}