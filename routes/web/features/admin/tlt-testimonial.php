<?php

use App\Http\Controllers\Web\Admin\TltTestimonial\TltTestimonialController;
use Illuminate\Support\Facades\Route;

Route::prefix("tlt-testimonials")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::get('', [TltTestimonialController::class, 'get']);
        Route::post('', [TltTestimonialController::class, 'create']);

        Route::prefix('trash')
            ->group(function () {
                Route::get('', [TltTestimonialController::class, 'trash']);
                Route::post('{id}/restore', [TltTestimonialController::class, 'restore']);
                Route::delete('{id}', [TltTestimonialController::class, 'forceDelete']);
            });

        Route::get('{id}', [TltTestimonialController::class, 'detail']);
        Route::post('{id}', [TltTestimonialController::class, 'update']);
        Route::delete('{id}', [TltTestimonialController::class, 'delete']);
    });
