<?php

use App\Http\Controllers\Public\Component\ComponentContactFormTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix("components/contact-form-types")
    ->group(function () {
        Route::get('', [ComponentContactFormTypeController::class, 'types']);
    });

Route::prefix("components/contact-form-inquiry-types")
    ->group(function () {
        Route::get('', [ComponentContactFormTypeController::class, 'inquiryTypes']);
    });
