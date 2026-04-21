<?php

namespace App\Http\Controllers\Murid;

use App\Http\Controllers\Controller;
use App\Models\Murid;
use App\Models\MonthlyReport;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $murid = Murid::where('id_user', Auth::id())
            ->with(['jadwals.guru', 'jadwals.kelas'])
            ->firstOrFail();

        $sppBulanIni    = $murid->sppBulanIni();
        $reportTerakhir = $murid->monthlyReports()->latest()->first();

        // Presensi bulan ini
        $presensiBulanIni = $murid->presensis()
            ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [now()->format('Y-m')])
            ->with(['materiKbm', 'videoProgres'])
            ->latest()
            ->get();

        return view('murid.dashboard', compact('murid', 'sppBulanIni', 'reportTerakhir', 'presensiBulanIni'));
    }
}
