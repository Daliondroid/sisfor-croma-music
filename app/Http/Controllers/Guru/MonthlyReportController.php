<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\MonthlyReport;
use App\Models\Spp;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonthlyReportController extends Controller
{
    /**
     * Daftar murid yang diajar guru ini + status monthly report per bulan.
     *
     * FR-28: Guru membuat laporan bulanan.
     * Record baru pada MONTHLY_REPORT terbentuk, mencakup evaluasi & url_video.
     */
    public function index(Request $request)
    {
        $guru = Guru::where('id_user', Auth::id())->firstOrFail();
        $bulan = $request->bulan ?? now()->format('Y-m');

        [$tahun, $bln] = explode('-', $bulan);

        // Ambil semua SPP (murid) yang diajar guru di bulan ini
        $sppIds = Jadwal::where('id_guru', $guru->id_guru)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bln)
            ->where('is_active', true)
            ->pluck('id_spp')
            ->unique();

        $spps = Spp::with(['murid', 'programKursus'])
            ->whereIn('id_spp', $sppIds)
            ->get()
            ->map(function (Spp $spp) use ($guru, $tahun, $bln) {
                // Rekap jadwal bulan ini
                $jadwals = Jadwal::where('id_guru', $guru->id_guru)
                    ->where('id_spp', $spp->id_spp)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bln)
                    ->where('is_active', true)
                    ->get();

                $stats = Jadwal::calculateAttendanceStats($jadwals);

                $spp->total_sesi = $stats['total_sesi'];
                $spp->hadir_murid = $stats['hadir'];
                $spp->hadir_guru = $jadwals->where('status_kehadiran_guru', 'Hadir')->count();
                $spp->persen = $stats['persen_hadir'];

                // Cek apakah report sudah dibuat
                $spp->report = MonthlyReport::where('id_spp', $spp->id_spp)
                    ->whereYear('periode_bulan', $tahun)
                    ->whereMonth('periode_bulan', $bln)
                    ->first();

                return $spp;
            });

        return view('guru.monthly_report.index', compact('guru', 'spps', 'bulan'));
    }

    /**
     * Form buat / edit monthly report untuk satu murid.
     */
    public function create(Request $request)
    {
        $guru = Guru::where('id_user', Auth::id())->firstOrFail();
        $bulan = $request->bulan ?? now()->format('Y-m');
        [$tahun, $bln] = explode('-', $bulan);

        $spp = Spp::with(['murid', 'programKursus'])
            ->where('id_spp', $request->id_spp)
            ->firstOrFail();

        // Validasi: SPP ini memang diajar guru yang login di bulan itu
        $jadwals = Jadwal::with('progresMurid')
            ->where('id_guru', $guru->id_guru)
            ->where('id_spp', $spp->id_spp)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bln)
            ->where('is_active', true)
            ->orderBy('tanggal')
            ->get();

        abort_if($jadwals->isEmpty(), 403, 'Anda tidak mengajar murid ini di bulan tersebut.');

        // Cek apakah sudah ada report
        $report = MonthlyReport::where('id_spp', $spp->id_spp)
            ->whereYear('periode_bulan', $tahun)
            ->whereMonth('periode_bulan', $bln)
            ->first();

        // Auto-hitung skor dari kehadiran
        $stats = Jadwal::calculateAttendanceStats($jadwals);
        $totalSesi = $stats['total_sesi'];
        $totalHadir = $stats['hadir'];
        $persen = $stats['persen_hadir'];
        $skorOtomatis = MonthlyReport::calculateScore($persen);

        return view('guru.monthly_report.create', compact(
            'guru', 'spp', 'jadwals', 'bulan', 'report',
            'totalSesi', 'totalHadir', 'persen', 'skorOtomatis'
        ));
    }

    /**
     * Simpan atau update monthly report.
     * Guru mengisi field evaluasi teks dan URL video KBM bulanan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_spp' => 'required|exists:spps,id_spp',
            'bulan' => 'required|date_format:Y-m',
            'skor' => 'required|in:A+,A,A-,B+,B,B-,C+,C,C-',
            'evaluasi_bulanan' => 'required|string|max:3000',
            'url_video' => 'nullable|url|max:500',
        ]);

        $guru = Guru::where('id_user', Auth::id())->firstOrFail();
        [$tahun, $bln] = explode('-', $request->bulan);

        $spp = Spp::findOrFail($request->id_spp);

        // Validasi kepemilikan
        $jadwalCount = Jadwal::where('id_guru', $guru->id_guru)
            ->where('id_spp', $spp->id_spp)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bln)
            ->where('is_active', true)
            ->count();

        abort_if($jadwalCount === 0, 403);

        MonthlyReport::updateOrCreate(
            [
                'id_spp' => $spp->id_spp,
                'periode_bulan' => $request->bulan.'-01',
            ],
            [
                'skor' => $request->skor,
                'evaluasi_bulanan' => $request->evaluasi_bulanan,
                'url_video' => $request->url_video,
            ]
        );

        return redirect()
            ->route('guru.monthly-report.index', ['bulan' => $request->bulan])
            ->with('success', 'Laporan bulanan berhasil disimpan.');
    }

    /**
     * Detail monthly report satu murid.
     */
    public function show(Request $request, MonthlyReport $monthlyReport)
    {
        $guru = Guru::where('id_user', Auth::id())->firstOrFail();

        // Validasi akses
        $spp = $monthlyReport->spp;
        $bulan = $monthlyReport->periode_bulan->format('Y-m');
        [$tahun, $bln] = explode('-', $bulan);

        $jadwals = Jadwal::with(['progresMurid', 'spp.murid', 'spp.programKursus'])
            ->where('id_guru', $guru->id_guru)
            ->where('id_spp', $spp->id_spp)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bln)
            ->where('is_active', true)
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get();

        abort_if($jadwals->isEmpty(), 403);

        $murid = $spp->murid;
        $program = $spp->programKursus;

        return view('guru.monthly_report.show', compact(
            'guru', 'monthlyReport', 'jadwals', 'murid', 'program', 'spp', 'bulan'
        ));
    }

    /**
     * Export monthly report satu murid ke PDF.
     *
     * Struktur PDF (sesuai Note #1 dari permintaan):
     *   - Judul: Capaian Belajar Murid
     *   - Nama Murid, Nama Program Kursus, Nama Guru, Bulan
     *   - Kesimpulan belajar bulan ini (evaluasi_bulanan dari guru)
     *   - Tabel 4 pertemuan: tanggal, materi pembelajaran, catatan perkembangan
     */
    public function exportPdf(MonthlyReport $monthlyReport)
    {
        $guru = Guru::where('id_user', Auth::id())->firstOrFail();

        $spp = $monthlyReport->spp->load(['murid', 'programKursus']);
        $bulan = $monthlyReport->periode_bulan->format('Y-m');
        [$tahun, $bln] = explode('-', $bulan);

        // Ambil jadwal + progres murid bulan ini
        $jadwals = Jadwal::with('progresMurid')
            ->where('id_guru', $guru->id_guru)
            ->where('id_spp', $spp->id_spp)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bln)
            ->where('is_active', true)
            ->orderBy('tanggal')
            ->get();

        abort_if($jadwals->isEmpty(), 403);

        // Kelompokkan dalam blok-blok 4 pertemuan
        $blokPertemuan = $jadwals->chunk(4);

        $pdf = Pdf::loadView('guru.monthly_report.pdf', compact(
            'guru', 'monthlyReport', 'spp', 'jadwals', 'blokPertemuan', 'bulan'
        ))->setPaper('A4', 'portrait');

        $namaFile = 'laporan-bulanan-'
            .str()->slug($spp->murid->nama_murid).'-'
            .$bulan.'.pdf';

        return $pdf->download($namaFile);
    }
}
