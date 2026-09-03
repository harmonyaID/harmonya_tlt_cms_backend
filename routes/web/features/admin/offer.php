<?php

use App\Http\Controllers\Web\Admin\Offer\OfferController;
use Illuminate\Support\Facades\Route;

Route::prefix("offers")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::get('', [OfferController::class, 'get']);
        Route::post('', [OfferController::class, 'create']);

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
