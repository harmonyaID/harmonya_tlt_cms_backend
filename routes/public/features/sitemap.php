<?php

use App\Http\Controllers\Public\Sitemap\SitemapController;
use Illuminate\Support\Facades\Route;

Route::prefix("sitemap")
    ->group(function () {
        Route::get('', [SitemapController::class, 'get']);
        Route::post('generate', [SitemapController::class, 'generate'])->middleware('throttle:6,1');
    });

Route::get('sitemap.xml', [SitemapController::class, 'xml']);
