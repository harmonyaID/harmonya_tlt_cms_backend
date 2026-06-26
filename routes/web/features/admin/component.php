<?php

use App\Http\Controllers\Web\Admin\Component\ComponentContactFormTypeController;
use App\Http\Controllers\Web\Admin\Component\ComponentCountryController;
use Illuminate\Support\Facades\Route;

Route::prefix("components")
    ->middleware('auth.web.admin')
    ->group(function () {

    
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
