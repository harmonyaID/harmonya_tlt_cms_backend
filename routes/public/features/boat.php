<?php

use App\Http\Controllers\Public\Boat\BoatContactFormController;
use App\Http\Controllers\Public\Boat\BoatController;
use Illuminate\Support\Facades\Route;

Route::prefix("boats")
    ->group(function () {
        Route::get('', [BoatController::class, 'get']);
        Route::get('{id}', [BoatController::class, 'detail']);
    });

Route::prefix("boat-contact-forms")
    ->group(function () {
        Route::post('', [BoatContactFormController::class, 'create']);
    });
