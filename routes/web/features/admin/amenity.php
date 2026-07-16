<?php

use App\Http\Controllers\Web\Admin\Amenity\AmenityCategoryController;
use App\Http\Controllers\Web\Admin\Amenity\AmenityController;
use Illuminate\Support\Facades\Route;

Route::prefix("amenities")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::get('', [AmenityController::class, 'get']);
        Route::post('', [AmenityController::class, 'create']);

        Route::prefix('categories')
            ->group(function () {
                Route::get('', [AmenityCategoryController::class, 'get']);
                Route::post('', [AmenityCategoryController::class, 'create']);
                Route::get('{id}', [AmenityCategoryController::class, 'detail']);
                Route::put('{id}', [AmenityCategoryController::class, 'update']);
                Route::delete('{id}', [AmenityCategoryController::class, 'delete']);
            });

        Route::get('{id}', [AmenityController::class, 'detail']);
        Route::put('{id}', [AmenityController::class, 'update']);
        Route::delete('{id}', [AmenityController::class, 'delete']);
    });