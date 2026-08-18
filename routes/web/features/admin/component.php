<?php

use App\Http\Controllers\Web\Admin\Component\ComponentContactFormTypeController;
use App\Http\Controllers\Web\Admin\Component\ComponentCountryController;
use App\Http\Controllers\Web\Admin\Component\ComponentStaticController;
use Illuminate\Support\Facades\Route;

Route::prefix("components")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::prefix('statics')
            ->group(function () {
                Route::get('status-form', [ComponentStaticController::class, 'getStatusForm']);
                Route::get('media-partner-type', [ComponentStaticController::class, 'getMediaPartnerType']);
                Route::get('property-unit-types', [ComponentStaticController::class, 'getPropertyUnitTypes']);
                Route::get('property-listing-types', [ComponentStaticController::class, 'getPropertyListingTypes']);
                Route::get('property-statuses', [ComponentStaticController::class, 'getPropertyStatuses']);
                Route::get('property-address-types', [ComponentStaticController::class, 'getPropertyAddressTypes']);
                Route::get('property-availability-types', [ComponentStaticController::class, 'getPropertyAvailabilityTypes']);
                Route::get('property-cleaning-fee-types', [ComponentStaticController::class, 'getPropertyCleaningFeeTypes']);
                Route::get('property-cleaning-statuses', [ComponentStaticController::class, 'getPropertyCleaningStatuses']);
                Route::get('property-advance-notice-units', [ComponentStaticController::class, 'getPropertyAdvanceNoticeUnits']);
                Route::get('property-guesty-sync-statuses', [ComponentStaticController::class, 'getPropertyGuestySyncStatuses']);
                Route::get('menu-types', [ComponentStaticController::class, 'getMenuTypes']);
            });

        Route::prefix('contact-form-types')
            ->group(function () {
                Route::get('', [ComponentContactFormTypeController::class, 'get']);
                Route::post('', [ComponentContactFormTypeController::class, 'create']);
                Route::put('{componentContactFormType}', [ComponentContactFormTypeController::class, 'update']);
                Route::delete('{componentContactFormType}', [ComponentContactFormTypeController::class, 'delete']);
            });

        Route::prefix('countries')
            ->group(function () {
                Route::get('', [ComponentCountryController::class, 'get']);
            });
    });
