<?php

use App\Http\Controllers\Public\Menu\MenuController;
use Illuminate\Support\Facades\Route;

Route::prefix("menus")
    ->group(function () {
        Route::get('{handle}', [MenuController::class, 'detail']);
    });
