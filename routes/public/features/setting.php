<?php

use App\Http\Controllers\Public\Setting\SettingAmenityController;
use Illuminate\Support\Facades\Route;

Route::prefix("amenities")
    ->group(function () {
        Route::get('', [SettingAmenityController::class, 'get']);
    });
