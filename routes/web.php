<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\WhatsappController;

Route::middleware(['auth', 'verified', 'user'])->group(function () {
    Route::get('/user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');

    //Whastapp
    // Route::get('/user/whatsapp', [WhatsappController::class, 'indexWhatsapp'])->name('user.whatsapp');
    Route::get('/user/whatsapp/add', [WhatsappController::class, 'createWhatsapp'])->name('user.whatsapp.create');
    Route::post('/user/whatsapp/add', [WhatsappController::class, 'storeWhatsapp'])->name('user.whatsapp.store');
    Route::get('/user/whatsapp/edit', [WhatsappController::class, 'editWhatsapp'])->name('user.whatsapp.edit');
    Route::put('/user/whatsapp/edit', [WhatsappController::class, 'updateWhatsapp'])->name('user.whatsapp.update');
    Route::delete('/user/whatsapp/delete', [WhatsappController::class, 'deleteWhatsapp'])->name('user.whatsapp.delete');

    //Riwayat
    Route::get('/user/riwayat', [RiwayatController::class, 'indexRiwayat'])->name('user.riwayat');
    Route::get('/user/riwayat/download', [RiwayatController::class, 'downloadRiwayat'])->name('user.riwayat.download');
});

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    //Pengguna
    Route::get('/admin/pengguna', [PenggunaController::class, 'indexPengguna'])->name('admin.pengguna');
    Route::get('/admin/pengguna/add', [PenggunaController::class, 'createPengguna'])->name('admin.pengguna.create');
    Route::post('/admin/pengguna/add', [PenggunaController::class, 'storePengguna'])->name('admin.pengguna.store');
    Route::get('/admin/pengguna/{id}/edit', [PenggunaController::class, 'editPengguna'])->name('admin.pengguna.edit');
    Route::put('/admin/pengguna/{id}', [PenggunaController::class, 'updatePengguna'])->name('admin.pengguna.update');
    Route::delete('/admin/pengguna/{id}', [PenggunaController::class, 'deletePengguna'])->name('admin.pengguna.delete');
    Route::get('/admin/pengguna/download', [PenggunaController::class, 'downloadPengguna'])->name('admin.pengguna.download');

    //Alat
    Route::get('/admin/devices', [DeviceController::class, 'indexDevice'])->name('admin.devices');
    Route::get('/admin/devices/show/{id}', [DeviceController::class, 'showDevice'])->name('admin.devices.show');
    Route::get('/admin/devices/create', [DeviceController::class, 'createDevice'])->name('admin.devices.create');
    Route::post('/admin/devices/store', [DeviceController::class, 'storeDevice'])->name('admin.devices.store');
    Route::get('/admin/devices/{id}/edit', [DeviceController::class, 'editDevice'])->name('admin.devices.edit');
    Route::put('/admin/devices/{id}', [DeviceController::class, 'updateDevice'])->name('admin.devices.update');
    Route::delete('/admin/devices/{id}', [DeviceController::class, 'destroyDevice'])->name('admin.devices.destroy');

    //Riwayat
    Route::get('/admin/riwayat', [RiwayatController::class, 'indexRiwayat'])->name('admin.riwayat');
    Route::get('/admin/riwayat/download', [RiwayatController::class, 'downloadRiwayat'])->name('admin.riwayat.download');
    Route::delete('/admin/riwayat/truncate', [RiwayatController::class, 'truncateRiwayat'])->name('admin.riwayat.truncate');
});


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'indexProfile'])->name('profile.index');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [ProfileController::class, 'destroyProfile'])->name('profile.destroy');
});

//GUEST
Route::get('/', [GuestController::class, 'home'])->name('home');
Route::get('/panduan', [GuestController::class, 'panduan'])->name('panduan');
Route::get('/monitoring', [GuestController::class, 'monitoring'])->name('monitoring');
Route::get('/peta', [GuestController::class, 'map'])->name('guest.peta');

require __DIR__ . '/auth.php';
