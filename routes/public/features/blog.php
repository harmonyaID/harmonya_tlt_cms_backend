<?php

use App\Http\Controllers\Public\Blog\BlogCategoryController;
use App\Http\Controllers\Public\Blog\BlogController;
use App\Http\Controllers\Public\Blog\BlogTagController;
use Illuminate\Support\Facades\Route;

Route::prefix("blogs")
    ->group(function () {
        Route::get('', [BlogController::class, 'get']);
        Route::get('{id}', [BlogController::class, 'detail']);
    });

Route::prefix("blog-categories")
    ->group(function () {
        Route::get('', [BlogCategoryController::class, 'get']);
    });

Route::prefix("blog-tags")
    ->group(function () {
        Route::get('', [BlogTagController::class, 'get']);
    });
