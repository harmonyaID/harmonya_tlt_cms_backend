<?php

use App\Http\Controllers\Public\Faq\FaqController;
use App\Http\Controllers\Public\Faq\FaqTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix("faqs")
    ->group(function () {
        Route::get('', [FaqController::class, 'get']);
    });

Route::prefix("faq-types")
    ->group(function () {
        Route::get('', [FaqTypeController::class, 'get']);
    });
