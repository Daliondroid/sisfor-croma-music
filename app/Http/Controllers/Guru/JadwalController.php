<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    /**
     * FR-13: Melihat Jadwal Kelas.
     * Guru melihat semua jadwal mengajar yang aktif, bisa filter per bulan/minggu.
     */
    public function index(Request $request)
    {
        $guru = Guru::where('id_user', Auth::id())
            ->with('spesialisasis')
            ->firstOrFail();

        $bulan = $request->bulan ?? now()->format('Y-m');
        [$tahun, $bln] = explode('-', $bulan);

        // Jadwal bulan ini
        $jadwals = Jadwal::with(['spp.murid', 'spp.programKursus'])
            ->where('id_guru', $guru->id_guru)
            ->where('is_active', true)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bln)
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get();

        // Statistik cepat
        $totalJadwal   = $jadwals->count();
        $jadwalHariIni = $jadwals->filter(fn($j) => $j->tanggal->isToday())->count();
        $sudahPresensi = $jadwals->whereNotNull('waktu_presensi_diisi')->count();
        $belumPresensi = $totalJadwal - $sudahPresensi;

        // Group per tanggal untuk tampilan kalender-list
        $jadwalGrouped = $jadwals->groupBy(fn($j) => $j->tanggal->format('Y-m-d'));

        return view('guru.jadwal.index', compact(
            'guru', 'jadwals', 'jadwalGrouped', 'bulan',
            'totalJadwal', 'jadwalHariIni', 'sudahPresensi', 'belumPresensi'
        ));
    }
}
