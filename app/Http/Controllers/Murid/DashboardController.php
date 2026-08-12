<?php

namespace App\Http\Controllers\Murid;

use App\Http\Controllers\Controller;
use App\Models\Murid;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $murid = Murid::where('id_user', Auth::id())
            ->with(['jadwals.guru', 'jadwals.spp'])
            ->firstOrFail();

        $sppBulanIni = $murid->sppBulanIni();
        $reportTerakhir = $murid->monthlyReports()->latest()->first();

        // Mengambil riwayat jadwal pelajaran yang sudah diisi presensinya pada bulan ini
        $presensiBulanIni = $murid->jadwals()
            ->whereYear('tanggal', now()->year)
            ->whereMonth('tanggal', now()->month)
            ->whereNotNull('status_kehadiran_murid')
            ->with('progresMurid')
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('murid.dashboard', compact('murid', 'sppBulanIni', 'reportTerakhir', 'presensiBulanIni'));
    }
}
