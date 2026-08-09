<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Spp;
use App\Models\HonorGuru;
use App\Models\Guru;
use App\Models\Murid;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    // ══════════════════════════════════════════════════════════════
    //  LAPORAN KEUANGAN
    // ══════════════════════════════════════════════════════════════

    /**
     * Tampilkan halaman laporan keuangan dengan filter date range.
     */
    public function keuangan(Request $request)
    {
        $bulan      = $request->bulan ?? now()->format('Y-m');
        $startDate  = $request->start_date ?? Carbon::parse($bulan . '-01')->startOfMonth()->format('Y-m-d');
        $endDate    = $request->end_date   ?? Carbon::parse($bulan . '-01')->endOfMonth()->format('Y-m-d');

        $query = Spp::with(['murid', 'programKursus', 'transaksi'])
            ->whereBetween('periode_tagihan', [
                Carbon::parse($startDate)->startOfMonth()->format('Y-m-d'),
                Carbon::parse($endDate)->endOfMonth()->format('Y-m-d'),
            ]);

        $spps         = $query->latest('periode_tagihan')->get();
        $totalMasuk   = $spps->where('status_bayar', 'Lunas')->sum('nominal_tagihan');
        $totalTagihan = $spps->sum('nominal_tagihan');
        $totalTunggakan = $totalTagihan - $totalMasuk;

        return view('admin.laporan.keuangan', compact(
            'spps', 'totalMasuk', 'totalTagihan', 'totalTunggakan',
            'bulan', 'startDate', 'endDate'
        ));
    }

    /**
     * Tampilkan halaman rekap absensi per murid.
     */
    public function absensi(Request $request)
    {
        $bulan = $request->bulan ?? now()->format('Y-m');
        [$tahun, $bln] = explode('-', $bulan);

        // Build a database-level aggregation subquery so no jadwal rows are
        // loaded into PHP memory.  The subquery counts sessions per murid
        // directly inside MySQL, then we join it onto the murids table.
        $attendanceSub = DB::table('jadwals')
            ->join('spps', 'jadwals.id_spp', '=', 'spps.id_spp')
            ->whereYear('jadwals.tanggal', $tahun)
            ->whereMonth('jadwals.tanggal', $bln)
            ->where('jadwals.is_active', true)
            ->select(
                'spps.id_murid',
                DB::raw('COUNT(*) as total_sesi'),
                DB::raw("SUM(CASE WHEN jadwals.status_kehadiran_murid = 'Hadir' THEN 1 ELSE 0 END) as total_hadir"),
                DB::raw("SUM(CASE WHEN jadwals.status_kehadiran_murid = 'Tidak Hadir' THEN 1 ELSE 0 END) as total_absen"),
                DB::raw('SUM(CASE WHEN jadwals.status_kehadiran_murid IS NULL THEN 1 ELSE 0 END) as belum_diisi')
            )
            ->groupBy('spps.id_murid');

        $murids = Murid::where('status_aktif', true)
            ->leftJoinSub($attendanceSub, 'att', 'murids.id_murid', '=', 'att.id_murid')
            ->select(
                'murids.*',
                DB::raw('COALESCE(att.total_sesi, 0)   as total_sesi'),
                DB::raw('COALESCE(att.total_hadir, 0)  as total_hadir'),
                DB::raw('COALESCE(att.total_absen, 0)  as total_absen'),
                DB::raw('COALESCE(att.belum_diisi, 0)  as belum_diisi'),
                DB::raw(
                    'CASE WHEN COALESCE(att.total_sesi, 0) > 0'
                    . ' THEN ROUND(COALESCE(att.total_hadir, 0) / att.total_sesi * 100, 1)'
                    . ' ELSE 0 END as persen_hadir'
                )
            )
            ->get();

        return view('admin.laporan.absensi', compact('murids', 'bulan'));
    }

    /**
     * Tampilkan halaman rekap gaji guru.
     */
    public function gajiGuru(Request $request)
    {
        $bulan     = $request->bulan ?? now()->format('Y-m');
        $startDate = $request->start_date ?? Carbon::parse($bulan . '-01')->startOfMonth()->format('Y-m-d');
        $endDate   = $request->end_date   ?? Carbon::parse($bulan . '-01')->endOfMonth()->format('Y-m-d');

        $honors = HonorGuru::with(['guru', 'jadwals.spp.murid'])
            ->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ])
            ->latest()
            ->get();

        // Grup per guru untuk ringkasan
        $ringkasanGuru = $honors->groupBy('id_guru')->map(function ($items) {
            $guru = $items->first()->guru;
            return [
                'guru'             => $guru,
                'total_honor'      => $items->sum('jumlah_honor'),
                'total_pertemuan'  => $items->sum('jumlah_pertemuan'),
                'total_lunas'      => $items->where('status_bayar', 'Lunas')->sum('jumlah_honor'),
                'total_pending'    => $items->whereIn('status_bayar', ['Belum Lunas', 'Siap Dibayar'])->sum('jumlah_honor'),
                'records'          => $items,
            ];
        })->values();

        return view('admin.laporan.gaji', compact(
            'honors', 'ringkasanGuru', 'bulan', 'startDate', 'endDate'
        ));
    }

    // ══════════════════════════════════════════════════════════════
    //  EKSPOR PDF
    // ══════════════════════════════════════════════════════════════

    /**
     * FR-26: Ekspor laporan ke PDF.
     * Mendukung jenis: keuangan, absensi, gaji
     */
    public function exportPdf(Request $request, string $jenis)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ], [
            'end_date.after_or_equal' => 'Tanggal akhir tidak boleh lebih awal dari tanggal mulai.',
        ]);

        $bulan     = $request->bulan ?? now()->format('Y-m');
        $startDate = $request->start_date ?? Carbon::parse($bulan . '-01')->startOfMonth()->format('Y-m-d');
        $endDate   = $request->end_date   ?? Carbon::parse($bulan . '-01')->endOfMonth()->format('Y-m-d');

        [$data, $view, $namaFile] = match ($jenis) {
            'keuangan' => $this->dataKeuangan($startDate, $endDate),
            'absensi'  => $this->dataAbsensi($bulan),
            'gaji'     => $this->dataGaji($startDate, $endDate),
            default    => abort(404, 'Jenis laporan tidak dikenali.'),
        };

        $pdf = Pdf::loadView("admin.laporan.pdf.{$jenis}", array_merge(
            $data,
            compact('bulan', 'startDate', 'endDate')
        ))->setPaper('A4', 'portrait');

        return $pdf->download("{$namaFile}.pdf");
    }

    // ══════════════════════════════════════════════════════════════
    //  EKSPOR XLSX
    // ══════════════════════════════════════════════════════════════

    /**
     * FR-26: Ekspor laporan ke Excel (.xlsx).
     * Mendukung jenis: keuangan, gaji
     */
    public function exportXlsx(Request $request, string $jenis)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ], [
            'end_date.after_or_equal' => 'Tanggal akhir tidak boleh lebih awal dari tanggal mulai.',
        ]);

        $bulan     = $request->bulan ?? now()->format('Y-m');
        $startDate = $request->start_date ?? Carbon::parse($bulan . '-01')->startOfMonth()->format('Y-m-d');
        $endDate   = $request->end_date   ?? Carbon::parse($bulan . '-01')->endOfMonth()->format('Y-m-d');

        return match ($jenis) {
            'keuangan' => $this->xlsxKeuangan($startDate, $endDate, $bulan),
            'gaji'     => $this->xlsxGaji($startDate, $endDate, $bulan),
            'absensi'  => $this->xlsxAbsensi($bulan),
            default    => abort(404, 'Jenis laporan tidak dikenali.'),
        };
    }

    // ══════════════════════════════════════════════════════════════
    //  PRIVATE: DATA HELPERS
    // ══════════════════════════════════════════════════════════════

    private function dataKeuangan(string $startDate, string $endDate): array
    {
        $spps = Spp::with(['murid', 'programKursus', 'transaksi'])
            ->whereBetween('periode_tagihan', [
                Carbon::parse($startDate)->startOfMonth()->format('Y-m-d'),
                Carbon::parse($endDate)->endOfMonth()->format('Y-m-d'),
            ])
            ->latest('periode_tagihan')
            ->get();

        $totalMasuk     = $spps->where('status_bayar', 'Lunas')->sum('nominal_tagihan');
        $totalTagihan   = $spps->sum('nominal_tagihan');
        $totalTunggakan = $totalTagihan - $totalMasuk;

        $namaFile = 'laporan-keuangan-' . $startDate . '-sd-' . $endDate;

        return [
            ['spps' => $spps, 'totalMasuk' => $totalMasuk, 'totalTagihan' => $totalTagihan, 'totalTunggakan' => $totalTunggakan],
            'keuangan',
            $namaFile,
        ];
    }

    private function dataAbsensi(string $bulan): array
    {
        [$tahun, $bln] = explode('-', $bulan);

        $murids = Murid::where('status_aktif', true)
            ->with(['spps.jadwals' => function ($q) use ($tahun, $bln) {
                $q->where('is_active', true)
                  ->whereYear('tanggal', $tahun)
                  ->whereMonth('tanggal', $bln);
            }])->get()->map(function ($murid) {
            $jadwals = collect();
            if ($murid->spps) {
                foreach ($murid->spps as $spp) {
                    if ($spp->jadwals) {
                        $jadwals = $jadwals->merge($spp->jadwals);
                    }
                }
            }

            $murid->total_sesi   = $jadwals->count();
            $murid->total_hadir  = $jadwals->where('status_kehadiran_murid', 'Hadir')->count();
            $murid->total_absen  = $jadwals->where('status_kehadiran_murid', 'Tidak Hadir')->count();
            $murid->belum_diisi  = $jadwals->whereNull('status_kehadiran_murid')->count();
            $murid->persen_hadir = $murid->total_sesi > 0
                ? round(($murid->total_hadir / $murid->total_sesi) * 100, 1) : 0;
            return $murid;
        });

        $namaFile = 'rekap-absensi-' . $bulan;

        return [
            ['murids' => $murids],
            'absensi',
            $namaFile,
        ];
    }

    private function dataGaji(string $startDate, string $endDate): array
    {
        $honors = HonorGuru::with(['guru', 'jadwals.spp.murid'])
            ->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ])
            ->latest()
            ->get();

        $ringkasanGuru = $honors->groupBy('id_guru')->map(function ($items) {
            return [
                'guru'            => $items->first()->guru,
                'total_honor'     => $items->sum('jumlah_honor'),
                'total_pertemuan' => $items->sum('jumlah_pertemuan'),
                'total_lunas'     => $items->where('status_bayar', 'Lunas')->sum('jumlah_honor'),
                'total_pending'   => $items->whereIn('status_bayar', ['Belum Lunas', 'Siap Dibayar'])->sum('jumlah_honor'),
                'records'         => $items,
            ];
        })->values();

        $namaFile = 'rekap-gaji-guru-' . $startDate . '-sd-' . $endDate;

        return [
            ['honors' => $honors, 'ringkasanGuru' => $ringkasanGuru],
            'gaji',
            $namaFile,
        ];
    }

    // ══════════════════════════════════════════════════════════════
    //  PRIVATE: XLSX BUILDERS
    // ══════════════════════════════════════════════════════════════

    private function xlsxKeuangan(string $startDate, string $endDate, string $bulan)
    {
        $spps = Spp::with(['murid', 'programKursus', 'transaksi'])
            ->whereBetween('periode_tagihan', [
                Carbon::parse($startDate)->startOfMonth()->format('Y-m-d'),
                Carbon::parse($endDate)->endOfMonth()->format('Y-m-d'),
            ])
            ->latest('periode_tagihan')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Keuangan');

        // ── Styling helpers ──────────────────────────────────────
        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFD1D5DB']]],
        ];
        $subHeaderStyle = [
            'font'      => ['bold' => true, 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFEFF6FF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFD1D5DB']]],
        ];
        $dataStyle = [
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE5E7EB']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ];

        // ── Header laporan ───────────────────────────────────────
        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'LAPORAN KEUANGAN — CROMA MUSIC');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells('A2:H2');
        $label = Carbon::parse($startDate)->isSameMonth(Carbon::parse($endDate))
            ? 'Periode: ' . Carbon::parse($startDate)->translatedFormat('F Y')
            : 'Periode: ' . Carbon::parse($startDate)->format('d/m/Y') . ' s.d. ' . Carbon::parse($endDate)->format('d/m/Y');
        $sheet->setCellValue('A2', $label);
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['size' => 10, 'italic' => true, 'color' => ['argb' => 'FF6B7280']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells('A3:H3');
        $sheet->setCellValue('A3', 'Dibuat: ' . now()->translatedFormat('d F Y, H:i') . ' WIB');
        $sheet->getStyle('A3')->applyFromArray([
            'font'      => ['size' => 9, 'color' => ['argb' => 'FF9CA3AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // ── Ringkasan ────────────────────────────────────────────
        $totalMasuk     = $spps->where('status_bayar', 'Lunas')->sum('nominal_tagihan');
        $totalTagihan   = $spps->sum('nominal_tagihan');
        $totalTunggakan = $totalTagihan - $totalMasuk;
        $pct            = $totalTagihan > 0 ? round(($totalMasuk / $totalTagihan) * 100) : 0;

        $sheet->setCellValue('A5', 'RINGKASAN');
        $sheet->getStyle('A5')->applyFromArray(['font' => ['bold' => true, 'size' => 11]]);

        $summaryData = [
            ['Total Tagihan',     'Rp ' . number_format($totalTagihan, 0, ',', '.')],
            ['Total Masuk (Lunas)', 'Rp ' . number_format($totalMasuk, 0, ',', '.')],
            ['Total Tunggakan',   'Rp ' . number_format($totalTunggakan, 0, ',', '.')],
            ['Tingkat Pembayaran', $pct . '%'],
            ['Jumlah Tagihan',    $spps->count() . ' tagihan'],
        ];

        foreach ($summaryData as $i => [$label, $val]) {
            $row = 6 + $i;
            $sheet->setCellValue('A' . $row, $label);
            $sheet->setCellValue('B' . $row, $val);
            $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE5E7EB']]],
            ]);
        }

        $sheet->getStyle('B6:B10')->applyFromArray(['font' => ['bold' => true]]);

        // ── Tabel detail ─────────────────────────────────────────
        $startRow = 13;
        $headers  = ['#', 'Nama Murid', 'Program Kursus', 'Periode', 'Nominal (Rp)', 'Tipe Les', 'Status', 'Tgl Konfirmasi'];

        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . $startRow;
            $sheet->setCellValue($cell, $header);
        }
        $sheet->getStyle('A' . $startRow . ':H' . $startRow)->applyFromArray($subHeaderStyle);
        $sheet->getRowDimension($startRow)->setRowHeight(20);

        $row = $startRow + 1;
        foreach ($spps as $i => $spp) {
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $spp->murid->nama_murid ?? '-');
            $sheet->setCellValue('C' . $row, $spp->programKursus->nama_program ?? '-');
            $sheet->setCellValue('D' . $row, Carbon::parse($spp->periode_tagihan)->translatedFormat('F Y'));
            $sheet->setCellValue('E' . $row, (float) $spp->nominal_tagihan);
            $sheet->setCellValue('F' . $row, $spp->tipe_les ?? '-');
            $sheet->setCellValue('G' . $row, $spp->status_bayar);
            $sheet->setCellValue('H' . $row, $spp->transaksi?->tanggal_konfirmasi
                ? Carbon::parse($spp->transaksi->tanggal_konfirmasi)->format('d/m/Y')
                : '-');

            // Warna baris berdasarkan status
            if ($spp->status_bayar === 'Lunas') {
                $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF0FDF4']],
                ]);
            } else {
                $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFF7ED']],
                ]);
            }

            $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray($dataStyle);
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('A' . $row)->applyFromArray(['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);
            $row++;
        }

        // Total row
        $sheet->setCellValue('D' . $row, 'TOTAL');
        $sheet->setCellValue('E' . $row, (float) $totalTagihan);
        $sheet->getStyle('D' . $row . ':E' . $row)->applyFromArray([
            'font'      => ['bold' => true],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFDBEAFE']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFD1D5DB']]],
        ]);
        $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');

        // ── Column widths ────────────────────────────────────────
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(28);
        $sheet->getColumnDimension('C')->setWidth(22);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(14);
        $sheet->getColumnDimension('G')->setWidth(14);
        $sheet->getColumnDimension('H')->setWidth(18);

        return $this->streamXlsx($spreadsheet, 'laporan-keuangan-' . $startDate . '-sd-' . $endDate);
    }

    private function xlsxGaji(string $startDate, string $endDate, string $bulan)
    {
        $honors = HonorGuru::with(['guru', 'jadwals.spp.murid'])
            ->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ])
            ->latest()
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Gaji Guru');

        $subHeaderStyle = [
            'font'      => ['bold' => true, 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFEFF6FF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFD1D5DB']]],
        ];

        // ── Header ───────────────────────────────────────────────
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'REKAP GAJI GURU — CROMA MUSIC');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells('A2:G2');
        $label = 'Periode: ' . Carbon::parse($startDate)->format('d/m/Y') . ' s.d. ' . Carbon::parse($endDate)->format('d/m/Y');
        $sheet->setCellValue('A2', $label);
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['size' => 10, 'italic' => true, 'color' => ['argb' => 'FF6B7280']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells('A3:G3');
        $sheet->setCellValue('A3', 'Dibuat: ' . now()->translatedFormat('d F Y, H:i') . ' WIB');
        $sheet->getStyle('A3')->applyFromArray([
            'font'      => ['size' => 9, 'color' => ['argb' => 'FF9CA3AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // ── Ringkasan per guru ───────────────────────────────────
        $ringkasanGuru = $honors->groupBy('id_guru')->map(function ($items) {
            return [
                'guru'            => $items->first()->guru,
                'total_pertemuan' => $items->sum('jumlah_pertemuan'),
                'total_honor'     => $items->sum('jumlah_honor'),
                'total_lunas'     => $items->where('status_bayar', 'Lunas')->sum('jumlah_honor'),
                'total_pending'   => $items->whereIn('status_bayar', ['Belum Lunas', 'Siap Dibayar'])->sum('jumlah_honor'),
            ];
        })->values();

        $sheet->setCellValue('A5', 'RINGKASAN PER GURU');
        $sheet->getStyle('A5')->applyFromArray(['font' => ['bold' => true, 'size' => 11]]);

        $sumHeaders = ['Nama Guru', 'Total Pertemuan', 'Total Honor (Rp)', 'Sudah Cair (Rp)', 'Belum Cair (Rp)'];
        foreach ($sumHeaders as $ci => $h) {
            $sheet->setCellValue(chr(65 + $ci) . '6', $h);
        }
        $sheet->getStyle('A6:E6')->applyFromArray($subHeaderStyle);

        $row = 7;
        foreach ($ringkasanGuru as $r) {
            $sheet->setCellValue('A' . $row, $r['guru']->nama_guru ?? '-');
            $sheet->setCellValue('B' . $row, $r['total_pertemuan']);
            $sheet->setCellValue('C' . $row, (float) $r['total_honor']);
            $sheet->setCellValue('D' . $row, (float) $r['total_lunas']);
            $sheet->setCellValue('E' . $row, (float) $r['total_pending']);
            $sheet->getStyle('C' . $row . ':E' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE5E7EB']]],
            ]);
            $row++;
        }

        // Total
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->setCellValue('C' . $row, (float) $honors->sum('jumlah_honor'));
        $sheet->setCellValue('D' . $row, (float) $honors->where('status_bayar', 'Lunas')->sum('jumlah_honor'));
        $sheet->setCellValue('E' . $row, (float) $honors->whereIn('status_bayar', ['Belum Lunas', 'Siap Dibayar'])->sum('jumlah_honor'));
        $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
            'font'      => ['bold' => true],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFDBEAFE']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFD1D5DB']]],
        ]);
        $sheet->getStyle('C' . $row . ':E' . $row)->getNumberFormat()->setFormatCode('#,##0');

        // ── Detail semua record honor ────────────────────────────
        $detailStart = $row + 3;
        $sheet->setCellValue('A' . $detailStart, 'DETAIL RECORD HONOR');
        $sheet->getStyle('A' . $detailStart)->applyFromArray(['font' => ['bold' => true, 'size' => 11]]);

        $detailHeaders = ['#', 'ID Honor', 'Nama Guru', 'Murid', 'Sesi', 'Nominal (Rp)', 'Status', 'Tgl Pencairan'];
        foreach ($detailHeaders as $ci => $h) {
            $sheet->setCellValue(chr(65 + $ci) . ($detailStart + 1), $h);
        }
        $sheet->getStyle('A' . ($detailStart + 1) . ':H' . ($detailStart + 1))->applyFromArray($subHeaderStyle);

        $row = $detailStart + 2;
        foreach ($honors as $i => $honor) {
            $namaMurid = $honor->jadwals->first()?->spp?->murid?->nama_murid ?? 'N/A';

            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, 'HG-' . str_pad($honor->id_honor, 4, '0', STR_PAD_LEFT));
            $sheet->setCellValue('C' . $row, $honor->guru->nama_guru ?? '-');
            $sheet->setCellValue('D' . $row, $namaMurid);
            $sheet->setCellValue('E' . $row, $honor->jumlah_pertemuan);
            $sheet->setCellValue('F' . $row, (float) $honor->jumlah_honor);
            $sheet->setCellValue('G' . $row, $honor->status_bayar);
            $sheet->setCellValue('H' . $row, $honor->tanggal_pencairan
                ? Carbon::parse($honor->tanggal_pencairan)->format('d/m/Y')
                : '-');

            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE5E7EB']]],
            ]);

            if ($honor->status_bayar === 'Lunas') {
                $sheet->getStyle('G' . $row)->applyFromArray([
                    'font' => ['color' => ['argb' => 'FF16A34A'], 'bold' => true],
                ]);
            } elseif ($honor->status_bayar === 'Siap Dibayar') {
                $sheet->getStyle('G' . $row)->applyFromArray([
                    'font' => ['color' => ['argb' => 'FF2563EB'], 'bold' => true],
                ]);
            }

            $row++;
        }

        // ── Column widths ────────────────────────────────────────
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(26);
        $sheet->getColumnDimension('D')->setWidth(26);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(16);
        $sheet->getColumnDimension('H')->setWidth(16);

        return $this->streamXlsx($spreadsheet, 'rekap-gaji-guru-' . $startDate . '-sd-' . $endDate);
    }

    private function xlsxAbsensi(string $bulan)
    {
        [$tahun, $bln] = explode('-', $bulan);

        $murids = Murid::where('status_aktif', true)
            ->with(['spps.jadwals' => function ($q) use ($tahun, $bln) {
                $q->where('is_active', true)
                  ->whereYear('tanggal', $tahun)
                  ->whereMonth('tanggal', $bln);
            }])->get()->map(function ($murid) {
            $jadwals = collect();
            if ($murid->spps) {
                foreach ($murid->spps as $spp) {
                    if ($spp->jadwals) {
                        $jadwals = $jadwals->merge($spp->jadwals);
                    }
                }
            }

            $murid->total_sesi   = $jadwals->count();
            $murid->total_hadir  = $jadwals->where('status_kehadiran_murid', 'Hadir')->count();
            $murid->total_absen  = $jadwals->where('status_kehadiran_murid', 'Tidak Hadir')->count();
            $murid->belum_diisi  = $jadwals->whereNull('status_kehadiran_murid')->count();
            $murid->persen_hadir = $murid->total_sesi > 0
                ? round(($murid->total_hadir / $murid->total_sesi) * 100, 1) : 0;
            return $murid;
        });

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Absensi');

        $subHeaderStyle = [
            'font'      => ['bold' => true, 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFEFF6FF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFD1D5DB']]],
        ];

        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'REKAP ABSENSI MURID — CROMA MUSIC');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells('A2:G2');
        $sheet->setCellValue('A2', 'Periode: ' . Carbon::parse($bulan . '-01')->translatedFormat('F Y'));
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['size' => 10, 'italic' => true, 'color' => ['argb' => 'FF6B7280']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $headers = ['#', 'Nama Murid', 'Total Sesi', 'Hadir', 'Tidak Hadir', 'Belum Diisi', '% Kehadiran'];
        foreach ($headers as $ci => $h) {
            $sheet->setCellValue(chr(65 + $ci) . '4', $h);
        }
        $sheet->getStyle('A4:G4')->applyFromArray($subHeaderStyle);
        $sheet->getRowDimension(4)->setRowHeight(20);

        $row = 5;
        foreach ($murids as $i => $murid) {
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $murid->nama_murid);
            $sheet->setCellValue('C' . $row, $murid->total_sesi);
            $sheet->setCellValue('D' . $row, $murid->total_hadir);
            $sheet->setCellValue('E' . $row, $murid->total_absen);
            $sheet->setCellValue('F' . $row, $murid->belum_diisi);
            $sheet->setCellValue('G' . $row, $murid->persen_hadir . '%');

            $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE5E7EB']]],
            ]);

            // Warna % kehadiran
            if ($murid->persen_hadir >= 75) {
                $sheet->getStyle('G' . $row)->applyFromArray([
                    'font' => ['color' => ['argb' => 'FF16A34A'], 'bold' => true],
                ]);
            } elseif ($murid->persen_hadir >= 50) {
                $sheet->getStyle('G' . $row)->applyFromArray([
                    'font' => ['color' => ['argb' => 'FFD97706'], 'bold' => true],
                ]);
            } else {
                $sheet->getStyle('G' . $row)->applyFromArray([
                    'font' => ['color' => ['argb' => 'FFDC2626'], 'bold' => true],
                ]);
            }

            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(14);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(14);
        $sheet->getColumnDimension('F')->setWidth(14);
        $sheet->getColumnDimension('G')->setWidth(14);

        return $this->streamXlsx($spreadsheet, 'rekap-absensi-' . $bulan);
    }

    /**
     * Stream file XLSX ke browser sebagai download.
     */
    private function streamXlsx(Spreadsheet $spreadsheet, string $namaFile)
    {
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(
            function () use ($writer) {
                $writer->save('php://output');
            },
            $namaFile . '.xlsx',
            [
                'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control'       => 'max-age=0',
                'Content-Disposition' => 'attachment; filename="' . $namaFile . '.xlsx"',
            ]
        );
    }
}