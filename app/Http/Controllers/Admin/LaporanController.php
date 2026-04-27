<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Spp;
use App\Models\Presensi;
use App\Models\Guru;
use App\Models\MonthlyReport;
use App\Models\Murid;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    // Laporan keuangan bulanan
    public function keuangan(Request $request)
    {
        $bulan = $request->bulan ?? now()->format('Y-m');

        $spps         = Spp::with('murid')->where('bulan_tagihan', $bulan)->get();
        $totalMasuk   = $spps->where('status_bayar', 'sudah_bayar')->sum('nominal_tagihan');
        $totalTagihan = $spps->sum('nominal_tagihan');
        $belumBayar   = $spps->where('status_bayar', 'belum_bayar');

        return view('admin.laporan.keuangan', compact('spps', 'totalMasuk', 'totalTagihan', 'belumBayar', 'bulan'));
    }

    // Laporan gaji guru berdasarkan jumlah sesi
    public function gajiGuru(Request $request)
    {
        $bulan       = $request->bulan ?? now()->format('Y-m');
        $gajiPerSesi = $request->gaji_per_sesi ?? 100000; // default, bisa dikonfigurasi

        $gurus = Guru::with(['presensis' => function ($q) use ($bulan) {
            $q->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan]);
        }])->get()->map(function ($guru) use ($gajiPerSesi) {
            $jumlahSesi  = $guru->presensis->count();
            $guru->total_gaji = $jumlahSesi * $gajiPerSesi;
            $guru->jumlah_sesi = $jumlahSesi;
            return $guru;
        });

        return view('admin.laporan.gaji', compact('gurus', 'bulan', 'gajiPerSesi'));
    }

    // Rekap absensi bulanan semua murid
    public function absensi(Request $request)
    {
        $bulan  = $request->bulan ?? now()->format('Y-m');
        $murids = Murid::where('status_aktif', true)->get()->map(function ($murid) use ($bulan) {
            $presensis              = $murid->presensis()->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])->get();
            $murid->total_hadir     = $presensis->where('status_murid', 'hadir')->count();
            $murid->total_alpa      = $presensis->where('status_murid', 'alpa')->count();
            $murid->total_izin      = $presensis->where('status_murid', 'izin')->count();
            $murid->persen_hadir    = $presensis->count() > 0
                ? round(($murid->total_hadir / $presensis->count()) * 100, 1) : 0;
            return $murid;
        });

        return view('admin.laporan.absensi', compact('murids', 'bulan'));
    }

    // Export PDF laporan
    public function exportPdf(Request $request, string $jenis)
    {
        $bulan = $request->bulan ?? now()->format('Y-m');
        $data  = match($jenis) {
            'keuangan' => Spp::with('murid')->where('bulan_tagihan', $bulan)->get(),
            'absensi'  => Murid::where('status_aktif', true)->get()->map(function ($murid) use ($bulan) {
                $presensis = $murid->presensis()->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])->get();
                $murid->total_hadir = $presensis->where('status_murid', 'hadir')->count();
                $murid->total_alpa  = $presensis->where('status_murid', 'alpa')->count();
                $murid->total_izin  = $presensis->where('status_murid', 'izin')->count();
                return $murid;
            }),
            default    => collect(),
        };

        $pdf = Pdf::loadView("admin.laporan.pdf.{$jenis}", compact('data', 'bulan'));
        return $pdf->download("laporan_{$jenis}_{$bulan}.pdf");
    }
}