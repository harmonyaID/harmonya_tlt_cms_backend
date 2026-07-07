<?php

use App\Http\Controllers\Web\Admin\Blog\BlogController;
use App\Http\Controllers\Web\Admin\Experience\ExperienceCategoryController;
use App\Http\Controllers\Web\Admin\Experience\ExperienceController;
use App\Http\Controllers\Web\Admin\Experience\ExperienceInquiryFormController;
use App\Http\Controllers\Web\Admin\Experience\ExperienceTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix("experiences")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::get('', [ExperienceController::class, 'get']);
        Route::post('', [ExperienceController::class, 'create']);
        Route::get('{id}', [ExperienceController::class, 'detail']);
        Route::post('{id}', [ExperienceController::class, 'update']);
        Route::delete('{id}', [ExperienceController::class, 'delete']);

        Route::prefix("types")->middleware('auth.web.admin')->group(function () {
            Route::get('', [ExperienceTypeController::class, 'get']);
            Route::post('', [ExperienceTypeController::class, 'create']);
            Route::get('{id}', [ExperienceTypeController::class, 'detail']);
            Route::put('{id}', [ExperienceTypeController::class, 'update']);
            Route::delete('{id}', [ExperienceTypeController::class, 'delete']);
        });

        Route::prefix("categories")->middleware('auth.web.admin')->group(function () {
            Route::get('', [ExperienceCategoryController::class, 'get']);
            Route::post('', [ExperienceCategoryController::class, 'create']);
            Route::get('{id}', [ExperienceCategoryController::class, 'detail']);
            Route::put('{id}', [ExperienceCategoryController::class, 'update']);
            Route::delete('{id}', [ExperienceCategoryController::class, 'delete']);
        });

        Route::prefix("inquiry-forms")
            ->middleware('auth.web.admin')
            ->group(function () {
                Route::get('', [ExperienceInquiryFormController::class, 'get']);
                Route::post('', [ExperienceInquiryFormController::class, 'create']);
                Route::get('{id}', [ExperienceInquiryFormController::class, 'detail']);
                Route::delete('{id}', [ExperienceInquiryFormController::class, 'delete']);
            });
    });
