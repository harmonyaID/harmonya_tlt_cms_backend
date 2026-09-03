<?php

use App\Http\Controllers\Web\Admin\Faq\FaqController;
use App\Http\Controllers\Web\Admin\Faq\FaqTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix("faqs")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::get('', [FaqController::class, 'get']);
        Route::post('', [FaqController::class, 'create']);

        Route::prefix('types')
            ->group(function () {
                Route::get('', [FaqTypeController::class, 'get']);
                Route::post('', [FaqTypeController::class, 'create']);

                Route::prefix('trash')
                    ->group(function () {
                        Route::get('', [FaqTypeController::class, 'trash']);
                        Route::post('{id}/restore', [FaqTypeController::class, 'restore']);
                        Route::delete('{id}', [FaqTypeController::class, 'forceDelete']);
                    });

                Route::get('{id}', [FaqTypeController::class, 'detail']);
                Route::put('{id}', [FaqTypeController::class, 'update']);
                Route::delete('{id}', [FaqTypeController::class, 'delete']);
            });

        Route::get('{id}', [FaqController::class, 'detail']);
        Route::put('{id}', [FaqController::class, 'update']);
        Route::delete('{id}', [FaqController::class, 'delete']);

    });
