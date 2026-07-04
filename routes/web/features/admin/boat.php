<?php

use App\Http\Controllers\Web\Admin\Boat\BoatController;
use Illuminate\Support\Facades\Route;

Route::prefix("boats")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::get('', [BoatController::class, 'get']);
        Route::post('', [BoatController::class, 'create']);
        Route::get('{id}', [BoatController::class, 'detail']);
        Route::post('{id}', [BoatController::class, 'update']);
        Route::delete('{id}', [BoatController::class, 'delete']);

    });