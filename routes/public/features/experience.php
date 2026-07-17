<?php

use App\Http\Controllers\Public\Experience\ExperienceController;
use App\Http\Controllers\Public\Experience\ExperienceInquiryFormController;
use Illuminate\Support\Facades\Route;

Route::prefix("experiences")
    ->group(function () {
        Route::get('', [ExperienceController::class, 'get']);
        Route::get('{id}', [ExperienceController::class, 'detail']);
    });

Route::prefix("experience-inquiry-forms")
    ->group(function () {
        Route::post('', [ExperienceInquiryFormController::class, 'create']);
    });
