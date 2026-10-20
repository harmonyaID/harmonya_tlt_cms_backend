<?php

namespace App\Http\Controllers\Web\Admin\Property;

use App\Algorithms\Property\PropertyVariantPhotoAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Property\PropertyVariantPhotoRequest;
use App\Models\Property\PropertyVariant;
use App\Models\Property\PropertyVariantPhoto;
use App\Services\Constant\Access\AccessPermissionName;

class PropertyVariantPhotoController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_VARIANT_UPDATE);
                return $next($request);
            })->only(['upload', 'delete']);

        }
    }

    public function upload($propertyVariantId, PropertyVariantPhotoRequest $request)
    {
        $property = PropertyVariant::find($propertyVariantId);
        if (!$property) {
            errPropertyVariantGet();
        }

        $algo = new PropertyVariantPhotoAlgo($property);
        return $algo->upload($request);
    }

    public function delete($propertyVariantId, $photoId)
    {
        $property = PropertyVariant::find($propertyVariantId);
        if (!$property) {
            errPropertyVariantGet();
        }

        $photo = PropertyVariantPhoto::where('propertyVariantId', $propertyVariantId)->find($photoId);
        if (!$photo) {
            errPropertyVariantPhotoGet();
        }

        $algo = new PropertyVariantPhotoAlgo($property);
        return $algo->delete($photo);
    }
}