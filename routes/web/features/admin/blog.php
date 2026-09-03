<?php

use App\Http\Controllers\Web\Admin\Blog\BlogCategoryController;
use App\Http\Controllers\Web\Admin\Blog\BlogController;
use App\Http\Controllers\Web\Admin\Blog\BlogTagController;
use Illuminate\Support\Facades\Route;

Route::prefix("blogs")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::get('', [BlogController::class, 'get']);
        Route::post('', [BlogController::class, 'create']);

        Route::prefix('categories')
            ->group(function () {
                Route::get('', [BlogCategoryController::class, 'get']);
                Route::post('', [BlogCategoryController::class, 'create']);
                Route::get('{id}', [BlogCategoryController::class, 'detail']);
                Route::put('{id}', [BlogCategoryController::class, 'update']);
                Route::delete('{id}', [BlogCategoryController::class, 'delete']);
            });

        Route::prefix("tags")
            ->middleware('auth.web.admin')
            ->group(function () {
                Route::get('', [BlogTagController::class, 'get']);
                Route::post('', [BlogTagController::class, 'create']);
                Route::get('{id}', [BlogTagController::class, 'detail']);
                Route::put('{id}', [BlogTagController::class, 'update']);
                Route::delete('{id}', [BlogTagController::class, 'delete']);
            });

        Route::prefix('trash')
            ->group(function () {
                Route::get('', [BlogController::class, 'trash']);
                Route::post('{id}/restore', [BlogController::class, 'restore']);
                Route::delete('{id}', [BlogController::class, 'forceDelete']);
            });

        Route::get('{id}', [BlogController::class, 'detail']);
        Route::post('{id}', [BlogController::class, 'update']);
        Route::delete('{id}', [BlogController::class, 'delete']);
    });
