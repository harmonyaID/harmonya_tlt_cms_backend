<?php

use App\Http\Controllers\Public\TltTestimonial\TltTestimonialController;
use Illuminate\Support\Facades\Route;

Route::prefix("tlt-testimonials")
    ->group(function () {
        Route::get('', [TltTestimonialController::class, 'get']);
    });
