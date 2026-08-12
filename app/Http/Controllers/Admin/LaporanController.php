<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LaporanExcelService;
use App\Services\LaporanReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function __construct(
        protected LaporanReportService $reportService,
        protected LaporanExcelService $excelService
    ) {}

    /**
     * Tampilkan halaman laporan keuangan dengan filter date range.
     */
    public function keuangan(Request $request)
    {
        [$bulan, $startDate, $endDate] = $this->reportService->resolveDateRange($request->bulan, $request->start_date, $request->end_date);
        $data = $this->reportService->getKeuanganData($startDate, $endDate);

        return view('admin.laporan.keuangan', array_merge($data, compact('bulan', 'startDate', 'endDate')));
    }

    /**
     * Tampilkan halaman rekap absensi per murid.
     */
    public function absensi(Request $request)
    {
        $bulan = $request->bulan ?? now()->format('Y-m');
        $data = $this->reportService->getAbsensiData($bulan);

        return view('admin.laporan.absensi', array_merge($data, compact('bulan')));
    }

    /**
     * Tampilkan halaman rekap gaji guru.
     */
    public function gajiGuru(Request $request)
    {
        [$bulan, $startDate, $endDate] = $this->reportService->resolveDateRange($request->bulan, $request->start_date, $request->end_date);
        $data = $this->reportService->getGajiData($startDate, $endDate);

        return view('admin.laporan.gaji', array_merge($data, compact('bulan', 'startDate', 'endDate')));
    }

    /**
     * FR-26: Ekspor laporan ke PDF.
     * Mendukung jenis: keuangan, absensi, gaji
     */
    public function exportPdf(Request $request, string $jenis)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ], [
            'end_date.after_or_equal' => 'Tanggal akhir tidak boleh lebih awal dari tanggal mulai.',
        ]);

        [$bulan, $startDate, $endDate] = $this->reportService->resolveDateRange($request->bulan, $request->start_date, $request->end_date);

        [$data, $namaFile] = match ($jenis) {
            'keuangan' => [$this->reportService->getKeuanganData($startDate, $endDate), 'laporan-keuangan-'.$startDate.'-sd-'.$endDate],
            'absensi' => [$this->reportService->getAbsensiData($bulan), 'rekap-absensi-'.$bulan],
            'gaji' => [$this->reportService->getGajiData($startDate, $endDate), 'rekap-gaji-guru-'.$startDate.'-sd-'.$endDate],
            default => abort(404, 'Jenis laporan tidak dikenali.'),
        };

        $pdf = Pdf::loadView("admin.laporan.pdf.{$jenis}", array_merge(
            $data,
            compact('bulan', 'startDate', 'endDate')
        ))->setPaper('A4', 'portrait');

        return $pdf->download("{$namaFile}.pdf");
    }

    /**
     * FR-26: Ekspor laporan ke Excel (.xlsx).
     * Mendukung jenis: keuangan, gaji, absensi
     */
    public function exportXlsx(Request $request, string $jenis)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ], [
            'end_date.after_or_equal' => 'Tanggal akhir tidak boleh lebih awal dari tanggal mulai.',
        ]);

        [$bulan, $startDate, $endDate] = $this->reportService->resolveDateRange($request->bulan, $request->start_date, $request->end_date);

        return match ($jenis) {
            'keuangan' => $this->excelService->generateKeuanganXlsx($this->reportService->getKeuanganData($startDate, $endDate), $startDate, $endDate),
            'gaji' => $this->excelService->generateGajiXlsx($this->reportService->getGajiData($startDate, $endDate), $startDate, $endDate),
            'absensi' => $this->excelService->generateAbsensiXlsx($this->reportService->getAbsensiData($bulan), $bulan),
            default => abort(404, 'Jenis laporan tidak dikenali.'),
        };
    }
}
