<?php

use App\Http\Controllers\Web\Admin\Redirection\RedirectionController;
use Illuminate\Support\Facades\Route;

Route::prefix("redirections")
    ->middleware('auth.web.admin')
    ->group(function () {

    Route::get('/', [RedirectionController::class, 'index']);
    Route::get('/{id}', [RedirectionController::class, 'show']);
    Route::post('/', [RedirectionController::class, 'store']);
    Route::put('/{id}', [RedirectionController::class, 'update']);
    Route::delete('/{id}', [RedirectionController::class, 'destroy']);
    
});
