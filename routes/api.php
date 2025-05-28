<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\NotificationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/kirim-darurat', [NotificationController::class, 'sendEmergencyNotification']);
Route::post('/trigger-alert', [NotificationController::class, 'sendEmergencyNotification']);

Route::get('/admin/battery-data', [AdminController::class, 'battryUpdate']);
