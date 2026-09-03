<?php

use App\Http\Controllers\Web\Admin\Boat\BoatContactFormController;
use Illuminate\Support\Facades\Route;

Route::prefix("boat-contact-forms")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::get('', [BoatContactFormController::class, 'get']);
        Route::post('', [BoatContactFormController::class, 'create']);
        Route::get('{id}', [BoatContactFormController::class, 'detail']);
        Route::delete('{id}', [BoatContactFormController::class, 'delete']);
        Route::patch('{id}/read', [BoatContactFormController::class, 'markAsRead']);
        Route::patch('{id}/status', [BoatContactFormController::class, 'changeStatus']);

    });