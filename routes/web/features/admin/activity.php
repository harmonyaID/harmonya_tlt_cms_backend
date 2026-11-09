<?php

use App\Http\Controllers\Web\Admin\Activity\ActivityController;
use Illuminate\Support\Facades\Route;

Route::prefix("activities")
    ->middleware('auth.web.admin:api')
    ->group(function () {
        Route::get('', [ActivityController::class, 'get']);

        Route::get('settings/type', [ActivityController::class, 'getType']);
        Route::get('settings/action', [ActivityController::class, 'getAction']);
    });
