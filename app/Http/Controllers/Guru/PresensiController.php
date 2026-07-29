<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Guru;
use App\Models\Spp;
use App\Models\ProgresMurid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{
    /**
     * Daftar jadwal guru — bisa filter tanggal.
     * FR-16: Guru membaca status kehadiran murid (via index).
     * FR-18: Guru mencatat kehadiran mengajar.
     */
    public function index(Request $request)
    {
        $guru  = Guru::where('id_user', Auth::id())->firstOrFail();
        $bulan = $request->bulan ?? now()->format('Y-m');
        [$tahun, $bln] = explode('-', $bulan);

        $jadwals = Jadwal::with(['spp.murid', 'spp.programKursus', 'progresMurid'])
            ->where('id_guru', $guru->id_guru)
            ->where('is_active', true)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bln)
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get();

        $jadwalGrouped = $jadwals->groupBy(fn($j) => $j->tanggal->format('Y-m-d'));

        $selectedJadwal = null;
        if ($request->filled('jadwal')) {
            $selectedJadwal = $jadwals->firstWhere('id_jadwal', $request->jadwal);
        }

        return view('guru.presensi.index', compact(
            'guru', 'bulan', 'jadwals', 'jadwalGrouped'
        ));
    }

    /**
     * FR-16: Rekap / laporan data absensi murid per bulan.
     * Guru melihat kehadiran semua murid yang diajarnya.
     */
    public function rekap(Request $request)
    {
        $guru  = Guru::where('id_user', Auth::id())->firstOrFail();
        $bulan = $request->bulan ?? now()->format('Y-m');

        [$tahun, $bln] = explode('-', $bulan);

        // Semua id_spp yang diajar guru di bulan ini
        $sppIds = Jadwal::where('id_guru', $guru->id_guru)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bln)
            ->where('is_active', true)
            ->pluck('id_spp')
            ->unique();

        // Rekap per murid
        $rekapAbsensi = Spp::with(['murid', 'programKursus'])
            ->whereIn('id_spp', $sppIds)
            ->get()
            ->map(function (Spp $spp) use ($guru, $tahun, $bln) {
                $jadwals = Jadwal::where('id_guru', $guru->id_guru)
                    ->where('id_spp', $spp->id_spp)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bln)
                    ->where('is_active', true)
                    ->orderBy('tanggal')
                    ->get();

                $total       = $jadwals->count();
                $hadir       = $jadwals->where('status_kehadiran_murid', 'Hadir')->count();
                $tidakHadir  = $jadwals->where('status_kehadiran_murid', 'Tidak Hadir')->count();
                $belumDiisi  = $jadwals->whereNull('status_kehadiran_murid')->count();

                return (object) [
                    'spp'          => $spp,
                    'murid'        => $spp->murid,
                    'program'      => $spp->programKursus,
                    'jadwals'      => $jadwals,
                    'total_sesi'   => $total,
                    'hadir'        => $hadir,
                    'tidak_hadir'  => $tidakHadir,
                    'belum_diisi'  => $belumDiisi,
                    'persen_hadir' => $total > 0 ? round(($hadir / $total) * 100) : 0,
                ];
            })
            ->sortBy('murid.nama_murid')
            ->values();

        // Detail per sesi jika ada filter murid
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

        // Statistik keseluruhan
        $totalSesiAll  = $rekapAbsensi->sum('total_sesi');
        $totalHadirAll = $rekapAbsensi->sum('hadir');
        $totalAbsenAll = $rekapAbsensi->sum('tidak_hadir');
        $totalBelumAll = $rekapAbsensi->sum('belum_diisi');
        $totalMenunggu = 0;

        return view('guru.absensi.index', compact(
            'guru', 'bulan', 'rekapAbsensi', 'detailJadwals', 'selectedSpp',
            'totalSesiAll', 'totalHadirAll', 'totalAbsenAll', 'totalBelumAll', 'totalMenunggu'
        ));
    }

    /**
     * Simpan presensi.
     * Immutable setelah waktu_presensi_diisi terisi.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_jadwal'              => 'required|exists:jadwals,id_jadwal',
            'status_kehadiran_murid' => 'required|in:Hadir,Tidak Hadir',
            'status_kehadiran_guru'  => 'required|in:Hadir,Tidak Hadir',
        ]);

        $guru   = Guru::where('id_user', Auth::id())->firstOrFail();
        $jadwal = Jadwal::where('id_jadwal', $request->id_jadwal)
            ->where('id_guru', $guru->id_guru)
            ->firstOrFail();

        // ── BARU: tolak jika jam mulai belum tiba ──────────────
        $jadwalMulai = \Carbon\Carbon::parse(
            $jadwal->tanggal->format('Y-m-d') . ' ' . $jadwal->jam_mulai
        );
        if (now()->lt($jadwalMulai)) {
            return back()->with('error',
                'Presensi belum bisa diisi sebelum jadwal dimulai pukul '
                . substr($jadwal->jam_mulai, 0, 5) . '.'
            );
        }
        // ───────────────────────────────────────────────────────

        if ($jadwal->waktu_presensi_diisi !== null) {
            return back()->with('error', 'Presensi jadwal ini sudah diisi dan tidak dapat diubah lagi.');
        }

        $jadwal->update([
            'status_kehadiran_murid' => $request->status_kehadiran_murid,
            'status_kehadiran_guru'  => $request->status_kehadiran_guru,
            'waktu_presensi_diisi'   => now(),
            'presensi_diisi_oleh'    => 'Guru',
        ]);

        return redirect()
            ->route('guru.presensi.index', ['tanggal' => $jadwal->tanggal->format('Y-m-d')])
            ->with('success', 'Presensi berhasil dicatat.');
    }
}
