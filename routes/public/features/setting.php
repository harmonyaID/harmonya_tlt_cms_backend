<?php

use App\Http\Controllers\Public\Setting\SettingAmenityController;
use App\Http\Controllers\Public\Setting\SettingPropertyFeatureController;
use Illuminate\Support\Facades\Route;

Route::prefix("amenities")
    ->group(function () {
        Route::get('', [SettingAmenityController::class, 'get']);
    });

Route::prefix("property-features")
    ->group(function () {
        Route::get('', [SettingPropertyFeatureController::class, 'get']);
    });
