<?php

use App\Http\Controllers\Web\Admin\Access\AccessController;
use App\Http\Controllers\Web\Admin\Access\AccessPermissionController;
use App\Http\Controllers\Web\Admin\Access\AccessRoleController;
use Illuminate\Support\Facades\Route;

Route::prefix("accesses")
    ->middleware('auth.web.admin:api')
    ->group(function () {

        // Static
        Route::get('group', [AccessController::class, 'getGroup']);

        Route::get("roles", [AccessRoleController::class, "get"]);
        Route::get("roles/{id}/permissions", [AccessRoleController::class, "mappingPermission"]);
        Route::post("roles/{id}/permissions", [AccessRoleController::class, "saveMappingPermission"]);
        Route::get("permissions", [AccessPermissionController::class, "get"]);

    });
