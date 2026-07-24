<?php

use App\Http\Controllers\Web\Admin\Setting\SettingAmenityCategoryController;
use App\Http\Controllers\Web\Admin\Setting\SettingAmenityController;
use App\Http\Controllers\Web\Admin\Setting\SettingController;
use App\Http\Controllers\Web\Admin\Setting\SettingPropertyFeatureController;

use Illuminate\Support\Facades\Route;

Route::prefix("settings")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::get('', [SettingController::class, 'get']);

        Route::prefix("amenities")
        ->middleware('auth.web.admin')
        ->group(function () {

            Route::get('', [SettingAmenityController::class, 'get']);
            Route::post('', [SettingAmenityController::class, 'create']);

            Route::prefix('categories')
                ->group(function () {
                    Route::get('', [SettingAmenityCategoryController::class, 'get']);
                    Route::post('', [SettingAmenityCategoryController::class, 'create']);
                    Route::get('{id}', [SettingAmenityCategoryController::class, 'detail']);
                    Route::put('{id}', [SettingAmenityCategoryController::class, 'update']);
                    Route::delete('{id}', [SettingAmenityCategoryController::class, 'delete']);
                });

            Route::get('{id}', [SettingAmenityController::class, 'detail']);
            Route::put('{id}', [SettingAmenityController::class, 'update']);
            Route::delete('{id}', [SettingAmenityController::class, 'delete']);
        });

        Route::prefix("property-features")
            ->group(function () {
                Route::get('', [SettingPropertyFeatureController::class, 'get']);
                Route::post('', [SettingPropertyFeatureController::class, 'create']);
                Route::get('{id}', [SettingPropertyFeatureController::class, 'detail']);
                Route::put('{id}', [SettingPropertyFeatureController::class, 'update']);
                Route::delete('{id}', [SettingPropertyFeatureController::class, 'delete']);
            });

        Route::get('{id}', [SettingController::class, 'detail']);
        Route::put('{id}', [SettingController::class, 'update']);
    });
