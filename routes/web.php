<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Guru;
use App\Http\Controllers\Murid;
use App\Http\Controllers\Jadwal;
use App\Http\Controllers\NotifikasiController;

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
    Route::delete('/murids/{murid}',    [Admin\UserController::class, 'destroyMurid'])->name('murids.destroy');

    // Guru
    Route::get('/gurus',                [Admin\UserController::class, 'indexGuru'])->name('gurus.index');
    Route::get('/gurus/create',         [Admin\UserController::class, 'createGuru'])->name('gurus.create');
    Route::post('/gurus',               [Admin\UserController::class, 'storeGuru'])->name('gurus.store');
    Route::get('/gurus/{guru}/edit',    [Admin\UserController::class, 'editGuru'])->name('gurus.edit');
    Route::put('/gurus/{guru}',         [Admin\UserController::class, 'updateGuru'])->name('gurus.update');
    Route::delete('/gurus/{guru}',      [Admin\UserController::class, 'destroyGuru'])->name('gurus.destroy');

    // Toggle aktif/nonaktif user
    Route::patch('/users/{user}/toggle', [Admin\UserController::class, 'toggleAktif'])->name('users.toggle');

    // Jadwal
    Route::get('/jadwals/cek-sesi', [Admin\JadwalController::class, 'cekSesi'])->name('jadwals.cekSesi');
    Route::resource('jadwals', Admin\JadwalController::class);

    // SPP
    Route::get('/spp',                     [Admin\SppController::class, 'index'])->name('spp.index');
    Route::post('/spp/generate',           [Admin\SppController::class, 'generateBulanan'])->name('spp.generate');
    Route::patch('/spp/{spp}/validasi',    [Admin\SppController::class, 'validasi'])->name('spp.validasi');
    Route::post('/spp/{spp}/notifikasi',   [Admin\SppController::class, 'kirimNotifikasi'])->name('spp.notifikasi');

    // Laporan Gaji
    Route::get('/honor-guru',                [Admin\HonorGuruController::class, 'index'])->name('honor-guru.index');
    Route::get('/honor-guru/{honorGuru}/edit',[Admin\HonorGuruController::class, 'edit'])->name('honor-guru.edit');
    Route::put('/honor-guru/{honorGuru}',    [Admin\HonorGuruController::class, 'update'])->name('honor-guru.update');
    Route::delete('/honor-guru/{honorGuru}', [Admin\HonorGuruController::class, 'destroy'])->name('honor-guru.destroy');

    // Laporan
    Route::get('/laporan/keuangan',          [Admin\LaporanController::class, 'keuangan'])->name('laporan.keuangan');
    Route::get('/laporan/absensi',           [Admin\LaporanController::class, 'absensi'])->name('laporan.absensi');
    Route::get('/laporan/gaji',              [Admin\LaporanController::class, 'gajiGuru'])->name('laporan.gaji');

    // FR-26: Ekspor PDF & XLSX
    Route::get('/laporan/export/pdf/{jenis}',  [Admin\LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');
    Route::get('/laporan/export/xlsx/{jenis}', [Admin\LaporanController::class, 'exportXlsx'])->name('laporan.export.xlsx');

    // Monthly Report
    Route::post('/monthly-report/generate',       [Admin\MonthlyReportController::class, 'generate'])->name('report.generate');
    Route::get('/monthly-report/{murid}/{bulan}', [Admin\MonthlyReportController::class, 'show'])->name('report.show');

    // Program Kursus
    Route::resource('program-kursus', Admin\ProgramKursusController::class)
        ->names('program-kursus');

    // Profil Admin
    Route::get('/profil',  [Admin\ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil',  [Admin\ProfilController::class, 'update'])->name('profil.update');
});

// ── GURU ─────────────────────────────────────────────────────
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard',      [Guru\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/presensi',       [Guru\PresensiController::class, 'index'])->name('presensi.index');
    Route::get('/presensi/rekap', [Guru\PresensiController::class, 'rekap'])->name('presensi.rekap');
    Route::post('/presensi',      [Guru\PresensiController::class, 'store'])->name('presensi.store');

    // Jadwal Kelas
    Route::get('/jadwal', [Guru\JadwalController::class, 'index'])->name('jadwal.index');

    // Absensi (ganti nama dari yang sudah ada, tambah verifikasi)
    Route::get('/absensi',              [Guru\AbsensiController::class, 'index'])->name('absensi.index');
    Route::patch('/absensi/{id}/verifikasi', [Guru\AbsensiController::class, 'verifikasi'])->name('absensi.verifikasi');

    // Laporan KBM Harian
    Route::get('/progres',                [Guru\ProgresMuridController::class, 'index'])->name('progres.index');
    Route::get('/progres/create',         [Guru\ProgresMuridController::class, 'create'])->name('progres.create');
    Route::post('/progres',               [Guru\ProgresMuridController::class, 'store'])->name('progres.store');
    Route::get('/progres/{idSpp}',        [Guru\ProgresMuridController::class, 'show'])->name('progres.show');
    Route::get('/progres/{progresMurid}/edit', [Guru\ProgresMuridController::class, 'edit'])->name('progres.edit');
    Route::put('/progres/{progresMurid}',      [Guru\ProgresMuridController::class, 'update'])->name('progres.update');

    // Laporan Bulanan
    Route::get('/monthly-report',               [Guru\MonthlyReportController::class, 'index'])->name('monthly-report.index');
    Route::get('/monthly-report/create',        [Guru\MonthlyReportController::class, 'create'])->name('monthly-report.create');
    Route::post('/monthly-report',              [Guru\MonthlyReportController::class, 'store'])->name('monthly-report.store');
    Route::get('/monthly-report/{monthlyReport}',[Guru\MonthlyReportController::class, 'show'])->name('monthly-report.show');
    Route::get('/monthly-report/{monthlyReport}/pdf', [Guru\MonthlyReportController::class, 'exportPdf'])->name('monthly-report.pdf');

    // Profil Guru
    Route::get('/profil',         [Guru\ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil',         [Guru\ProfilController::class, 'update'])->name('profil.update');
});

// ── MURID ────────────────────────────────────────────────────
Route::middleware(['auth', 'role:murid'])->prefix('murid')->name('murid.')->group(function () {
    Route::get('/dashboard',         [Murid\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/presensi',         [Murid\PresensiController::class, 'store'])->name('presensi.store');
    Route::get('/spp',               [Murid\SppController::class, 'index'])->name('spp.index');
    Route::post('/spp/{spp}/bukti',  [Murid\SppController::class, 'uploadBukti'])->name('spp.bukti');
    Route::get('/profil',            [Murid\ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil',            [Murid\ProfilController::class, 'update'])->name('profil.update');

    // JALUR BARU: Halaman indeks jadwal untuk murid
    Route::get('/jadwal', [App\Http\Controllers\Murid\JadwalController::class, 'index'])->name('jadwal.index');});

Route::middleware('auth')->group(function () {
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
});

require __DIR__.'/auth.php';
