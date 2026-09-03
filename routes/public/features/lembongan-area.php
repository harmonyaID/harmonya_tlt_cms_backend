<?php

use App\Http\Controllers\Public\LembonganArea\LembonganAreaController;
use Illuminate\Support\Facades\Route;

Route::prefix("lembongan-areas")
    ->group(function () {

        Route::get('', [LembonganAreaController::class, 'get']);
        Route::get('{id}', [LembonganAreaController::class, 'detail']);
    });
