<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Spp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PresensiController extends Controller
{
    /**
     * Daftar jadwal guru — bisa filter tanggal.
     * FR-16: Guru membaca status kehadiran murid (via index).
     * FR-18: Guru mencatat kehadiran mengajar.
     */
    public function index(Request $request)
    {
        $guru = Guru::where('id_user', Auth::id())->firstOrFail();
        $bulan = $request->bulan ?? now()->format('Y-m');
        $startDate = Carbon::parse($bulan.'-01')->startOfMonth()->toDateString();
        $endDate = Carbon::parse($bulan.'-01')->endOfMonth()->toDateString();

        $jadwals = Jadwal::with(['spp.murid', 'spp.programKursus', 'progresMurid'])
            ->where('id_guru', $guru->id_guru)
            ->where('is_active', true)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get();

        $jadwalGrouped = $jadwals->groupBy(fn ($j) => $j->tanggal->format('Y-m-d'));

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
        $guru = Guru::where('id_user', Auth::id())->firstOrFail();
        $bulan = $request->bulan ?? now()->format('Y-m');
        $startDate = Carbon::parse($bulan.'-01')->startOfMonth()->toDateString();
        $endDate = Carbon::parse($bulan.'-01')->endOfMonth()->toDateString();

        // Batch load all jadwals for this teacher in the given month (0 N+1)
        $allJadwals = Jadwal::where('id_guru', $guru->id_guru)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->where('is_active', true)
            ->orderBy('tanggal')
            ->get()
            ->groupBy('id_spp');

        // Rekap per murid
        $rekapAbsensi = Spp::with(['murid', 'programKursus'])
            ->whereIn('id_spp', $allJadwals->keys())
            ->get()
            ->map(function (Spp $spp) use ($allJadwals) {
                $jadwals = $allJadwals->get($spp->id_spp, collect());
                $total = $jadwals->count();
                $hadir = $jadwals->where('status_kehadiran_murid', 'Hadir')->count();
                $tidakHadir = $jadwals->where('status_kehadiran_murid', 'Tidak Hadir')->count();
                $belumDiisi = $jadwals->whereNull('status_kehadiran_murid')->count();

                return (object) [
                    'spp' => $spp,
                    'murid' => $spp->murid,
                    'program' => $spp->programKursus,
                    'jadwals' => $jadwals,
                    'total_sesi' => $total,
                    'hadir' => $hadir,
                    'tidak_hadir' => $tidakHadir,
                    'belum_diisi' => $belumDiisi,
                    'persen_hadir' => $total > 0 ? round(($hadir / $total) * 100) : 0,
                ];
            })
            ->sortBy('murid.nama_murid')
            ->values();

        // Detail per sesi jika ada filter murid
        $detailJadwals = collect();
        $selectedSpp = null;

        if ($request->filled('id_spp')) {
            $selectedSpp = Spp::with(['murid', 'programKursus'])->find($request->id_spp);
            if ($selectedSpp) {
                $detailJadwals = Jadwal::with('progresMurid')
                    ->where('id_guru', $guru->id_guru)
                    ->where('id_spp', $request->id_spp)
                    ->whereBetween('tanggal', [$startDate, $endDate])
                    ->where('is_active', true)
                    ->orderBy('tanggal')
                    ->orderBy('jam_mulai')
                    ->get();
            }
        }

        // Statistik keseluruhan
        $totalSesiAll = $rekapAbsensi->sum('total_sesi');
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
            'id_jadwal' => 'required|exists:jadwals,id_jadwal',
            'status_kehadiran_murid' => 'required|in:Hadir,Tidak Hadir',
            'status_kehadiran_guru' => 'required|in:Hadir,Tidak Hadir',
        ]);

        $guru = Guru::where('id_user', Auth::id())->firstOrFail();

        $result = DB::transaction(function () use ($request, $guru) {
            $jadwal = Jadwal::where('id_jadwal', $request->id_jadwal)
                ->where('id_guru', $guru->id_guru)
                ->lockForUpdate()
                ->firstOrFail();

            // Validasi jadwal sudah dimulai
            $jadwalMulai = Carbon::parse(
                $jadwal->tanggal->format('Y-m-d').' '.$jadwal->jam_mulai
            );
            if (now()->lt($jadwalMulai)) {
                return [
                    'status' => 'error',
                    'message' => 'Presensi belum bisa diisi sebelum jadwal dimulai pukul '.substr($jadwal->jam_mulai, 0, 5).'.',
                ];
            }

            if ($jadwal->waktu_presensi_diisi !== null) {
                return [
                    'status' => 'error',
                    'message' => 'Presensi jadwal ini sudah diisi dan tidak dapat diubah lagi.',
                ];
            }

            $jadwal->update([
                'status_kehadiran_murid' => $request->status_kehadiran_murid,
                'status_kehadiran_guru' => $request->status_kehadiran_guru,
                'waktu_presensi_diisi' => now(),
                'presensi_diisi_oleh' => 'Guru',
            ]);

            return [
                'status' => 'success',
                'message' => 'Presensi berhasil dicatat.',
                'tanggal' => $jadwal->tanggal->format('Y-m-d'),
            ];
        });

        if ($result['status'] === 'error') {
            return back()->with('error', $result['message']);
        }

        return redirect()
            ->route('guru.presensi.index', ['tanggal' => $result['tanggal']])
            ->with('success', $result['message']);
    }
}
