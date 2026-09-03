<?php

use App\Http\Controllers\Web\Admin\Team\TeamMemberController;
use Illuminate\Support\Facades\Route;

Route::prefix("teams")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::get('', [TeamMemberController::class, 'get']);
        Route::post('', [TeamMemberController::class, 'create']);

        Route::prefix('trash')
            ->group(function () {
                Route::get('', [TeamMemberController::class, 'trash']);
                Route::post('{id}/restore', [TeamMemberController::class, 'restore']);
                Route::delete('{id}', [TeamMemberController::class, 'forceDelete']);
            });

        Route::get('{id}', [TeamMemberController::class, 'detail']);
        Route::post('{id}', [TeamMemberController::class, 'update']);
        Route::delete('{id}', [TeamMemberController::class, 'delete']);
    });
