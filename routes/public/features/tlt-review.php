<?php

use App\Http\Controllers\Public\TltReview\TltReviewController;
use Illuminate\Support\Facades\Route;

Route::prefix("tlt-reviews")
    ->group(function () {
        Route::get('', [TltReviewController::class, 'get']);
    });
