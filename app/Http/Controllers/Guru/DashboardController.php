<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Jadwal;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $guru = Guru::where('id_user', Auth::id())
            ->with('spesialisasis')
            ->firstOrFail();

        // Jadwal mengajar hari ini
        $jadwalHariIni = Jadwal::with(['spp.murid', 'spp.programKursus', 'progresMurid'])
            ->where('id_guru', $guru->id_guru)
            ->where('is_active', true)
            ->whereDate('tanggal', today())
            ->whereIn('status_jadwal', ['Sesuai Jadwal', 'Reschedule'])
            ->orderBy('jam_mulai')
            ->get();

        return view('guru.dashboard', compact('guru', 'jadwalHariIni'));
    }
}
