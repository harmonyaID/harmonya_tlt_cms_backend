<?php

use App\Http\Controllers\Web\Admin\TltReview\TltReviewController;
use Illuminate\Support\Facades\Route;

Route::prefix("tlt-reviews")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::get('', [TltReviewController::class, 'get']);
        Route::post('', [TltReviewController::class, 'create']);
        Route::get('{id}', [TltReviewController::class, 'detail']);
        Route::post('{id}', [TltReviewController::class, 'update']);
        Route::delete('{id}', [TltReviewController::class, 'delete']);

    });