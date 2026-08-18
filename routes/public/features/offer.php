<?php

use App\Http\Controllers\Public\Offer\OfferController;
use Illuminate\Support\Facades\Route;

Route::prefix("offers")
    ->group(function () {
        Route::get('', [OfferController::class, 'get']);
        Route::get('{id}', [OfferController::class, 'detail']);
    });
