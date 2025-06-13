<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\SensorController;
use App\Http\Controllers\Api\VisitorController;
use App\Http\Controllers\Api\SummaryController;

Route::prefix('v1')->group(function () {
    Route::apiResource('locations', LocationController::class);
    Route::apiResource('sensors', SensorController::class);
    Route::apiResource('visitors', VisitorController::class)->only([
        'index',
        'store',
        'show'
    ]);
    Route::get('/summary', [SummaryController::class, 'index']);
});
