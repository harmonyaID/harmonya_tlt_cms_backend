<?php

use App\Http\Controllers\Public\Configuration\WebsiteInformationController;
use Illuminate\Support\Facades\Route;

Route::prefix("website-information")
    ->group(function () {
        Route::get('', [WebsiteInformationController::class, 'get']);
    });
