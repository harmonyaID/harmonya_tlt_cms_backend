<?php

use App\Http\Controllers\Public\WebsiteContactForm\WebsiteContactFormController;
use Illuminate\Support\Facades\Route;

Route::prefix("website-contact-forms")
    ->group(function () {
        Route::post('', [WebsiteContactFormController::class, 'create']);
    });
