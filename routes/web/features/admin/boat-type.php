<?php

use App\Http\Controllers\Web\Admin\Boat\BoatTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix("boat-types")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::get('', [BoatTypeController::class, 'get']);
        Route::post('', [BoatTypeController::class, 'create']);
        Route::get('{id}', [BoatTypeController::class, 'detail']);
        Route::put('{id}', [BoatTypeController::class, 'update']);
        Route::delete('{id}', [BoatTypeController::class, 'delete']);

    });