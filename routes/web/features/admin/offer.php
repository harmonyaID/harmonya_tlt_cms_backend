<?php

use App\Http\Controllers\Web\Admin\Offer\OfferCategoryController;
use App\Http\Controllers\Web\Admin\Offer\OfferController;
use App\Http\Controllers\Web\Admin\Offer\OfferTagController;
use Illuminate\Support\Facades\Route;

Route::prefix("offers")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::get('', [OfferController::class, 'get']);
        Route::post('', [OfferController::class, 'create']);

        Route::prefix('categories')
            ->group(function () {
                Route::get('', [OfferCategoryController::class, 'get']);
                Route::post('', [OfferCategoryController::class, 'create']);
                Route::get('{id}', [OfferCategoryController::class, 'detail']);
                Route::put('{id}', [OfferCategoryController::class, 'update']);
                Route::delete('{id}', [OfferCategoryController::class, 'delete']);
            });

        Route::prefix('tags')
            ->group(function () {
                Route::get('', [OfferTagController::class, 'get']);
                Route::post('', [OfferTagController::class, 'create']);
                Route::get('{id}', [OfferTagController::class, 'detail']);
                Route::put('{id}', [OfferTagController::class, 'update']);
                Route::delete('{id}', [OfferTagController::class, 'delete']);
            });

        Route::prefix('trash')
            ->group(function () {
                Route::get('', [OfferController::class, 'trash']);
                Route::post('{id}/restore', [OfferController::class, 'restore']);
                Route::delete('{id}', [OfferController::class, 'forceDelete']);
            });

        Route::get('{id}', [OfferController::class, 'detail']);
        Route::post('{id}', [OfferController::class, 'update']);
        Route::delete('{id}', [OfferController::class, 'delete']);
    });
