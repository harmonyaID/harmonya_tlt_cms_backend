<?php

use App\Http\Controllers\Web\Admin\Language\GroupController;
use App\Http\Controllers\Web\Admin\Language\LanguageController;
use App\Http\Controllers\Web\Admin\Language\TranslationController;
use Illuminate\Support\Facades\Route;

Route::prefix("languages")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::get('', [LanguageController::class, 'get']);
        Route::post('', [LanguageController::class, 'create']);
        Route::post('status/main', [LanguageController::class, 'changeMainStatus']);
        Route::put('{id}', [LanguageController::class, 'update']);
        Route::delete('{id}', [LanguageController::class, 'delete']);

        Route::prefix('group')
            ->group(function () {
                Route::get('', [GroupController::class, 'get']);
                Route::post('{groupId}/update', [GroupController::class, 'update']);

            });

        Route::prefix('translation')
            ->group(function () {
                Route::get('', [TranslationController::class, 'get']);
                Route::post('{translationId}/update', [TranslationController::class, 'update']);
                Route::post('{translationId}/update/{locale}', [TranslationController::class, 'updateSpecificLang']);

            });


    });
