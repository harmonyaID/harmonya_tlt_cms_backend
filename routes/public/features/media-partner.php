<?php

use App\Http\Controllers\Public\MediaPartner\MediaPartnerController;
use Illuminate\Support\Facades\Route;

Route::prefix("media-partners")
    ->group(function () {
        Route::get('', [MediaPartnerController::class, 'get']);
    });
