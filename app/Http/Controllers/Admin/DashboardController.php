<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Murid;
use App\Models\Guru;
use App\Models\Spp;
use App\Models\Jadwal;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $hariIni = now()->locale('id')->dayName; // Senin, Selasa, dst

        $totalMurid  = Murid::where('status_aktif', true)->count();
        $totalGuru   = Guru::where('status_aktif', true)->count();
        $belumBayar  = Spp::where('status_bayar', 'belum_bayar')
                          ->where('bulan_tagihan', now()->format('Y-m'))
                          ->count();

        $totalPemasukanBulanIni = Spp::where('status_bayar', 'sudah_bayar')
                          ->where('bulan_tagihan', now()->format('Y-m'))
                          ->sum('nominal_tagihan');

        $sppBelumBayar = Spp::with('murid')
                          ->where('status_bayar', 'belum_bayar')
                          ->where('bulan_tagihan', now()->format('Y-m'))
                          ->orderBy('tanggal_jatuh_tempo')
                          ->take(5)
                          ->get();

        $jadwalHariIni = Jadwal::with(['murid', 'guru', 'kelas'])
                          ->where('hari', $hariIni)
                          ->where('is_active', true)
                          ->get();

        return view('admin.dashboard', compact(
            'totalMurid', 'totalGuru', 'belumBayar',
            'totalPemasukanBulanIni', 'sppBelumBayar', 'jadwalHariIni'
        ));
    }
}