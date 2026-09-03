<?php

use App\Http\Controllers\Web\Admin\LembonganArea\LembonganAreaController;
use Illuminate\Support\Facades\Route;

Route::prefix("lembongan-areas")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::get('', [LembonganAreaController::class, 'get']);
        Route::post('', [LembonganAreaController::class, 'create']);
        Route::get('{id}', [LembonganAreaController::class, 'detail']);
        Route::post('{id}', [LembonganAreaController::class, 'update']);
        Route::delete('{id}', [LembonganAreaController::class, 'delete']);
    });
