<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\MonthlyReport;
use App\Models\Murid;
use App\Models\Spp;
use Illuminate\Http\Request;

class MonthlyReportController extends Controller
{
    /**
     * Daftar semua monthly report dengan filter bulan.
     */
    public function index(Request $request)
    {
        $bulan = $request->bulan ?? now()->format('Y-m');
        $tahun = substr($bulan, 0, 4);
        $bln = substr($bulan, 5, 2);

        $murids = Murid::where('status_aktif', true)
            ->with([
                'user',
                'monthlyReports' => fn ($q) => $q
                    ->whereYear('periode_bulan', $tahun)
                    ->whereMonth('periode_bulan', $bln),
            ])
            ->get();

        $sppsGrouped = Spp::whereIn('id_murid', $murids->pluck('id_murid'))
            ->get()
            ->groupBy('id_murid');

        $allSppIds = $sppsGrouped->flatten()->pluck('id_spp');
        $jadwalsGrouped = Jadwal::where('is_active', true)
            ->whereIn('id_spp', $allSppIds)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bln)
            ->get()
            ->groupBy('id_spp');

        $murids->transform(function (Murid $murid) use ($sppsGrouped, $jadwalsGrouped) {
            $userSpps = $sppsGrouped->get($murid->id_murid, collect());
            $jadwals = collect();
            foreach ($userSpps as $spp) {
                $jadwals = $jadwals->merge($jadwalsGrouped->get($spp->id_spp, collect()));
            }

            $stats = Jadwal::calculateAttendanceStats($jadwals);
            $murid->total_sesi = $stats['total_sesi'];
            $murid->total_hadir = $stats['hadir'];
            $murid->total_absen = $stats['tidak_hadir'];
            $murid->persen_hadir = $stats['persen_hadir'];
            $murid->report = $murid->monthlyReports->first();

            return $murid;
        });

        return view('admin.monthly_report.index', compact('murids', 'bulan'));
    }

    /**
     * Generate monthly report untuk semua murid aktif pada bulan tertentu.
     */
    public function generate(Request $request)
    {
        $request->validate(['bulan' => 'required|date_format:Y-m']);

        $tahun = substr($request->bulan, 0, 4);
        $bln = substr($request->bulan, 5, 2);
        $murids = Murid::where('status_aktif', true)->get();

        $spps = Spp::whereIn('id_murid', $murids->pluck('id_murid'))
            ->whereYear('periode_tagihan', $tahun)
            ->whereMonth('periode_tagihan', $bln)
            ->get()
            ->keyBy('id_murid');

        $jadwalsGrouped = Jadwal::where('is_active', true)
            ->whereIn('id_spp', $spps->pluck('id_spp'))
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bln)
            ->get()
            ->groupBy('id_spp');

        $count = 0;

        foreach ($murids as $murid) {
            $spp = $spps->get($murid->id_murid);
            if (! $spp) {
                continue;
            }

            $jadwals = $jadwalsGrouped->get($spp->id_spp, collect());
            $stats = Jadwal::calculateAttendanceStats($jadwals);
            $skor = MonthlyReport::calculateScore($stats['persen_hadir']);

            MonthlyReport::updateOrCreate(
                [
                    'id_spp' => $spp->id_spp,
                    'periode_bulan' => $request->bulan.'-01',
                ],
                [
                    'skor' => $skor,
                    'evaluasi_bulanan' => "Kehadiran {$stats['persen_hadir']}% ({$stats['hadir']}/{$stats['total_sesi']} sesi).",
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
            ->when($spp, fn ($q) => $q->where('id_spp', $spp->id_spp))
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
            'skor' => 'required|in:A+,A,A-,B+,B,B-,C+,C,C-',
            'evaluasi_bulanan' => 'required|string|max:2000',
            'url_video' => 'nullable|url',
        ]);

        $monthlyReport->update($request->only(['skor', 'evaluasi_bulanan', 'url_video']));

        return back()->with('success', 'Monthly report berhasil diperbarui.');
    }
}
