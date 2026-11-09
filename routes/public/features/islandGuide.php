<?php

use App\Http\Controllers\Public\IslandGuide\IslandGuideAreaController;
use App\Http\Controllers\Public\IslandGuide\IslandGuideController;
use App\Http\Controllers\Public\IslandGuide\IslandGuideTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('island-guides')->group(function () {
    Route::get('', [IslandGuideController::class, 'get']);
    Route::get('{id}', [IslandGuideController::class, 'detail']);
});

Route::prefix('island-guide-types')->group(function () {
    Route::get('', [IslandGuideTypeController::class, 'get']);
    Route::get('{id}', [IslandGuideTypeController::class, 'detail']);
});

Route::prefix('island-guide-areas')->group(function () {
    Route::get('', [IslandGuideAreaController::class, 'get']);
    Route::get('{id}', [IslandGuideAreaController::class, 'detail']);
});
