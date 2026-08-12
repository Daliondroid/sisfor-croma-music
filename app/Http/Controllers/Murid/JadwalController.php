<?php

namespace App\Http\Controllers\Murid;

use App\Http\Controllers\Controller;
use App\Models\Murid;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $murid = Murid::where('id_user', Auth::id())->firstOrFail();

        $availableMonths = $murid->jadwals()
            ->where('is_active', true)
            ->pluck('tanggal')
            ->map(fn ($t) => Carbon::parse($t)->format('Y-m'))
            ->unique()
            ->sortDesc()
            ->values();

        $selectedMonth = $request->get('bulan', now()->format('Y-m'));

        if ($availableMonths->isNotEmpty() && ! $availableMonths->contains($selectedMonth)) {
            $selectedMonth = $availableMonths->first();
        }

        [$year, $month] = explode('-', $selectedMonth);

        // Jadwal bulan terpilih — eager load progres murid
        $jadwals = $murid->jadwals()
            ->where('is_active', true)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->with(['guru', 'spp.programKursus', 'progresMurid'])
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->get();

        // Statistik kehadiran bulan ini
        $totalSesi = $jadwals->count();
        $totalHadir = $jadwals->where('status_kehadiran_murid', 'Hadir')->count();
        $pctKehadiran = $totalSesi > 0 ? round(($totalHadir / $totalSesi) * 100) : 0;

        // Filter
        $filter = $request->get('filter', 'semua');
        $jadwalsFiltered = match ($filter) {
            'belum' => $jadwals->filter(fn ($j) => $j->status_kehadiran_murid === null && ! $j->tanggal->isFuture()),
            'hadir' => $jadwals->filter(fn ($j) => $j->status_kehadiran_murid === 'Hadir'),
            default => $jadwals,
        };

        return view('murid.jadwal.index', compact(
            'murid', 'jadwals', 'jadwalsFiltered',
            'availableMonths', 'selectedMonth', 'filter',
            'totalSesi', 'totalHadir', 'pctKehadiran'
        ));
    }
}
