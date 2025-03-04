<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PenggunaController;

// Route::get('/', function () {
//     return Inertia::render('Guest/Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::middleware(['auth', 'verified', 'user'])->group(function () {
    // Dashboard User
    Route::get('/user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');

    // // Halaman Riwayat
    // Route::get('/user/history', [UserController::class, 'history'])->name('user.history');

    //Whastapp
    Route::get('/user/whatsapp', [UserController::class, 'indexWhatsapp'])->name('user.whatsapp');
    Route::get('/user/whatsapp/add', [UserController::class, 'createWhatsapp'])->name('user.whatsapp.create');
    Route::post('/user/whatsapp/add', [UserController::class, 'storeWhatsapp'])->name('user.whatsapp.store');
    Route::get('/user/whatsapp/edit', [UserController::class, 'editWhatsapp'])->name('user.whatsapp.edit');
    Route::put('/user/whatsapp/edit', [UserController::class, 'updateWhatsapp'])->name('user.whatsapp.update');
    Route::delete('/user/whatsapp/delete', [UserController::class, 'deleteWhatsapp'])->name('user.whatsapp.delete');
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

    //Alat
    Route::get('/admin/devices', [DeviceController::class, 'index'])->name('admin.devices');
    Route::get('/admin/devices/show/{id}', [DeviceController::class, 'show'])->name('admin.devices.show');
    Route::get('/admin/devices/create', [DeviceController::class, 'create'])->name('admin.devices.create');
    Route::post('/admin/devices/store', [DeviceController::class, 'store'])->name('admin.devices.store');
    Route::get('/admin/devices/{id}/edit', [DeviceController::class, 'edit'])->name('admin.devices.edit');
    Route::put('/admin/devices/{id}', [DeviceController::class, 'update'])->name('admin.devices.update');
    Route::delete('/admin/devices/{id}', [DeviceController::class, 'destroy'])->name('admin.devices.delete');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//GUEST
Route::get('/', function () {
    return Inertia::render('Guest/Welcome');
})->name('home');

Route::get('/peta', function () {
    return Inertia::render('Guest/Peta', [
        'googleMapsApiKey' => config('services.google_maps.key')
    ]);
})->name('peta');

Route::get('/panduan', function () {
    return Inertia::render('Guest/Panduan');
})->name('panduan');

Route::get('/prakiraan-cuaca', function () {
    return Inertia::render('Guest/PraCuaca');
})->name('prakiraan-cuaca');

require __DIR__ . '/auth.php';
