<?php

namespace App\Http\Controllers\Murid;

use App\Http\Controllers\Controller;
use App\Models\Murid;
use App\Models\MonthlyReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MonthlyReportController extends Controller
{
    /**
     * Daftar semua monthly report milik murid yang login.
     */
    public function index()
    {
        $murid = Murid::where('id_user', Auth::id())->firstOrFail();

        $reports = $murid->monthlyReports()
            ->with(['spp.programKursus', 'jadwals.progresMurid', 'jadwals.guru'])
            ->orderByDesc('periode_bulan')
            ->get();

        return view('murid.laporan.index', compact('murid', 'reports'));
    }

    /**
     * Detail satu monthly report — video embed + tombol PDF.
     */
    public function show(MonthlyReport $report)
    {
        $murid = Murid::where('id_user', Auth::id())->firstOrFail();
        abort_unless($report->spp?->id_murid === $murid->id_murid, 403);

        $report->load(['spp.programKursus', 'jadwals.progresMurid', 'jadwals.guru']);

        return view('murid.laporan.show', compact('murid', 'report'));
    }

    /**
     * Generate dan stream PDF laporan bulanan.
     * Dipanggil saat murid klik "Download PDF".
     */
    public function exportPdf(MonthlyReport $report): StreamedResponse
    {
        $murid = Murid::where('id_user', Auth::id())->firstOrFail();
        abort_unless($report->spp?->id_murid === $murid->id_murid, 403);

        $report->load(['spp.programKursus', 'jadwals.progresMurid', 'jadwals.guru']);

        $bulanLabel = Carbon::parse($report->periode_bulan)->translatedFormat('F Y');
        $totalSesi  = $report->jadwals->count();
        $hadirSesi  = $report->jadwals->where('status_kehadiran_murid', 'Hadir')->count();
        $pct        = $totalSesi > 0 ? round($hadirSesi / $totalSesi * 100) : 0;

        // Susun data jadwal per pertemuan
        $jadwalsData = $report->jadwals
            ->sortBy('tanggal')
            ->values()
            ->map(fn($j, $i) => [
                'no'      => $i + 1,
                'tanggal' => $j->tanggal->format('d/m/Y'),
                'materi'  => $j->progresMurid?->materi_diajarkan ?? '-',
            ])
            ->toArray();

        // Payload JSON untuk script Python
        $payload = json_encode([
            'nama_murid'        => $murid->nama_murid,
            'nama_guru'         => $report->jadwals->first()?->guru?->nama_guru ?? '-',
            'nama_program'      => $report->spp?->programKursus?->nama_program ?? '-',
            'bulan_label'       => $bulanLabel,
            'kota'              => 'Bekasi',          // sesuaikan kota sekolah
            'skor'              => $report->skor ?? '—',
            'pct_hadir'         => $pct,
            'total_hadir'       => $hadirSesi,
            'total_sesi'        => $totalSesi,
            'evaluasi_bulanan'  => $report->evaluasi_bulanan ?? '',
            'jadwals'           => $jadwalsData,
        ], JSON_UNESCAPED_UNICODE);

        // Jalankan script Python generator
        $scriptPath = base_path('scripts/generate_laporan_pdf.py');
        $tmpOut     = tempnam(sys_get_temp_dir(), 'laporan_') . '.pdf';

        $escaped  = escapeshellarg($payload);
        $cmd      = "python3 {$scriptPath} {$escaped} " . escapeshellarg($tmpOut) . " 2>&1";
        exec($cmd, $output, $code);

        if ($code !== 0 || !file_exists($tmpOut)) {
            abort(500, 'Gagal generate PDF: ' . implode("\n", $output));
        }

        $filename = 'laporan_' . $murid->nama_murid . '_' . Carbon::parse($report->periode_bulan)->format('Y-m') . '.pdf';

        return response()->streamDownload(function () use ($tmpOut) {
            echo file_get_contents($tmpOut);
            @unlink($tmpOut);
        }, $filename, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}