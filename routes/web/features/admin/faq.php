<?php

use App\Http\Controllers\Web\Admin\Faq\FaqController;
use Illuminate\Support\Facades\Route;

Route::prefix("faqs")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::get('', [FaqController::class, 'get']);
        Route::post('', [FaqController::class, 'create']);
        Route::get('{id}', [FaqController::class, 'detail']);
        Route::post('{id}', [FaqController::class, 'update']);
        Route::delete('{id}', [FaqController::class, 'delete']);

    });