<?php

use App\Http\Controllers\Web\Admin\IslandGuide\IslandGuideAreaController;
use App\Http\Controllers\Web\Admin\IslandGuide\IslandGuideController;
use App\Http\Controllers\Web\Admin\IslandGuide\IslandGuideTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('island-guides')->middleware('auth.web.admin')->group(function () {
    Route::get('', [IslandGuideController::class, 'get']);
    Route::post('', [IslandGuideController::class, 'create']);

    Route::prefix('types')->middleware('auth.web.admin')->group(function () {
        Route::get('', [IslandGuideTypeController::class, 'get']);
        Route::post('', [IslandGuideTypeController::class, 'create']);
        Route::prefix('trash')->group(function () {
            Route::get('', [IslandGuideTypeController::class, 'trash']);
            Route::post('{id}/restore', [IslandGuideTypeController::class, 'restore']);
            Route::delete('{id}', [IslandGuideTypeController::class, 'forceDelete']);
        });
        Route::get('{id}', [IslandGuideTypeController::class, 'detail']);
        Route::post('{id}', [IslandGuideTypeController::class, 'update']);
        Route::delete('{id}', [IslandGuideTypeController::class, 'delete']);
    });

    Route::prefix('areas')->middleware('auth.web.admin')->group(function () {
        Route::get('', [IslandGuideAreaController::class, 'get']);
        Route::post('', [IslandGuideAreaController::class, 'create']);
        Route::prefix('trash')->group(function () {
            Route::get('', [IslandGuideAreaController::class, 'trash']);
            Route::post('{id}/restore', [IslandGuideAreaController::class, 'restore']);
            Route::delete('{id}', [IslandGuideAreaController::class, 'forceDelete']);
        });
        Route::get('{id}', [IslandGuideAreaController::class, 'detail']);
        Route::post('{id}', [IslandGuideAreaController::class, 'update']);
        Route::delete('{id}', [IslandGuideAreaController::class, 'delete']);
    });

    Route::prefix('trash')->group(function () {
        Route::get('', [IslandGuideController::class, 'trash']);
        Route::post('{id}/restore', [IslandGuideController::class, 'restore']);
        Route::delete('{id}', [IslandGuideController::class, 'forceDelete']);
    });

    Route::get('{id}', [IslandGuideController::class, 'detail']);
    Route::post('{id}', [IslandGuideController::class, 'update']);
    Route::delete('{id}', [IslandGuideController::class, 'delete']);
});
