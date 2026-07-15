<?php

use App\Http\Controllers\Web\Public\Homepage\HomepageController;
use Illuminate\Support\Facades\Route;

Route::prefix("homepages")
    ->group(function () {
        Route::get('', [HomepageController::class, 'get']);
        Route::get('{id}', [HomepageController::class, 'detail']);
    });
