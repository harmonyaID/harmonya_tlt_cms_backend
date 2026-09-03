<?php

use App\Http\Controllers\Web\Admin\System\SystemCacheController;
use App\Http\Controllers\Web\Admin\System\SystemInformationController;
use Illuminate\Support\Facades\Route;

Route::prefix("system")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::get('information', [SystemInformationController::class, 'get']);

        Route::prefix('cache')
            ->group(function () {
                Route::post('clear', [SystemCacheController::class, 'artisanCache']);
                Route::post('config-clear', [SystemCacheController::class, 'artisanConfig']);
                Route::post('route-clear', [SystemCacheController::class, 'artisanRoute']);
                Route::post('view-clear', [SystemCacheController::class, 'artisanView']);
                Route::post('optimize', [SystemCacheController::class, 'artisanOptimize']);
                Route::post('optimize-clear', [SystemCacheController::class, 'artisanOptimizeClear']);
                Route::post('queue-restart', [SystemCacheController::class, 'artisanQueueRestart']);
            });
    });
