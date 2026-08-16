<?php

namespace App\Http\Controllers\Murid;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Murid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        $result = DB::transaction(function () use ($request, $murid) {
            // Pastikan jadwal ini memang milik murid (lewat relasi SPP) with row lock
            $jadwal = Jadwal::where('id_jadwal', $request->id_jadwal)
                ->whereHas('spp', fn ($q) => $q->where('id_murid', $murid->id_murid))
                ->lockForUpdate()
                ->firstOrFail();

            // Tolak jika sudah diisi
            if ($jadwal->status_kehadiran_murid !== null) {
                return ['status' => 'error', 'message' => 'Kehadiran untuk sesi ini sudah pernah diajukan.'];
            }

            // Tolak jika jadwal bukan hari ini atau masa lalu
            if ($jadwal->tanggal->isFuture()) {
                return ['status' => 'error', 'message' => 'Belum bisa mengajukan kehadiran untuk jadwal yang akan datang.'];
            }

            $jadwal->update([
                'status_kehadiran_murid' => 'Hadir',
                'waktu_presensi_diisi' => now(),
                'presensi_diisi_oleh' => 'Murid',
            ]);

            return ['status' => 'success', 'message' => 'Kehadiran berhasil diajukan! Menunggu konfirmasi guru.'];
        });

        return back()->with($result['status'], $result['message']);
    }
}
