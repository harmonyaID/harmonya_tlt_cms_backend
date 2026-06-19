<?php

use App\Http\Controllers\Web\Admin\Staff\StaffAccessController;
use App\Http\Controllers\Web\Admin\Staff\StaffController;
use Illuminate\Support\Facades\Route;

Route::prefix("staffs")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::get('', [StaffController::class, 'get']);
        Route::post('', [StaffController::class, 'create']);
        Route::get('{id}', [StaffController::class, 'detail']);
        Route::put('{id}', [StaffController::class, 'update']);
        Route::delete('{id}', [StaffController::class, 'delete']);
        Route::patch('{id}/activation', [StaffController::class, 'activation']);
        Route::patch('{id}/password', [StaffController::class, 'changePassword']);

        Route::prefix('{id}/accesses')
            ->group(function () {

                Route::get('roles', [StaffAccessController::class, 'getRole']);
                Route::post('roles', [StaffAccessController::class, 'assignToRole']);
                Route::get('permissions', [StaffAccessController::class, 'getPermission']);
                Route::post('permissions', [StaffAccessController::class, 'assignToPermissions']);

            });

    });
