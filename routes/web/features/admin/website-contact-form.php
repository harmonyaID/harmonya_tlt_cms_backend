<?php

use App\Http\Controllers\Web\Admin\WebsiteContactForm\WebsiteContactFormController;
use Illuminate\Support\Facades\Route;

Route::prefix("website-contact-forms")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::get('', [WebsiteContactFormController::class, 'get']);
        Route::post('', [WebsiteContactFormController::class, 'create']);
        Route::get('{id}', [WebsiteContactFormController::class, 'detail']);
        Route::put('{id}', [WebsiteContactFormController::class, 'update']);
        Route::delete('{id}', [WebsiteContactFormController::class, 'delete']);
        Route::patch('{id}/read', [WebsiteContactFormController::class, 'markAsRead']);

    });