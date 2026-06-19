<?php

use App\Http\Controllers\Web\Admin\Auth\AuthenticationController;
use Illuminate\Support\Facades\Route;

Route::prefix("auths")
    ->group(function () {

        Route::post("login", [AuthenticationController::class, "login"]);
        Route::post("forgot-password", [AuthenticationController::class, "forgotPassword"]);
        Route::post("forgot-password/{token}", [AuthenticationController::class, "resetPassword"]);

        Route::middleware('auth.web.admin:api')
            ->group(function () {

                Route::get('profile', [AuthenticationController::class, "profile"]);
                Route::post('logout', [AuthenticationController::class, "logout"]);
                Route::patch('change-password', [AuthenticationController::class, "changePassword"]);

            });

    });
