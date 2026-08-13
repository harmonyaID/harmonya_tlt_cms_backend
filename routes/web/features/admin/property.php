<?php

use App\Http\Controllers\Web\Admin\Property\PropertyBedTypeController;
use App\Http\Controllers\Web\Admin\Property\PropertyContactFormController;
use App\Http\Controllers\Web\Admin\Property\PropertyController;
use App\Http\Controllers\Web\Admin\Property\PropertyGuestyConfigurationController;
use App\Http\Controllers\Web\Admin\Property\PropertyPhotoController;
use App\Http\Controllers\Web\Admin\Property\PropertyRelatedController;
use App\Http\Controllers\Web\Admin\Property\PropertyReviewController;
use App\Http\Controllers\Web\Admin\Property\PropertyRoomTypeController;
use App\Http\Controllers\Web\Admin\Property\PropertySourceTypeController;
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

                Route::prefix('trash')
                    ->group(function () {
                        Route::get('', [PropertyTypeController::class, 'trash']);
                        Route::post('{id}/restore', [PropertyTypeController::class, 'restore']);
                        Route::delete('{id}', [PropertyTypeController::class, 'forceDelete']);
                    });

                Route::get('{id}', [PropertyTypeController::class, 'detail']);
                Route::put('{id}', [PropertyTypeController::class, 'update']);
                Route::delete('{id}', [PropertyTypeController::class, 'delete']);
            });

        Route::prefix('room-types')
            ->group(function () {
                Route::get('', [PropertyRoomTypeController::class, 'get']);
                Route::post('', [PropertyRoomTypeController::class, 'create']);

                Route::prefix('trash')
                    ->group(function () {
                        Route::get('', [PropertyRoomTypeController::class, 'trash']);
                        Route::post('{id}/restore', [PropertyRoomTypeController::class, 'restore']);
                        Route::delete('{id}', [PropertyRoomTypeController::class, 'forceDelete']);
                    });

                Route::get('{id}', [PropertyRoomTypeController::class, 'detail']);
                Route::put('{id}', [PropertyRoomTypeController::class, 'update']);
                Route::delete('{id}', [PropertyRoomTypeController::class, 'delete']);
            });

        Route::prefix('bed-types')
            ->group(function () {
                Route::get('', [PropertyBedTypeController::class, 'get']);
                Route::post('', [PropertyBedTypeController::class, 'create']);

                Route::prefix('trash')
                    ->group(function () {
                        Route::get('', [PropertyBedTypeController::class, 'trash']);
                        Route::post('{id}/restore', [PropertyBedTypeController::class, 'restore']);
                        Route::delete('{id}', [PropertyBedTypeController::class, 'forceDelete']);
                    });

                Route::get('{id}', [PropertyBedTypeController::class, 'detail']);
                Route::put('{id}', [PropertyBedTypeController::class, 'update']);
                Route::delete('{id}', [PropertyBedTypeController::class, 'delete']);
            });

        Route::prefix('tags')
            ->group(function () {
                Route::get('', [PropertyTagController::class, 'get']);
                Route::post('', [PropertyTagController::class, 'create']);

                Route::prefix('trash')
                    ->group(function () {
                        Route::get('', [PropertyTagController::class, 'trash']);
                        Route::post('{id}/restore', [PropertyTagController::class, 'restore']);
                        Route::delete('{id}', [PropertyTagController::class, 'forceDelete']);
                    });

                Route::get('{id}', [PropertyTagController::class, 'detail']);
                Route::put('{id}', [PropertyTagController::class, 'update']);
                Route::delete('{id}', [PropertyTagController::class, 'delete']);
            });

        Route::prefix('source-types')
            ->group(function () {
                Route::get('', [PropertySourceTypeController::class, 'get']);
                Route::post('', [PropertySourceTypeController::class, 'create']);

                Route::prefix('trash')
                    ->group(function () {
                        Route::get('', [PropertySourceTypeController::class, 'trash']);
                        Route::post('{id}/restore', [PropertySourceTypeController::class, 'restore']);
                        Route::delete('{id}', [PropertySourceTypeController::class, 'forceDelete']);
                    });

                Route::get('{id}', [PropertySourceTypeController::class, 'detail']);
                Route::put('{id}', [PropertySourceTypeController::class, 'update']);
                Route::delete('{id}', [PropertySourceTypeController::class, 'delete']);
            });

        Route::post('{propertyId}/photos', [PropertyPhotoController::class, 'upload']);
        Route::delete('{propertyId}/photos/{photoId}', [PropertyPhotoController::class, 'delete']);

        Route::prefix('reviews')
            ->group(function () {
                Route::get('', [PropertyReviewController::class, 'get']);
                Route::post('', [PropertyReviewController::class, 'create']);

                Route::prefix('trash')
                    ->group(function () {
                        Route::get('', [PropertyReviewController::class, 'trash']);
                        Route::post('{id}/restore', [PropertyReviewController::class, 'restore']);
                        Route::delete('{id}', [PropertyReviewController::class, 'forceDelete']);
                    });

                Route::get('{id}', [PropertyReviewController::class, 'detail']);
                Route::post('{id}', [PropertyReviewController::class, 'update']);
                Route::delete('{id}', [PropertyReviewController::class, 'delete']);
            });

        // Property Contact Form - NOT included in Trash (excluded per request; inquiries are simply hard-deleted)
        Route::prefix('contact-forms')
            ->group(function () {
                Route::get('', [PropertyContactFormController::class, 'get']);
                Route::post('', [PropertyContactFormController::class, 'create']);
                Route::get('{id}', [PropertyContactFormController::class, 'detail']);
                Route::delete('{id}', [PropertyContactFormController::class, 'delete']);
                Route::patch('{id}/read', [PropertyContactFormController::class, 'markAsRead']);
                Route::patch('{id}/status', [PropertyContactFormController::class, 'changeStatus']);
            });

        Route::prefix('source-types')
            ->group(function () {
                Route::get('', [PropertySourceTypeController::class, 'get']);
                Route::post('', [PropertySourceTypeController::class, 'create']);
                Route::get('{id}', [PropertySourceTypeController::class, 'detail']);
                Route::put('{id}', [PropertySourceTypeController::class, 'update']);
                Route::delete('{id}', [PropertySourceTypeController::class, 'delete']);
        });
        
        // Guesty API Configuration (Property Management Module)
        Route::prefix('guesty-configuration')
            ->group(function () {
                Route::get('', [PropertyGuestyConfigurationController::class, 'get']);
                Route::put('', [PropertyGuestyConfigurationController::class, 'update']);
                Route::post('test', [PropertyGuestyConfigurationController::class, 'test']);
            });

        Route::prefix('trash')
            ->group(function () {
                Route::get('', [PropertyController::class, 'trash']);
                Route::post('{id}/restore', [PropertyController::class, 'restore']);
                Route::delete('{id}', [PropertyController::class, 'forceDelete']);
            });

        Route::get('{propertyId}/related', [PropertyRelatedController::class, 'get']);
        Route::put('{propertyId}/related', [PropertyRelatedController::class, 'sync']);

        Route::get('{id}', [PropertyController::class, 'detail']);
        Route::post('{id}', [PropertyController::class, 'update']);
        Route::delete('{id}', [PropertyController::class, 'delete']);
    });
