<?php

use App\Http\Controllers\Web\Admin\Notification\NotificationController;
use App\Http\Controllers\Web\Admin\Notification\NotificationSettingController;
use Illuminate\Support\Facades\Route;

Route::prefix('notifications')
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::get('', [NotificationController::class, 'get']);
        Route::get('unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('read-all', [NotificationController::class, 'readAll']);

        Route::post('devices', [NotificationController::class, 'registerDevice']);
        Route::delete('devices', [NotificationController::class, 'removeDevice']);

        Route::get('settings', [NotificationSettingController::class, 'get']);
        Route::put('settings/{id}', [NotificationSettingController::class, 'update']);

        Route::post('{id}/read', [NotificationController::class, 'read']);

    });
