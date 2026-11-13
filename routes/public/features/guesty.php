<?php

use App\Http\Controllers\Public\Guesty\GuestyTokenController;
use Illuminate\Support\Facades\Route;

Route::get("guesty/token", [GuestyTokenController::class, 'show'])->middleware('throttle:30,1');
