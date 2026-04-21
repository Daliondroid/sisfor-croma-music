<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Guru;
use App\Http\Controllers\Murid;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// ── Landing Page ─────────────────────────────────────────────
Route::get('/', fn() => view('index'))->name('home');

// ── Auth Breeze ──────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('login',  [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// ── Redirect dashboard berdasarkan role ──────────────────────
Route::get('/dashboard', [AuthController::class, 'redirectAfterLogin'])
    ->middleware('auth')->name('dashboard');

// ── ADMIN ────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Murid
    Route::get('/murids',               [Admin\UserController::class, 'indexMurid'])->name('murids.index');
    Route::get('/murids/create',        [Admin\UserController::class, 'createMurid'])->name('murids.create');
    Route::post('/murids',              [Admin\UserController::class, 'storeMurid'])->name('murids.store');
    Route::get('/murids/{murid}/edit',  [Admin\UserController::class, 'editMurid'])->name('murids.edit');
    Route::put('/murids/{murid}',       [Admin\UserController::class, 'updateMurid'])->name('murids.update');

    // Guru
    Route::get('/gurus',                [Admin\UserController::class, 'indexGuru'])->name('gurus.index');
    Route::get('/gurus/create',         [Admin\UserController::class, 'createGuru'])->name('gurus.create');
    Route::post('/gurus',               [Admin\UserController::class, 'storeGuru'])->name('gurus.store');
    Route::get('/gurus/{guru}/edit',    [Admin\UserController::class, 'editGuru'])->name('gurus.edit');
    Route::put('/gurus/{guru}',         [Admin\UserController::class, 'updateGuru'])->name('gurus.update');

    // Toggle aktif/nonaktif user
    Route::patch('/users/{user}/toggle', [Admin\UserController::class, 'toggleAktif'])->name('users.toggle');

    // Jadwal
    Route::resource('jadwals', Admin\JadwalController::class)->only(['index','create','store','destroy']);

    // SPP
    Route::get('/spp',                     [Admin\SppController::class, 'index'])->name('spp.index');
    Route::post('/spp/generate',           [Admin\SppController::class, 'generateBulanan'])->name('spp.generate');
    Route::patch('/spp/{spp}/validasi',    [Admin\SppController::class, 'validasi'])->name('spp.validasi');

    // Laporan
    Route::get('/laporan/keuangan',        [Admin\LaporanController::class, 'keuangan'])->name('laporan.keuangan');
    Route::get('/laporan/gaji',            [Admin\LaporanController::class, 'gajiGuru'])->name('laporan.gaji');
    Route::get('/laporan/absensi',         [Admin\LaporanController::class, 'absensi'])->name('laporan.absensi');
    Route::get('/laporan/export/{jenis}',  [Admin\LaporanController::class, 'exportPdf'])->name('laporan.export');

    // Monthly Report
    Route::post('/monthly-report/generate',       [Admin\MonthlyReportController::class, 'generate'])->name('report.generate');
    Route::get('/monthly-report/{murid}/{bulan}', [Admin\MonthlyReportController::class, 'show'])->name('report.show');
});

// ── GURU ─────────────────────────────────────────────────────
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard',  [Guru\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/presensi',   [Guru\PresensiController::class, 'index'])->name('presensi.index');
    Route::post('/presensi',  [Guru\PresensiController::class, 'store'])->name('presensi.store');
    Route::get('/profil',     [Guru\ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil',     [Guru\ProfilController::class, 'update'])->name('profil.update');
});

// ── MURID ────────────────────────────────────────────────────
Route::middleware(['auth', 'role:murid'])->prefix('murid')->name('murid.')->group(function () {
    Route::get('/dashboard',         [Murid\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/presensi',         [Murid\PresensiController::class, 'store'])->name('presensi.store');
    Route::get('/spp',               [Murid\SppController::class, 'index'])->name('spp.index');
    Route::post('/spp/{spp}/bukti',  [Murid\SppController::class, 'uploadBukti'])->name('spp.bukti');
    Route::get('/profil',            [Murid\ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil',            [Murid\ProfilController::class, 'update'])->name('profil.update');
});

require __DIR__.'/auth.php';
