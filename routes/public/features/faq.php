<?php

use App\Http\Controllers\Public\Faq\FaqController;
use Illuminate\Support\Facades\Route;

Route::prefix("faqs")
    ->group(function () {
        Route::get('', [FaqController::class, 'get']);
    });
