<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonthlyReport;
use App\Models\Murid;
use App\Models\Spp;
use App\Models\Jadwal;
use Illuminate\Http\Request;

class MonthlyReportController extends Controller
{
    /**
     * Daftar semua monthly report dengan filter bulan.
     */
    public function index(Request $request)
    {
        $bulan = $request->bulan ?? now()->format('Y-m');

        // Ambil semua murid aktif beserta report-nya untuk bulan tsb
        $murids = Murid::where('status_aktif', true)
            ->with([
                'user',
                'monthlyReports' => fn($q) => $q
                    ->whereYear('periode_bulan', substr($bulan, 0, 4))
                    ->whereMonth('periode_bulan', substr($bulan, 5, 2)),
            ])
            ->get()
            ->map(function (Murid $murid) use ($bulan) {
                // Rekap absensi dari jadwal bulan tsb (kehadiran murid)
                $jadwals = Jadwal::where('is_active', true)
                    ->whereHas('spp', fn($q) => $q->where('id_murid', $murid->id_murid))
                    ->whereYear('tanggal', substr($bulan, 0, 4))
                    ->whereMonth('tanggal', substr($bulan, 5, 2))
                    ->get();

                $totalSesi  = $jadwals->count();
                $totalHadir = $jadwals->where('status_kehadiran_murid', 'Hadir')->count();
                $totalAbsen = $jadwals->where('status_kehadiran_murid', 'Tidak Hadir')->count();
                $persen     = $totalSesi > 0 ? round(($totalHadir / $totalSesi) * 100) : 0;

                $murid->total_sesi   = $totalSesi;
                $murid->total_hadir  = $totalHadir;
                $murid->total_absen  = $totalAbsen;
                $murid->persen_hadir = $persen;
                $murid->report       = $murid->monthlyReports->first();

                return $murid;
            });

        return view('admin.monthly_report.index', compact('murids', 'bulan'));
    }

    /**
     * Generate monthly report untuk satu murid pada bulan tertentu.
     * Jika sudah ada → update; belum ada → insert.
     */
    public function generate(Request $request)
    {
        $request->validate(['bulan' => 'required|date_format:Y-m']);

        $murids = Murid::where('status_aktif', true)->get();
        $count  = 0;

        foreach ($murids as $murid) {
            // Ambil SPP bulan ini milik murid
            $spp = Spp::where('id_murid', $murid->id_murid)
                ->whereYear('periode_tagihan', substr($request->bulan, 0, 4))
                ->whereMonth('periode_tagihan', substr($request->bulan, 5, 2))
                ->first();

            if (! $spp) {
                continue; // Murid tidak memiliki SPP bulan ini
            }

            // Rekap absensi jadwal
            $jadwals    = Jadwal::where('is_active', true)
                ->where('id_spp', $spp->id_spp)
                ->whereYear('tanggal', substr($request->bulan, 0, 4))
                ->whereMonth('tanggal', substr($request->bulan, 5, 2))
                ->get();

            $totalSesi  = $jadwals->count();
            $totalHadir = $jadwals->where('status_kehadiran_murid', 'Hadir')->count();
            $persen     = $totalSesi > 0 ? round(($totalHadir / $totalSesi) * 100) : 0;

            // Tentukan skor otomatis berdasarkan persentase kehadiran
            $skor = match (true) {
                $persen >= 95 => 'A+',
                $persen >= 90 => 'A',
                $persen >= 85 => 'A-',
                $persen >= 80 => 'B+',
                $persen >= 75 => 'B',
                $persen >= 70 => 'B-',
                $persen >= 65 => 'C+',
                $persen >= 60 => 'C',
                default       => 'C-',
            };

            MonthlyReport::updateOrCreate(
                [
                    'id_spp'       => $spp->id_spp,
                    'periode_bulan' => $request->bulan . '-01',
                ],
                [
                    'skor'              => $skor,
                    'evaluasi_bulanan'  => "Kehadiran {$persen}% ({$totalHadir}/{$totalSesi} sesi).",
                ]
            );

            $count++;
        }

        return back()->with('success', "Monthly report untuk {$count} murid berhasil di-generate ({$request->bulan}).");
    }

    /**
     * Detail monthly report satu murid + histori sesi.
     */
    public function show(Murid $murid, string $bulan)
    {
        $spp = Spp::where('id_murid', $murid->id_murid)
            ->whereYear('periode_tagihan', substr($bulan, 0, 4))
            ->whereMonth('periode_tagihan', substr($bulan, 5, 2))
            ->first();

        $report = $spp
            ? MonthlyReport::where('id_spp', $spp->id_spp)
                ->whereYear('periode_bulan', substr($bulan, 0, 4))
                ->whereMonth('periode_bulan', substr($bulan, 5, 2))
                ->firstOrFail()
            : null;

        // Jadwal + progres murid bulan ini
        $jadwals = Jadwal::with(['guru', 'progresMurid'])
            ->where('is_active', true)
            ->when($spp, fn($q) => $q->where('id_spp', $spp->id_spp))
            ->whereYear('tanggal', substr($bulan, 0, 4))
            ->whereMonth('tanggal', substr($bulan, 5, 2))
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get();

        return view('admin.monthly_report.show', compact('murid', 'report', 'jadwals', 'bulan', 'spp'));
    }

    /**
     * Update evaluasi / skor / URL video pada monthly report.
     */
    public function update(Request $request, MonthlyReport $monthlyReport)
    {
        $request->validate([
            'skor'             => 'required|in:A+,A,A-,B+,B,B-,C+,C,C-',
            'evaluasi_bulanan' => 'required|string|max:2000',
            'url_video'        => 'nullable|url',
        ]);

        $monthlyReport->update($request->only(['skor', 'evaluasi_bulanan', 'url_video']));

        return back()->with('success', 'Monthly report berhasil diperbarui.');
    }
}