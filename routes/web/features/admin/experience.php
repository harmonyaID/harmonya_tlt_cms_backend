<?php

use App\Http\Controllers\Web\Admin\Experience\ExperienceAreaController;
use App\Http\Controllers\Web\Admin\Experience\ExperienceController;
use App\Http\Controllers\Web\Admin\Experience\ExperienceInquiryFormController;
use App\Http\Controllers\Web\Admin\Experience\ExperienceTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix("experiences")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::get('', [ExperienceController::class, 'get']);
        Route::post('', [ExperienceController::class, 'create']);

        Route::prefix("types")->middleware('auth.web.admin')->group(function () {
            Route::get('', [ExperienceTypeController::class, 'get']);
            Route::post('', [ExperienceTypeController::class, 'create']);
            Route::get('{id}', [ExperienceTypeController::class, 'detail']);
            Route::post('{id}', [ExperienceTypeController::class, 'update']);
            Route::delete('{id}', [ExperienceTypeController::class, 'delete']);
        });

        Route::prefix("areas")->middleware('auth.web.admin')->group(function () {
            Route::get('', [ExperienceAreaController::class, 'get']);
            Route::post('', [ExperienceAreaController::class, 'create']);
            Route::get('{id}', [ExperienceAreaController::class, 'detail']);
            Route::post('{id}', [ExperienceAreaController::class, 'update']);
            Route::delete('{id}', [ExperienceAreaController::class, 'delete']);
        });

        Route::prefix("inquiry-forms")
            ->middleware('auth.web.admin')
            ->group(function () {
                Route::get('', [ExperienceInquiryFormController::class, 'get']);
                Route::post('', [ExperienceInquiryFormController::class, 'create']);
                Route::get('{id}', [ExperienceInquiryFormController::class, 'detail']);
                Route::delete('{id}', [ExperienceInquiryFormController::class, 'delete']);
                Route::patch('{id}/status', [ExperienceInquiryFormController::class, 'changeStatus']);
            });


        Route::get('{id}', [ExperienceController::class, 'detail']);
        Route::post('{id}', [ExperienceController::class, 'update']);
        Route::delete('{id}', [ExperienceController::class, 'delete']);
    });
