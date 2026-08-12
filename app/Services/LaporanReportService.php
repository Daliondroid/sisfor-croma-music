<?php

namespace App\Services;

use App\Models\HonorGuru;
use App\Models\Murid;
use App\Models\Spp;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanReportService
{
    /**
     * Resolve date range bounds from request inputs or default month.
     */
    public function resolveDateRange(?string $bulanInput, ?string $startDateInput, ?string $endDateInput): array
    {
        $bulan = $bulanInput ?? now()->format('Y-m');
        $startDate = $startDateInput ?? Carbon::parse($bulan.'-01')->startOfMonth()->format('Y-m-d');
        $endDate = $endDateInput ?? Carbon::parse($bulan.'-01')->endOfMonth()->format('Y-m-d');

        return [$bulan, $startDate, $endDate];
    }

    /**
     * Get financial (Keuangan) report dataset.
     */
    public function getKeuanganData(string $startDate, string $endDate): array
    {
        $spps = Spp::with(['murid', 'programKursus', 'transaksi'])
            ->whereBetween('periode_tagihan', [
                Carbon::parse($startDate)->startOfMonth()->format('Y-m-d'),
                Carbon::parse($endDate)->endOfMonth()->format('Y-m-d'),
            ])
            ->latest('periode_tagihan')
            ->get();

        $totalMasuk = $spps->where('status_bayar', 'Lunas')->sum('nominal_tagihan');
        $totalTagihan = $spps->sum('nominal_tagihan');
        $totalTunggakan = $totalTagihan - $totalMasuk;

        return compact('spps', 'totalMasuk', 'totalTagihan', 'totalTunggakan');
    }

    /**
     * Get attendance (Absensi) summary using database subquery aggregation (N+1 free).
     */
    public function getAbsensiData(string $bulan): array
    {
        [$tahun, $bln] = explode('-', $bulan);

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
                    .' THEN ROUND(COALESCE(att.total_hadir, 0) / att.total_sesi * 100, 1)'
                    .' ELSE 0 END as persen_hadir'
                )
            )
            ->get();

        return compact('murids');
    }

    /**
     * Get teacher salary (Gaji Guru) summary dataset.
     */
    public function getGajiData(string $startDate, string $endDate): array
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
                'guru' => $items->first()->guru,
                'total_honor' => $items->sum('jumlah_honor'),
                'total_pertemuan' => $items->sum('jumlah_pertemuan'),
                'total_lunas' => $items->where('status_bayar', 'Lunas')->sum('jumlah_honor'),
                'total_pending' => $items->whereIn('status_bayar', ['Belum Lunas', 'Siap Dibayar'])->sum('jumlah_honor'),
                'records' => $items,
            ];
        })->values();

        return compact('honors', 'ringkasanGuru');
    }
}
