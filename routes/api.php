<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('is.sensor')->group(function () {
    Route::post('/sensor/data', [DeviceController::class, 'storeData']);
});

Route::post('/device/register', [DeviceController::class, 'register']);

Route::post('/device/{deviceId}/sensor', [DeviceController::class, 'addSensor']);





