<?php

namespace App\Http\Controllers\Murid;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Murid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    /**
     * FR-15: Murid mengajukan kehadiran untuk satu sesi jadwal.
     * Mengupdate kolom status_kehadiran_murid, waktu_presensi_diisi,
     * dan presensi_diisi_oleh langsung pada tabel jadwals.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_jadwal' => 'required|exists:jadwals,id_jadwal',
        ]);

        $murid = Murid::where('id_user', Auth::id())->firstOrFail();

        // Pastikan jadwal ini memang milik murid (lewat relasi SPP)
        $jadwal = Jadwal::where('id_jadwal', $request->id_jadwal)
            ->whereHas('spp', fn ($q) => $q->where('id_murid', $murid->id_murid))
            ->firstOrFail();

        // Tolak jika sudah diisi
        if ($jadwal->status_kehadiran_murid !== null) {
            return back()->with('error', 'Kehadiran untuk sesi ini sudah pernah diajukan.');
        }

        // Tolak jika jadwal bukan hari ini atau masa lalu
        if ($jadwal->tanggal->isFuture()) {
            return back()->with('error', 'Belum bisa mengajukan kehadiran untuk jadwal yang akan datang.');
        }

        $jadwal->update([
            'status_kehadiran_murid' => 'Hadir',
            'waktu_presensi_diisi' => now(),
            'presensi_diisi_oleh' => 'Murid',
        ]);

        return back()->with('success', 'Kehadiran berhasil diajukan! Menunggu konfirmasi guru.');
    }
}
