<?php

use App\Http\Controllers\Public\Visitor\VisitorController;
use Illuminate\Support\Facades\Route;

Route::post("track-visitor", [VisitorController::class, 'track']);
