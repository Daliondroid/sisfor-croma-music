<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\HonorGuru;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $guru = Guru::where('id_user', Auth::id())
            ->with('spesialisasis')
            ->firstOrFail();

        // ── Jadwal hari ini ────────────────────────────────────────
        $jadwalHariIni = Jadwal::with(['spp.murid', 'spp.programKursus'])
            ->where('id_guru', $guru->id_guru)
            ->where('is_active', true)
            ->whereDate('tanggal', today())
            ->whereIn('status_jadwal', ['Sesuai Jadwal', 'Reschedule'])
            ->orderBy('jam_mulai')
            ->get();

        // ── Semua jadwal aktif guru (untuk lihat jadwal murid yang diajar) ──
        $semuaJadwal = Jadwal::with(['spp.murid', 'spp.programKursus'])
            ->where('id_guru', $guru->id_guru)
            ->where('is_active', true)
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_mulai')
            ->take(20)
            ->get();

        // ── Rekap honor / gaji guru ────────────────────────────────
        // Hitung total sesi hadir bulan ini
        $bulanIni = now()->format('Y-m');
        $sesiHadirBulanIni = Jadwal::where('id_guru', $guru->id_guru)
            ->where('status_kehadiran_guru', 'Hadir')
            ->whereYear('tanggal', now()->year)
            ->whereMonth('tanggal', now()->month)
            ->count();

        // Sesi hadir yang sudah di-cover honor (sudah dibayar / siap dibayar)
        $sesiTercoverBulanIni = HonorGuru::where('id_guru', $guru->id_guru)
            ->whereYear('tanggal_pencairan', now()->year)
            ->whereMonth('tanggal_pencairan', now()->month)
            ->sum('jumlah_pertemuan');

        // Blok 4 pertemuan yang sudah selesai & belum di-cover
        $sesiMenunggu = max(0, $sesiHadirBulanIni - $sesiTercoverBulanIni);
        $blokMenunggu = floor($sesiMenunggu / 4);

        // Riwayat honor guru (5 terakhir)
        $riwayatHonor = HonorGuru::where('id_guru', $guru->id_guru)
            ->latest('tanggal_pencairan')
            ->take(5)
            ->get();

        // Total honor yang sudah lunas (lifetime)
        $totalHonorDiterima = HonorGuru::where('id_guru', $guru->id_guru)
            ->where('status_bayar', 'Lunas')
            ->sum('jumlah_honor');

        // ── Ringkasan absensi murid bulan ini (per murid yang diajar) ──
        $muridDiajar = Jadwal::with('spp.murid')
            ->where('id_guru', $guru->id_guru)
            ->where('is_active', true)
            ->whereYear('tanggal', now()->year)
            ->whereMonth('tanggal', now()->month)
            ->get()
            ->groupBy('id_spp')
            ->map(function ($jadwals) {
                $murid = $jadwals->first()->spp?->murid;
                return [
                    'murid'       => $murid,
                    'total_sesi'  => $jadwals->count(),
                    'hadir'       => $jadwals->where('status_kehadiran_murid', 'Hadir')->count(),
                    'tidak_hadir' => $jadwals->where('status_kehadiran_murid', 'Tidak Hadir')->count(),
                    'belum_diisi' => $jadwals->whereNull('status_kehadiran_murid')->count(),
                ];
            });

        return view('guru.dashboard', compact(
            'guru',
            'jadwalHariIni',
            'semuaJadwal',
            'sesiHadirBulanIni',
            'blokMenunggu',
            'riwayatHonor',
            'totalHonorDiterima',
            'muridDiajar',
        ));
    }
}
