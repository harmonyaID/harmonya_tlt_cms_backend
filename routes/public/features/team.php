<?php

use App\Http\Controllers\Public\Team\TeamMemberController;
use Illuminate\Support\Facades\Route;

Route::prefix("teams")
    ->group(function () {
        Route::get('', [TeamMemberController::class, 'get']);
        Route::get('roles', [TeamMemberController::class, 'getRoles']);
    });
