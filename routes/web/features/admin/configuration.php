<?php

use App\Http\Controllers\Web\Admin\Configuration\WebsiteInformationController;
use Illuminate\Support\Facades\Route;

Route::prefix("configurations")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::prefix('website')
            ->group(function () {
                Route::get('', [WebsiteInformationController::class, 'get']);
                Route::put('{id}/update', [WebsiteInformationController::class, 'update']);
            });
    });
