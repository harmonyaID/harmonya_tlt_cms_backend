<?php

use App\Http\Controllers\Web\Admin\Page\PageController;
use App\Http\Controllers\Web\Admin\Staff\StaffAccessController;
use App\Http\Controllers\Web\Admin\Staff\StaffController;
use Illuminate\Support\Facades\Route;

Route::prefix("pages")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::get('/', [PageController::class, 'get']);
        Route::post('/', [PageController::class, 'create']);
        Route::get('{id}', [PageController::class, 'detail']);
        Route::put('{id}', [PageController::class, 'update']);
        Route::delete('{id}', [PageController::class, 'delete']);
    });
