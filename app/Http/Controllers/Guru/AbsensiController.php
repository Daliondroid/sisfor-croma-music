<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Spp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    /**
     * FR-16: Melihat Data Absensi.
     * FR-17: Memverifikasi Absensi Murid (konfirmasi kehadiran yang diisi murid).
     */
    public function index(Request $request)
    {
        $guru  = Guru::where('id_user', Auth::id())->firstOrFail();
        $bulan = $request->bulan ?? now()->format('Y-m');
        [$tahun, $bln] = explode('-', $bulan);

        // Ambil semua jadwal guru bulan ini grouped by id_spp (Batch query to eliminate N+1)
        $allJadwals = Jadwal::where('id_guru', $guru->id_guru)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bln)
            ->where('is_active', true)
            ->get()
            ->groupBy('id_spp');

        $rekapAbsensi = Spp::with(['murid', 'programKursus'])
            ->whereIn('id_spp', $allJadwals->keys())
            ->get()
            ->map(function (Spp $spp) use ($allJadwals) {
                $jadwals    = $allJadwals->get($spp->id_spp, collect());
                $total      = $jadwals->count();
                $hadir      = $jadwals->where('status_kehadiran_murid', 'Hadir')->count();
                $tidakHadir = $jadwals->where('status_kehadiran_murid', 'Tidak Hadir')->count();
                $belumDiisi = $jadwals->whereNull('status_kehadiran_murid')->count();
                $menunggu   = $jadwals->where('presensi_diisi_oleh', 'Murid')
                                      ->whereNull('verified_at')
                                      ->count();

                return (object) [
                    'spp'          => $spp,
                    'murid'        => $spp->murid,
                    'program'      => $spp->programKursus,
                    'jadwals'      => $jadwals,
                    'total_sesi'   => $total,
                    'hadir'        => $hadir,
                    'tidak_hadir'  => $tidakHadir,
                    'belum_diisi'  => $belumDiisi,
                    'menunggu'     => $menunggu,
                    'persen_hadir' => $total > 0 ? round(($hadir / $total) * 100) : 0,
                ];
            })
            ->sortBy('murid.nama_murid')
            ->values();

        // Statistik keseluruhan
        $totalSesiAll  = $rekapAbsensi->sum('total_sesi');
        $totalHadirAll = $rekapAbsensi->sum('hadir');
        $totalAbsenAll = $rekapAbsensi->sum('tidak_hadir');
        $totalBelumAll = $rekapAbsensi->sum('belum_diisi');
        $totalMenunggu = $rekapAbsensi->sum('menunggu');

        // Detail per murid jika ada filter
        $detailJadwals = collect();
        $selectedSpp   = null;

        if ($request->filled('id_spp')) {
            $selectedSpp = Spp::with(['murid', 'programKursus'])->find($request->id_spp);
            if ($selectedSpp) {
                $detailJadwals = Jadwal::with('progresMurid')
                    ->where('id_guru', $guru->id_guru)
                    ->where('id_spp', $request->id_spp)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bln)
                    ->where('is_active', true)
                    ->orderBy('tanggal')
                    ->orderBy('jam_mulai')
                    ->get();
            }
        }

        return view('guru.absensi.index', compact(
            'guru', 'bulan', 'rekapAbsensi', 'detailJadwals', 'selectedSpp',
            'totalSesiAll', 'totalHadirAll', 'totalAbsenAll', 'totalBelumAll', 'totalMenunggu'
        ));
    }

    /**
     * FR-17: Verifikasi absensi murid.
     * Guru mengkonfirmasi kehadiran yang sudah diisi oleh murid.
     */
    public function verifikasi(Request $request, int $id)
    {
        $guru   = Guru::where('id_user', Auth::id())->firstOrFail();
        $jadwal = Jadwal::where('id_jadwal', $id)
            ->where('id_guru', $guru->id_guru)
            ->firstOrFail();

        // Hanya bisa verifikasi jika diisi oleh murid
        if ($jadwal->presensi_diisi_oleh !== 'Murid') {
            return back()->with('error', 'Absensi ini tidak perlu diverifikasi.');
        }

        $jadwal->update([
            'verified_at'    => now(),
            'verified_by'    => Auth::id(),
        ]);

        return back()->with('success', 'Absensi murid berhasil diverifikasi.');
    }
}
