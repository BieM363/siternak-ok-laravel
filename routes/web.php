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
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes - SiTernak-OK (Sistem Pemantauan Kebuntingan Sapi Disnakbun Gorontalo)
|--------------------------------------------------------------------------
*/

// Public Home Landing Page & Authentication Routes
Route::get('/', [AuthController::class, 'showHome'])->name('home');
Route::get('/guest-access', [AuthController::class, 'guestAccess'])->name('guest.access');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/guest-login', [AuthController::class, 'guestLogin'])->name('guest.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public Read-Only Access Routes
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Data Sapi Betina (Akseptor IB)
Route::get('/cows', [CowController::class, 'index'])->name('cows.index');
Route::get('/cows/{id}', [CowController::class, 'show'])->name('cows.show');

// Menu Khusus: Data Kebuntingan Aktif & Ekspor Excel
Route::get('/pregnancies', [PregnancyController::class, 'index'])->name('pregnancies.index');
Route::get('/pregnancies/excel', [PregnancyController::class, 'exportExcel'])->name('pregnancies.excel');

// Menu Khusus: Notifikasi & Konfirmasi 3 Tahap Kebuntingan (Dipisah dari Dashboard)
Route::get('/confirmations', [NotificationController::class, 'index'])->name('confirmations.index');

// Menu Terpadu: Data Pedet & Kelahiran
Route::get('/calves', [CalfController::class, 'index'])->name('calves.index');
Route::get('/calves/excel', [ReportController::class, 'exportExcel'])->name('calves.excel');
Route::get('/calves/pdf', [ReportController::class, 'exportPdf'])->name('calves.pdf');
Route::get('/calves/{id}', [CalfController::class, 'show'])->name('calves.show');

// Rekapitulasi Laporan & Ekspor Excel (.xlsx) / PDF
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
Route::get('/reports/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');

// Leaderboard & Evaluasi Kinerja Inseminator
Route::get('/inseminators', [ReportController::class, 'inseminators'])->name('reports.inseminators');

// Data Peternak (Pemilik Sapi)
Route::get('/farmers', [FarmerController::class, 'index'])->name('farmers.index');
Route::get('/farmers/search-json', [FarmerController::class, 'searchJson'])->name('farmers.search-json');

// Inseminator & Admin Actions (Admin Kab/Kota dapat input susulan/backdate, Inseminator input bebas wilayah)
Route::middleware(['auth', 'role:admin,operator'])->group(function () {
    Route::get('/cows-create', [CowController::class, 'create'])->name('cows.create');
    Route::post('/cows', [CowController::class, 'store'])->name('cows.store');
    Route::post('/pregnancy', [PregnancyController::class, 'store'])->name('pregnancy.store');

    Route::get('/cows-auto-nik', [CowController::class, 'getAutoNik'])->name('cows.auto-nik');
    Route::get('/cows/{id}/edit', [CowController::class, 'edit'])->name('cows.edit');
    Route::put('/cows/{id}', [CowController::class, 'update'])->name('cows.update');
    Route::delete('/cows/{id}', [CowController::class, 'destroy'])->name('cows.destroy');

    // Database Master Identitas Pejantan (Straw) — Lihat untuk semua petugas, kelola untuk Admin Provinsi
    Route::get('/straws', [BullStrawController::class, 'index'])->name('straws.index');
    Route::post('/straws', [BullStrawController::class, 'store'])->name('straws.store');
    Route::put('/straws/{id}', [BullStrawController::class, 'update'])->name('straws.update');
    Route::delete('/straws/{id}', [BullStrawController::class, 'destroy'])->name('straws.destroy');

    // Konfirmasi Kebuntingan 3 Tahap & Trigger Cron
    Route::put('/pregnancy/{id}/update-status', [PregnancyController::class, 'updateStatus'])->name('pregnancy.update-status');
    Route::post('/pregnancy/trigger-cron', [PregnancyController::class, 'triggerCron'])->name('pregnancy.trigger-cron');

    // Endpoint Notifikasi Peramban Ponsel & Pemantauan Sesi 8 Jam Inseminator
    Route::get('/notifications/poll', [NotificationController::class, 'poll'])->name('notifications.poll');
    Route::post('/notifications/sync-cron', [NotificationController::class, 'syncCron'])->name('notifications.sync-cron');

    // Edit, Update & Promosikan Pedet Betina
    Route::get('/calves/{id}/edit', [CalfController::class, 'edit'])->name('calves.edit');
    Route::put('/calves/{id}', [CalfController::class, 'update'])->name('calves.update');
    Route::post('/calves/{id}/promote', [CalfController::class, 'promoteToCow'])->name('calves.promote');
});

// Administrasi Only Routes (Admin Provinsi & Admin Kabupaten/Kota)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::delete('/calves/{id}', [CalfController::class, 'destroy'])->name('calves.destroy');

    // Manajemen Peternak (NIK opsional)
    Route::post('/farmers', [FarmerController::class, 'store'])->name('farmers.store');
    Route::put('/farmers/{id}', [FarmerController::class, 'update'])->name('farmers.update');
    Route::delete('/farmers/{id}', [FarmerController::class, 'destroy'])->name('farmers.destroy');

    // Manajemen Wilayah Administratif (Provinsi)
    Route::get('/regions', [RegionController::class, 'index'])->name('regions.index');
    Route::post('/regions/regency', [RegionController::class, 'storeRegency'])->name('regions.store-regency');
    Route::put('/regions/regency/{id}', [RegionController::class, 'updateRegency'])->name('regions.update-regency');
    Route::post('/regions/district', [RegionController::class, 'storeDistrict'])->name('regions.store-district');
    Route::put('/regions/district/{id}', [RegionController::class, 'updateDistrict'])->name('regions.update-district');
    Route::post('/regions/village', [RegionController::class, 'storeVillage'])->name('regions.store-village');
    Route::put('/regions/village/{id}', [RegionController::class, 'updateVillage'])->name('regions.update-village');

    // Manajemen Pengguna (Admin Provinsi, Admin Kab/Kota, Inseminator)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
});
