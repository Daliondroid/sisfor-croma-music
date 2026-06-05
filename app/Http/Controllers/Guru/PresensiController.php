<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Guru;
use App\Models\ProgresMurid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{
    /**
     * Daftar jadwal guru — bisa filter tanggal & murid.
     * FR-16: Guru membaca status kehadiran murid.
     * FR-18: Guru mencatat kehadiran mengajar.
     */
    public function index(Request $request)
    {
        $guru = Guru::where('id_user', Auth::id())->firstOrFail();

        $query = Jadwal::with(['spp.murid', 'spp.programKursus', 'progresMurid'])
            ->where('id_guru', $guru->id_guru)
            ->where('is_active', true);

        // Filter tanggal (default: hari ini)
        $tanggal = $request->tanggal ?? today()->format('Y-m-d');
        $query->whereDate('tanggal', $tanggal);

        $jadwals = $query->orderBy('jam_mulai')->get();

        // Jadwal yang dipilih untuk di-input presensi
        $selectedJadwal = null;
        if ($request->filled('jadwal')) {
            $selectedJadwal = Jadwal::with(['spp.murid', 'spp.programKursus', 'progresMurid'])
                ->where('id_jadwal', $request->jadwal)
                ->where('id_guru', $guru->id_guru)
                ->first();
        }

        return view('guru.presensi.index', compact('guru', 'jadwals', 'tanggal', 'selectedJadwal'));
    }

    /**
     * FR-17: Guru memverifikasi / mengisi kehadiran murid.
     * FR-18: Guru mencatat status kehadiran guru (Hadir/Tidak Hadir).
     *
     * Sistem memberlakukan state Immutable (menolak update dari Murid)
     * jika waktu_presensi_diisi bernilai NOT NULL.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_jadwal'                => 'required|exists:jadwals,id_jadwal',
            'status_kehadiran_murid'   => 'required|in:Hadir,Tidak Hadir',
            'status_kehadiran_guru'    => 'required|in:Hadir,Tidak Hadir',
        ]);

        $guru   = Guru::where('id_user', Auth::id())->firstOrFail();
        $jadwal = Jadwal::where('id_jadwal', $request->id_jadwal)
            ->where('id_guru', $guru->id_guru)
            ->firstOrFail();

        // Immutable setelah guru submit — jika sudah terisi, tolak
        if ($jadwal->waktu_presensi_diisi !== null) {
            return back()->with('error', 'Presensi jadwal ini sudah diisi dan tidak dapat diubah lagi.');
        }

        $jadwal->update([
            'status_kehadiran_murid'  => $request->status_kehadiran_murid,
            'status_kehadiran_guru'   => $request->status_kehadiran_guru,
            'waktu_presensi_diisi'    => now(),
            'presensi_diisi_oleh'     => 'Guru',
        ]);

        return redirect()
            ->route('guru.presensi.index', ['tanggal' => $jadwal->tanggal->format('Y-m-d')])
            ->with('success', 'Presensi berhasil dicatat.');
    }
}