<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonthlyReport;
use App\Models\Murid;
use Illuminate\Http\Request;

class MonthlyReportController extends Controller
{
    // Generate semua monthly report untuk bulan tertentu
    public function generate(Request $request)
    {
        $request->validate(['bulan' => 'required|date_format:Y-m']);

        $murids = Murid::where('status_aktif', true)->get();
        foreach ($murids as $murid) {
            MonthlyReport::generateUntukMurid($murid->id_murid, $request->bulan);
        }

        return back()->with('success', "Monthly report bulan {$request->bulan} berhasil digenerate.");
    }

    // Tampilkan monthly report satu murid
    public function show(Murid $murid, string $bulan)
    {
        $report = MonthlyReport::where('id_murid', $murid->id_murid)
            ->where('bulan', $bulan)
            ->firstOrFail();

        $presensis = $murid->presensis()
            ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])
            ->with(['materiKbm', 'videoProgres', 'guru'])
            ->get();

        return view('admin.monthly_report.show', compact('murid', 'report', 'presensis', 'bulan'));
    }
}
