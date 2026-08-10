<?php

use App\Http\Controllers\Web\Admin\Menu\MenuController;
use Illuminate\Support\Facades\Route;

Route::prefix("menus")->middleware('auth.web.admin')->group(function () {
    Route::get('', [MenuController::class, 'get']);
    Route::post('', [MenuController::class, 'create']);
    Route::get('{id}', [MenuController::class, 'detail']);
    Route::post('{id}', [MenuController::class, 'update']);
    Route::delete('{id}', [MenuController::class, 'delete']);
});