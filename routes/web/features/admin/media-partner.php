<?php

use App\Http\Controllers\Web\Admin\MediaPartner\MediaPartnerController;
use Illuminate\Support\Facades\Route;

Route::prefix("media-partners")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::get('', [MediaPartnerController::class, 'get']);
        Route::post('', [MediaPartnerController::class, 'create']);
        Route::get('{id}', [MediaPartnerController::class, 'detail']);
        Route::post('{id}', [MediaPartnerController::class, 'update']);
        Route::delete('{id}', [MediaPartnerController::class, 'delete']);

    });