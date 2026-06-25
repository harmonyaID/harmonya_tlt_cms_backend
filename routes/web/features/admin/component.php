<?php

use App\Http\Controllers\Web\Admin\Component\ComponentCountryController;
use Illuminate\Support\Facades\Route;

Route::prefix("components")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::prefix('countries')
            ->group(function () {
                Route::get('', [ComponentCountryController::class, 'get']);
            });
    });
