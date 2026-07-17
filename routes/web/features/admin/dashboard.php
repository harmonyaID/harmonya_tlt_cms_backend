<?php

use App\Http\Controllers\Web\Admin\Dashboard\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix("dashboard")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::get('metrics', [DashboardController::class, 'metrics']);
    });