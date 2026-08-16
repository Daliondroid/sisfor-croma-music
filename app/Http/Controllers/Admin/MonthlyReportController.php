<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\MonthlyReport;
use App\Models\Murid;
use App\Models\Spp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonthlyReportController extends Controller
{
    /**
     * Daftar semua monthly report dengan filter bulan.
     */
    public function index(Request $request)
    {
        $bulan = $request->bulan ?? now()->format('Y-m');
        $startDate = Carbon::parse($bulan.'-01')->startOfMonth()->toDateString();
        $endDate = Carbon::parse($bulan.'-01')->endOfMonth()->toDateString();

        $murids = Murid::where('status_aktif', true)
            ->with([
                'user',
                'monthlyReports' => fn ($q) => $q->whereBetween('periode_bulan', [$startDate, $endDate]),
            ])
            ->get();

        $sppsGrouped = Spp::whereIn('id_murid', $murids->pluck('id_murid'))
            ->whereBetween('periode_tagihan', [$startDate, $endDate])
            ->get()
            ->groupBy('id_murid');

        $allSppIds = $sppsGrouped->flatten()->pluck('id_spp');
        $jadwalsGrouped = Jadwal::where('is_active', true)
            ->whereIn('id_spp', $allSppIds)
            ->whereBetween('tanggal', [$startDate, $endDate])
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

        $startDate = Carbon::parse($request->bulan.'-01')->startOfMonth()->toDateString();
        $endDate = Carbon::parse($request->bulan.'-01')->endOfMonth()->toDateString();
        $murids = Murid::where('status_aktif', true)->get();

        $spps = Spp::whereIn('id_murid', $murids->pluck('id_murid'))
            ->whereBetween('periode_tagihan', [$startDate, $endDate])
            ->get()
            ->keyBy('id_murid');

        $jadwalsGrouped = Jadwal::where('is_active', true)
            ->whereIn('id_spp', $spps->pluck('id_spp'))
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->groupBy('id_spp');

        $count = DB::transaction(function () use ($murids, $spps, $jadwalsGrouped, $request) {
            $createdCount = 0;
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

                $createdCount++;
            }

            return $createdCount;
        });

        return back()->with('success', "Monthly report untuk {$count} murid berhasil di-generate ({$request->bulan}).");
    }

    /**
     * Detail monthly report satu murid + histori sesi.
     */
    public function show(Murid $murid, string $bulan)
    {
        $startDate = Carbon::parse($bulan.'-01')->startOfMonth()->toDateString();
        $endDate = Carbon::parse($bulan.'-01')->endOfMonth()->toDateString();

        $spp = Spp::with('programKursus')
            ->where('id_murid', $murid->id_murid)
            ->whereBetween('periode_tagihan', [$startDate, $endDate])
            ->first();

        $report = $spp
            ? MonthlyReport::where('id_spp', $spp->id_spp)
                ->whereBetween('periode_bulan', [$startDate, $endDate])
                ->first()
            : null;

        // Jadwal + progres murid bulan ini
        $jadwals = Jadwal::with(['guru', 'progresMurid', 'spp.programKursus'])
            ->where('is_active', true)
            ->whereHas('spp', fn ($q) => $q->where('id_murid', $murid->id_murid))
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get();

        $stats = Jadwal::calculateAttendanceStats($jadwals);

        return view('admin.monthly_report.show', compact('murid', 'report', 'jadwals', 'bulan', 'spp', 'stats'));
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
