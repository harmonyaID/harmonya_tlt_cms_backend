<?php

use App\Http\Controllers\Web\Admin\Homepage\HomepageController;
use Illuminate\Support\Facades\Route;

Route::prefix("homepages")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::get('', [HomepageController::class, 'get']);
        Route::get('{id}', [HomepageController::class, 'detail']);
        Route::post('{id}', [HomepageController::class, 'update']);
    });
