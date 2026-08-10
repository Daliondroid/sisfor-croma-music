<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanExcelService
{
    private array $subHeaderStyle = [
        'font'      => ['bold' => true, 'size' => 10],
        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFEFF6FF']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFD1D5DB']]],
    ];

    private array $dataStyle = [
        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE5E7EB']]],
        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
    ];

    /**
     * Build financial report XLSX response.
     */
    public function generateKeuanganXlsx(array $data, string $startDate, string $endDate): StreamedResponse
    {
        $spps           = $data['spps'];
        $totalTagihan   = $data['totalTagihan'];
        $totalMasuk     = $data['totalMasuk'];
        $totalTunggakan = $data['totalTunggakan'];

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Keuangan');

        // Header
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

        // Ringkasan
        $pct = $totalTagihan > 0 ? round(($totalMasuk / $totalTagihan) * 100) : 0;
        $sheet->setCellValue('A5', 'RINGKASAN');
        $sheet->getStyle('A5')->applyFromArray(['font' => ['bold' => true, 'size' => 11]]);

        $summaryData = [
            ['Total Tagihan',     'Rp ' . number_format($totalTagihan, 0, ',', '.')],
            ['Total Masuk (Lunas)', 'Rp ' . number_format($totalMasuk, 0, ',', '.')],
            ['Total Tunggakan',   'Rp ' . number_format($totalTunggakan, 0, ',', '.')],
            ['Tingkat Pembayaran', $pct . '%'],
            ['Jumlah Tagihan',    $spps->count() . ' tagihan'],
        ];

        foreach ($summaryData as $i => [$lbl, $val]) {
            $row = 6 + $i;
            $sheet->setCellValue('A' . $row, $lbl);
            $sheet->setCellValue('B' . $row, $val);
            $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE5E7EB']]],
            ]);
        }
        $sheet->getStyle('B6:B10')->applyFromArray(['font' => ['bold' => true]]);

        // Detail Headers
        $startRow = 13;
        $headers  = ['#', 'Nama Murid', 'Program Kursus', 'Periode', 'Nominal (Rp)', 'Tipe Les', 'Status', 'Tgl Konfirmasi'];
        foreach ($headers as $col => $header) {
            $sheet->setCellValue(chr(65 + $col) . $startRow, $header);
        }
        $sheet->getStyle('A' . $startRow . ':H' . $startRow)->applyFromArray($this->subHeaderStyle);
        $sheet->getRowDimension($startRow)->setRowHeight(20);

        // Detail Rows
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

            $color = $spp->status_bayar === 'Lunas' ? 'FFF0FDF4' : 'FFFFF7ED';
            $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $color]],
            ]);

            $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray($this->dataStyle);
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('A' . $row)->applyFromArray(['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);
            $row++;
        }

        // Total
        $sheet->setCellValue('D' . $row, 'TOTAL');
        $sheet->setCellValue('E' . $row, (float) $totalTagihan);
        $sheet->getStyle('D' . $row . ':E' . $row)->applyFromArray([
            'font'    => ['bold' => true],
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFDBEAFE']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFD1D5DB']]],
        ]);
        $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');

        $widths = ['A' => 5, 'B' => 28, 'C' => 22, 'D' => 18, 'E' => 20, 'F' => 14, 'G' => 14, 'H' => 18];
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        return $this->streamXlsx($spreadsheet, 'laporan-keuangan-' . $startDate . '-sd-' . $endDate);
    }

    /**
     * Build teacher salary (Gaji) report XLSX response.
     */
    public function generateGajiXlsx(array $data, string $startDate, string $endDate): StreamedResponse
    {
        $honors        = $data['honors'];
        $ringkasanGuru = $data['ringkasanGuru'];

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Gaji Guru');

        // Header
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

        // Ringkasan per guru
        $sheet->setCellValue('A5', 'RINGKASAN PER GURU');
        $sheet->getStyle('A5')->applyFromArray(['font' => ['bold' => true, 'size' => 11]]);

        $sumHeaders = ['Nama Guru', 'Total Pertemuan', 'Total Honor (Rp)', 'Sudah Cair (Rp)', 'Belum Cair (Rp)'];
        foreach ($sumHeaders as $ci => $h) {
            $sheet->setCellValue(chr(65 + $ci) . '6', $h);
        }
        $sheet->getStyle('A6:E6')->applyFromArray($this->subHeaderStyle);

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
            'font'    => ['bold' => true],
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFDBEAFE']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFD1D5DB']]],
        ]);
        $sheet->getStyle('C' . $row . ':E' . $row)->getNumberFormat()->setFormatCode('#,##0');

        // Detail records
        $detailStart = $row + 3;
        $sheet->setCellValue('A' . $detailStart, 'DETAIL RECORD HONOR');
        $sheet->getStyle('A' . $detailStart)->applyFromArray(['font' => ['bold' => true, 'size' => 11]]);

        $detailHeaders = ['#', 'ID Honor', 'Nama Guru', 'Murid', 'Sesi', 'Nominal (Rp)', 'Status', 'Tgl Pencairan'];
        foreach ($detailHeaders as $ci => $h) {
            $sheet->setCellValue(chr(65 + $ci) . ($detailStart + 1), $h);
        }
        $sheet->getStyle('A' . ($detailStart + 1) . ':H' . ($detailStart + 1))->applyFromArray($this->subHeaderStyle);

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

        $widths = ['A' => 5, 'B' => 12, 'C' => 26, 'D' => 26, 'E' => 10, 'F' => 20, 'G' => 16, 'H' => 16];
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        return $this->streamXlsx($spreadsheet, 'rekap-gaji-guru-' . $startDate . '-sd-' . $endDate);
    }

    /**
     * Build attendance (Absensi) report XLSX response.
     */
    public function generateAbsensiXlsx(array $data, string $bulan): StreamedResponse
    {
        $murids = $data['murids'];

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Absensi');

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
        $sheet->getStyle('A4:G4')->applyFromArray($this->subHeaderStyle);
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

        $widths = ['A' => 5, 'B' => 30, 'C' => 14, 'D' => 10, 'E' => 14, 'F' => 14, 'G' => 14];
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        return $this->streamXlsx($spreadsheet, 'rekap-absensi-' . $bulan);
    }

    /**
     * Stream Spreadsheet object as XLSX download response.
     */
    private function streamXlsx(Spreadsheet $spreadsheet, string $namaFile): StreamedResponse
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
