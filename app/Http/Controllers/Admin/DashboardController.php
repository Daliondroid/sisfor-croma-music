<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Murid;
use App\Models\Guru;
use App\Models\Spp;
use App\Models\Jadwal;
use App\Models\HonorGuru;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $bulanIni = now()->format('Y-m');

        // ── Statistik utama ────────────────────────────────────────
        $totalMurid = Murid::where('status_aktif', true)->count();
        $totalGuru  = Guru::where('status_aktif', true)->count();

        // SPP belum lunas bulan ini
        $belumBayar = Spp::where('status_bayar', 'Belum Lunas')
            ->whereYear('periode_tagihan', now()->year)
            ->whereMonth('periode_tagihan', now()->month)
            ->count();

        // Total pemasukan bulan ini (SPP yang sudah lunas)
        $totalPemasukanBulanIni = Spp::where('status_bayar', 'Lunas')
            ->whereYear('periode_tagihan', now()->year)
            ->whereMonth('periode_tagihan', now()->month)
            ->sum('nominal_tagihan');

        // ── 5 tagihan SPP belum bayar (terlama) ───────────────────
        $sppBelumBayar = Spp::with('murid')
            ->where('status_bayar', 'Belum Lunas')
            ->whereYear('periode_tagihan', now()->year)
            ->whereMonth('periode_tagihan', now()->month)
            ->orderBy('tanggal_jatuh_tempo')
            ->take(5)
            ->get();

        // ── Jadwal hari ini ────────────────────────────────────────
        $jadwalHariIni = Jadwal::with(['guru', 'spp.murid'])
            ->whereDate('tanggal', today())
            ->where('is_active', true)
            ->whereIn('status_jadwal', ['Sesuai Jadwal', 'Reschedule'])
            ->orderBy('jam_mulai')
            ->get();

        // ── Bukti transfer menunggu validasi ──────────────────────
        $menantiValidasi = Transaksi::with(['spp.murid'])
            ->whereNull('tanggal_konfirmasi')
            ->whereHas('spp', fn($q) => $q->where('status_bayar', 'Belum Lunas'))
            ->latest()
            ->take(5)
            ->get();

        // ── Honor guru siap dibayar ────────────────────────────────
        $honorSiapDibayar = HonorGuru::with('guru')
            ->where('status_bayar', 'Siap Dibayar')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalMurid',
            'totalGuru',
            'belumBayar',
            'totalPemasukanBulanIni',
            'sppBelumBayar',
            'jadwalHariIni',
            'menantiValidasi',
            'honorSiapDibayar',
        ));
    }
}