<?php

use App\Http\Controllers\Public\Page\PageController;
use Illuminate\Support\Facades\Route;

Route::prefix("pages")
    ->group(function () {
        Route::get('{id}', [PageController::class, 'detail']);
    });
