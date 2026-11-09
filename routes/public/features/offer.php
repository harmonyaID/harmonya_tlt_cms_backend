<?php

use App\Http\Controllers\Public\Offer\OfferCategoryController;
use App\Http\Controllers\Public\Offer\OfferController;
use App\Http\Controllers\Public\Offer\OfferTagController;
use Illuminate\Support\Facades\Route;

Route::prefix("offers")
    ->group(function () {
        Route::get('', [OfferController::class, 'get']);
        Route::get('{id}', [OfferController::class, 'detail']);
    });

Route::prefix("offer-categories")
    ->group(function () {
        Route::get('', [OfferCategoryController::class, 'get']);
    });

Route::prefix("offer-tags")
    ->group(function () {
        Route::get('', [OfferTagController::class, 'get']);
    });
