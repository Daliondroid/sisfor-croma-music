<?php

namespace App\Http\Controllers\Murid;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Murid;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'id_jadwal' => 'required|exists:jadwals,id_jadwal',
            'sesi_ke'   => 'required|integer|min:1',
        ]);

        $murid = Murid::where('id_user', Auth::id())->firstOrFail();

        // Cek apakah jadwal memang milik murid ini
        $jadwal = Jadwal::where('id_jadwal', $request->id_jadwal)
            ->where('id_murid', $murid->id_murid)
            ->firstOrFail();

        // Cek belum pernah presensi di sesi yang sama
        $sudahAda = Presensi::where('id_jadwal', $jadwal->id_jadwal)
            ->where('id_murid', $murid->id_murid)
            ->where('tanggal', today())
            ->where('sesi_ke', $request->sesi_ke)
            ->exists();

        if ($sudahAda) {
            return back()->with('error', 'Presensi untuk sesi ini sudah diinput.');
        }

        Presensi::create([
            'id_jadwal'    => $jadwal->id_jadwal,
            'id_guru'      => $jadwal->id_guru,
            'id_murid'     => $murid->id_murid,
            'tanggal'      => today(),
            'sesi_ke'      => $request->sesi_ke,
            'status_murid' => 'hadir',
            'input_by'     => Auth::id(),
        ]);

        return back()->with('success', 'Kehadiran berhasil dicatat.');
    }
}
