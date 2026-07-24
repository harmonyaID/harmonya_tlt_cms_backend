<?php

use App\Http\Controllers\Web\Admin\Property\PropertyBedTypeController;
use App\Http\Controllers\Web\Admin\Property\PropertyContactFormController;
use App\Http\Controllers\Web\Admin\Property\PropertyController;
use App\Http\Controllers\Web\Admin\Property\PropertyPhotoController;
use App\Http\Controllers\Web\Admin\Property\PropertyRelatedController;
use App\Http\Controllers\Web\Admin\Property\PropertyReviewController;
use App\Http\Controllers\Web\Admin\Property\PropertyRoomTypeController;
use App\Http\Controllers\Web\Admin\Property\PropertyTagController;
use App\Http\Controllers\Web\Admin\Property\PropertyTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix("properties")
    ->middleware('auth.web.admin')
    ->group(function () {

        Route::get('', [PropertyController::class, 'get']);
        Route::post('', [PropertyController::class, 'create']);

        Route::prefix('types')
            ->group(function () {
                Route::get('', [PropertyTypeController::class, 'get']);
                Route::post('', [PropertyTypeController::class, 'create']);
                Route::get('{id}', [PropertyTypeController::class, 'detail']);
                Route::put('{id}', [PropertyTypeController::class, 'update']);
                Route::delete('{id}', [PropertyTypeController::class, 'delete']);
            });

        Route::prefix('room-types')
            ->group(function () {
                Route::get('', [PropertyRoomTypeController::class, 'get']);
                Route::post('', [PropertyRoomTypeController::class, 'create']);
                Route::get('{id}', [PropertyRoomTypeController::class, 'detail']);
                Route::put('{id}', [PropertyRoomTypeController::class, 'update']);
                Route::delete('{id}', [PropertyRoomTypeController::class, 'delete']);
            });

        Route::prefix('bed-types')
            ->group(function () {
                Route::get('', [PropertyBedTypeController::class, 'get']);
                Route::post('', [PropertyBedTypeController::class, 'create']);
                Route::get('{id}', [PropertyBedTypeController::class, 'detail']);
                Route::put('{id}', [PropertyBedTypeController::class, 'update']);
                Route::delete('{id}', [PropertyBedTypeController::class, 'delete']);
            });

        Route::prefix('tags')
            ->group(function () {
                Route::get('', [PropertyTagController::class, 'get']);
                Route::post('', [PropertyTagController::class, 'create']);
                Route::get('{id}', [PropertyTagController::class, 'detail']);
                Route::put('{id}', [PropertyTagController::class, 'update']);
                Route::delete('{id}', [PropertyTagController::class, 'delete']);
            });

        Route::prefix('reviews')
            ->group(function () {
                Route::get('', [PropertyReviewController::class, 'get']);
                Route::post('', [PropertyReviewController::class, 'create']);
                Route::get('{id}', [PropertyReviewController::class, 'detail']);
                Route::post('{id}', [PropertyReviewController::class, 'update']);
                Route::delete('{id}', [PropertyReviewController::class, 'delete']);
            });

        Route::prefix('contact-forms')
            ->group(function () {
                Route::get('', [PropertyContactFormController::class, 'get']);
                Route::post('', [PropertyContactFormController::class, 'create']);
                Route::get('{id}', [PropertyContactFormController::class, 'detail']);
                Route::delete('{id}', [PropertyContactFormController::class, 'delete']);
                Route::patch('{id}/read', [PropertyContactFormController::class, 'markAsRead']);
                Route::patch('{id}/status', [PropertyContactFormController::class, 'changeStatus']);
            });

        Route::post('{propertyId}/photos', [PropertyPhotoController::class, 'upload']);
        Route::delete('{propertyId}/photos/{photoId}', [PropertyPhotoController::class, 'delete']);

        Route::get('{propertyId}/related', [PropertyRelatedController::class, 'get']);
        Route::put('{propertyId}/related', [PropertyRelatedController::class, 'sync']);

        Route::get('{id}', [PropertyController::class, 'detail']);
        Route::post('{id}', [PropertyController::class, 'update']);
        Route::delete('{id}', [PropertyController::class, 'delete']);
    });
