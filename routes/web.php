<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Guru;
use App\Http\Controllers\Murid;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// ── Landing Page (Public / Tanpa Login) ────────
Route::get('/', function () {
    // Karena file berada di resources/views/index.blade.php
    return view('index'); 
})->name('home');


// Rute Login Manual
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

// ── Auth (disediakan Breeze, tambahkan redirect role) ────────
Route::get('/dashboard', [AuthController::class, 'redirectAfterLogin'])
    ->middleware('auth')->name('dashboard');

// ── Admin ────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // User management
    Route::get('/murids',          [Admin\UserController::class, 'indexMurid'])->name('murids.index');
    Route::get('/murids/create',   [Admin\UserController::class, 'createMurid'])->name('murids.create');
    Route::post('/murids',         [Admin\UserController::class, 'storeMurid'])->name('murids.store');
    Route::patch('/users/{user}/toggle', [Admin\UserController::class, 'toggleAktif'])->name('users.toggle');
    Route::get('/gurus',           [Admin\UserController::class, 'indexGuru'])->name('gurus.index');
    Route::post('/gurus',          [Admin\UserController::class, 'storeGuru'])->name('gurus.store');

    // Jadwal
    Route::resource('jadwals', Admin\JadwalController::class)->only(['index','create','store','destroy']);

    // SPP
    Route::get('/spp',                    [Admin\SppController::class, 'index'])->name('spp.index');
    Route::post('/spp/generate',          [Admin\SppController::class, 'generateBulanan'])->name('spp.generate');
    Route::patch('/spp/{spp}/validasi',   [Admin\SppController::class, 'validasi'])->name('spp.validasi');

    // Laporan
    Route::get('/laporan/keuangan',       [Admin\LaporanController::class, 'keuangan'])->name('laporan.keuangan');
    Route::get('/laporan/gaji',           [Admin\LaporanController::class, 'gajiGuru'])->name('laporan.gaji');
    Route::get('/laporan/absensi',        [Admin\LaporanController::class, 'absensi'])->name('laporan.absensi');
    Route::get('/laporan/export/{jenis}', [Admin\LaporanController::class, 'exportPdf'])->name('laporan.export');

    // Monthly report
    Route::post('/monthly-report/generate',          [Admin\MonthlyReportController::class, 'generate'])->name('report.generate');
    Route::get('/monthly-report/{murid}/{bulan}',    [Admin\MonthlyReportController::class, 'show'])->name('report.show');

    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
});

// ── Guru ─────────────────────────────────────────────────────
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard',        [Guru\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/presensi',         [Guru\PresensiController::class, 'index'])->name('presensi.index');
    Route::post('/presensi',        [Guru\PresensiController::class, 'store'])->name('presensi.store');
});

// ── Murid ────────────────────────────────────────────────────
Route::middleware(['auth', 'role:murid'])->prefix('murid')->name('murid.')->group(function () {
    Route::get('/dashboard',        [Murid\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/presensi',        [Murid\PresensiController::class, 'store'])->name('presensi.store');
    Route::get('/spp',              [Murid\SppController::class, 'index'])->name('spp.index');
    Route::post('/spp/{spp}/bukti', [Murid\SppController::class, 'uploadBukti'])->name('spp.bukti');
});

require __DIR__.'/auth.php';