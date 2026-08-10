<?php

namespace App\Http\Controllers\Web\Admin\Component;

use App\Http\Controllers\Controller;
use App\Services\Constant\Global\MailStatus;
use App\Services\Constant\MediaPartner\MediaPartnerType;
use App\Services\Constant\Menu\MenuItemType;
use App\Services\Constant\Property\PropertyAddressType;
use App\Services\Constant\Property\PropertyAdvanceNoticeUnit;
use App\Services\Constant\Property\PropertyAvailabilityType;
use App\Services\Constant\Property\PropertyCleaningFeeType;
use App\Services\Constant\Property\PropertyCleaningStatus;
use App\Services\Constant\Property\PropertyGuestySyncStatus;
use App\Services\Constant\Property\PropertyListingType;
use App\Services\Constant\Property\PropertySourceType;
use App\Services\Constant\Property\PropertyStatus;
use App\Services\Constant\Property\PropertyUnitType;

class ComponentStaticController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function getStatusForm()
    {
        return success(MailStatus::get());
    }

    public function getMediaPartnerType()
    {
        return success(MediaPartnerType::get());
    }
    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function getPropertyUnitTypes()
    {
        return success(PropertyUnitType::get());
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function getPropertyListingTypes()
    {
        return success(PropertyListingType::get());
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function getPropertyStatuses()
    {
        return success(PropertyStatus::get());
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function getPropertyAddressTypes()
    {
        return success(PropertyAddressType::get());
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function getPropertyAvailabilityTypes()
    {
        return success(PropertyAvailabilityType::get());
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function getPropertyCleaningFeeTypes()
    {
        return success(PropertyCleaningFeeType::get());
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function getPropertyCleaningStatuses()
    {
        return success(PropertyCleaningStatus::get());
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function getPropertyAdvanceNoticeUnits()
    {
        return success(PropertyAdvanceNoticeUnit::get());
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function getPropertyGuestySyncStatuses()
    {
        return success(PropertyGuestySyncStatus::get());
    }


    public function getPropertySourceTypes()
    {
        return success(PropertySourceType::get());
    }
    public function getMenuTypes()
    {
        return success(MenuItemType::get());
    }
}
