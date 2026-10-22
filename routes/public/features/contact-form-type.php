<?php

use App\Http\Controllers\Public\Component\ComponentContactFormTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix("contact-form-types")
    ->group(function () {
        Route::get('', [ComponentContactFormTypeController::class, 'types']);
    });

Route::prefix("contact-form-inquiry-types")
    ->group(function () {
        Route::get('', [ComponentContactFormTypeController::class, 'inquiryTypes']);
    });
