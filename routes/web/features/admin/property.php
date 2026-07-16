<?php

use App\Http\Controllers\Web\Admin\Property\PropertyBedTypeController;
use App\Http\Controllers\Web\Admin\Property\PropertyRoomTypeController;
use App\Http\Controllers\Web\Admin\Property\PropertyTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix("properties")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::prefix('types')
            ->group(function () {
                Route::get('', [PropertyTypeController::class, 'get']);
                Route::post('', [PropertyTypeController::class, 'create']);
                Route::get('{id}', [PropertyTypeController::class, 'detail']);
                Route::put('{id}', [PropertyTypeController::class, 'update']);
                Route::delete('{id}', [PropertyTypeController::class, 'delete']);
            });

        Route::prefix('room-types')
            ->group(function () {
                Route::get('', [PropertyRoomTypeController::class, 'get']);
                Route::post('', [PropertyRoomTypeController::class, 'create']);
                Route::get('{id}', [PropertyRoomTypeController::class, 'detail']);
                Route::put('{id}', [PropertyRoomTypeController::class, 'update']);
                Route::delete('{id}', [PropertyRoomTypeController::class, 'delete']);
            });

        Route::prefix('bed-types')
            ->group(function () {
                Route::get('', [PropertyBedTypeController::class, 'get']);
                Route::post('', [PropertyBedTypeController::class, 'create']);
                Route::get('{id}', [PropertyBedTypeController::class, 'detail']);
                Route::put('{id}', [PropertyBedTypeController::class, 'update']);
                Route::delete('{id}', [PropertyBedTypeController::class, 'delete']);
            });
    });