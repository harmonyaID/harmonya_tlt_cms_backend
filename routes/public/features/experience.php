<?php

use App\Http\Controllers\Public\Experience\ExperienceAreaController;
use App\Http\Controllers\Public\Experience\ExperienceController;
use App\Http\Controllers\Public\Experience\ExperienceInquiryFormController;
use App\Http\Controllers\Public\Experience\ExperienceTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix("experiences")
    ->group(function () {
        Route::get('', [ExperienceController::class, 'get']);
        Route::get('{id}', [ExperienceController::class, 'detail']);
    });

Route::prefix("experience-types")
    ->group(function () {
        Route::get('', [ExperienceTypeController::class, 'get']);
        Route::get('{id}', [ExperienceTypeController::class, 'detail']);
    });

Route::prefix("experience-areas")
    ->group(function () {
        Route::get('', [ExperienceAreaController::class, 'get']);
        Route::get('{id}', [ExperienceAreaController::class, 'detail']);
    });

Route::prefix("experience-inquiry-forms")
    ->group(function () {
        Route::post('', [ExperienceInquiryFormController::class, 'create']);
    });
