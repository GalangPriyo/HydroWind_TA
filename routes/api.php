<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PushNotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rute untuk polling data oleh admin
Route::get('/admin/battery-data', [AdminController::class, 'battryUpdate']);

// Grup Rute untuk Push Notification
Route::prefix('push')->group(function () {
    // Menghapus middleware 'auth:sanctum' agar endpoint ini bisa diakses publik
    Route::post('/subscribe', [PushNotificationController::class, 'subscribe'])
        ->name('push.subscribe');
});
