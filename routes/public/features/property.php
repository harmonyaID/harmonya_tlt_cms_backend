<?php

use App\Http\Controllers\Public\Property\PropertyController;
use App\Http\Controllers\Public\Property\PropertyReviewController;
use App\Http\Controllers\Public\Property\PropertyTagController;
use App\Http\Controllers\Public\Property\PropertyTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix("properties")
    ->group(function () {
        Route::get('', [PropertyController::class, 'get']);
        Route::get('reviews', [PropertyReviewController::class, 'get']);
        Route::get('{id}/nearby', [PropertyController::class, 'nearby']);
        Route::get('{id}', [PropertyController::class, 'detail']);
    });

Route::prefix("property-types")
    ->group(function () {
        Route::get('', [PropertyTypeController::class, 'get']);
    });

Route::prefix("property-tags")
    ->group(function () {
        Route::get('', [PropertyTagController::class, 'get']);
    });
