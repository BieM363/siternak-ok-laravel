<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CowController;
use App\Http\Controllers\PregnancyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CalfController;
use App\Http\Controllers\BullStrawController;

/*
|--------------------------------------------------------------------------
| Web Routes - SiTernak-OK (Sistem Pemantauan Kehamilan Sapi Dinas Peternakan)
|--------------------------------------------------------------------------
*/

// Public Home Landing Page & Authentication Routes
Route::get('/', [AuthController::class, 'showHome'])->name('home');
Route::get('/guest-access', [AuthController::class, 'guestAccess'])->name('guest.access');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/guest-login', [AuthController::class, 'guestLogin'])->name('guest.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public Read-Only Access Routes (Bisa Diakses oleh Tamu & Pengguna Umum Tanpa Login)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Sapi (Read-Only)
Route::get('/cows', [CowController::class, 'index'])->name('cows.index');
Route::get('/cows/{id}', [CowController::class, 'show'])->name('cows.show');

// Data Anak Sapi (Read-Only)
Route::get('/calves', [CalfController::class, 'index'])->name('calves.index');
Route::get('/calves/{id}', [CalfController::class, 'show'])->name('calves.show');

// Menu 1: Rekapan Kelahiran Pedet (Read-Only)
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
Route::get('/reports/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');

// Menu 2: Dedicated Leaderboard Inseminator (Read-Only)
Route::get('/inseminators', [ReportController::class, 'inseminators'])->name('reports.inseminators');

// Data Peternak (Read-Only)
Route::get('/farmers', [FarmerController::class, 'index'])->name('farmers.index');
Route::get('/farmers/search-json', [FarmerController::class, 'searchJson'])->name('farmers.search-json');

// Operator & Administrasi Interactive Actions (Wajib Auth Login)
Route::middleware(['auth', 'role:admin,operator'])->group(function () {
    // Pendaftaran Sapi Betina Induk Baru & Rekam IB (Dapat Diinput & Dihapus oleh Operator & Admin)
    Route::get('/cows-create', [CowController::class, 'create'])->name('cows.create');
    Route::get('/cows-auto-nik', [CowController::class, 'getAutoNik'])->name('cows.auto-nik');
    Route::post('/cows', [CowController::class, 'store'])->name('cows.store');
    Route::get('/cows/{id}/edit', [CowController::class, 'edit'])->name('cows.edit');
    Route::put('/cows/{id}', [CowController::class, 'update'])->name('cows.update');
    Route::delete('/cows/{id}', [CowController::class, 'destroy'])->name('cows.destroy');

    // Manajemen Database Straw Pejantan IB
    Route::get('/straws', [BullStrawController::class, 'index'])->name('straws.index');
    Route::post('/straws', [BullStrawController::class, 'store'])->name('straws.store');
    Route::delete('/straws/{id}', [BullStrawController::class, 'destroy'])->name('straws.destroy');

    // Update Status Kehamilan, Tandai Gagal & Trigger Cron
    Route::put('/pregnancy/{id}/update-status', [PregnancyController::class, 'updateStatus'])->name('pregnancy.update-status');
    Route::post('/pregnancy/trigger-cron', [PregnancyController::class, 'triggerCron'])->name('pregnancy.trigger-cron');

    // Edit, Update & Promosikan Anak Sapi
    Route::get('/calves/{id}/edit', [CalfController::class, 'edit'])->name('calves.edit');
    Route::put('/calves/{id}', [CalfController::class, 'update'])->name('calves.update');
    Route::post('/calves/{id}/promote', [CalfController::class, 'promoteToCow'])->name('calves.promote');
});

// Administrasi Only Routes (Pengisian Master Data Peternak, User & Wilayah)
Route::middleware(['auth', 'role:admin'])->group(function () {

    // Hapus Data Anak Sapi
    Route::delete('/calves/{id}', [CalfController::class, 'destroy'])->name('calves.destroy');

    // Rekam Kebuntingan (Input IB)
    Route::post('/pregnancy', [PregnancyController::class, 'store'])->name('pregnancy.store');

    // Manajemen Peternak Actions
    Route::post('/farmers', [FarmerController::class, 'store'])->name('farmers.store');
    Route::delete('/farmers/{id}', [FarmerController::class, 'destroy'])->name('farmers.destroy');

    // Manajemen Hirarki Wilayah (Kabupaten, Kecamatan, Desa)
    Route::get('/regions', [RegionController::class, 'index'])->name('regions.index');
    Route::post('/regions/regency', [RegionController::class, 'storeRegency'])->name('regions.store-regency');
    Route::post('/regions/district', [RegionController::class, 'storeDistrict'])->name('regions.store-district');
    Route::post('/regions/village', [RegionController::class, 'storeVillage'])->name('regions.store-village');

    // Manajemen User & Operator
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
});
