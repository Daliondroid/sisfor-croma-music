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
     * Detail satu monthly report.
     */
    public function show(MonthlyReport $report)
    {
        $murid = Murid::where('id_user', Auth::id())->firstOrFail();
        abort_unless($report->spp?->id_murid === $murid->id_murid, 403);

        $report->load(['spp.programKursus', 'jadwals.progresMurid', 'jadwals.guru']);

        return view('murid.laporan.show', compact('murid', 'report'));
    }

    /**
     * Generate dan download PDF laporan bulanan menggunakan DomPDF.
     * Pastikan package sudah diinstall:
     *   composer require barryvdh/laravel-dompdf
     */
    public function exportPdf(MonthlyReport $report)
    {
        $murid = Murid::where('id_user', Auth::id())->firstOrFail();
        abort_unless($report->spp?->id_murid === $murid->id_murid, 403);

        $report->load(['spp.programKursus', 'jadwals.progresMurid', 'jadwals.guru']);

        $bulanLabel = Carbon::parse($report->periode_bulan)->translatedFormat('F Y');
        $totalSesi  = $report->jadwals->count();
        $hadirSesi  = $report->jadwals->where('status_kehadiran_murid', 'Hadir')->count();
        $pct        = $totalSesi > 0 ? round($hadirSesi / $totalSesi * 100) : 0;
        $guruNama   = $report->jadwals->first()?->guru?->nama_guru ?? '-';

        $jadwalsData = $report->jadwals
            ->sortBy('tanggal')
            ->values()
            ->map(fn($j, $i) => [
                'no'      => $i + 1,
                'tanggal' => $j->tanggal->format('d/m/Y'),
                'materi'  => $j->progresMurid?->materi_diajarkan ?? '-',
                'catatan' => $j->progresMurid?->catatan_perkembangan ?? '',
                'hadir'   => $j->status_kehadiran_murid ?? '—',
            ]);

        $data = [
            'murid'       => $murid,
            'report'      => $report,
            'bulanLabel'  => $bulanLabel,
            'totalSesi'   => $totalSesi,
            'hadirSesi'   => $hadirSesi,
            'pct'         => $pct,
            'guruNama'    => $guruNama,
            'jadwalsData' => $jadwalsData,
            'namaProgram' => $report->spp?->programKursus?->nama_program ?? '-',
            'kota'        => 'Bekasi',
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('murid.laporan.pdf', $data)
            ->setPaper('a4', 'portrait');

        $filename = 'laporan_' . str($murid->nama_murid)->slug('_') . '_' . Carbon::parse($report->periode_bulan)->format('Y-m') . '.pdf';

        return $pdf->download($filename);
    }
}