<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Guru;
use App\Models\Spp;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    /**
     * Daftar semua jadwal aktif dengan filter.
     */
    public function index(Request $request)
    {
        $query = Jadwal::with(['guru', 'spp.murid'])
            ->where('is_active', true);

        // Filter opsional
        if ($request->filled('id_guru')) {
            $query->where('id_guru', $request->id_guru);
        }
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        if ($request->filled('status')) {
            $query->where('status_jadwal', $request->status);
        }

        $jadwals = $query->latest('tanggal')->paginate(20);

        $gurus = Guru::where('status_aktif', true)->get();

        return view('admin.jadwals.index', compact('jadwals', 'gurus'));
    }

    /**
     * Form buat jadwal baru.
     */
    public function create()
    {
        $gurus = Guru::where('status_aktif', true)->with('spesialisasis')->get();

        // Hanya SPP yang belum lunas / masih aktif yang bisa dijadwalkan
        $spps = Spp::with('murid')
            ->whereHas('murid', fn($q) => $q->where('status_aktif', true))
            ->latest()
            ->get();

        return view('admin.jadwals.create', compact('gurus', 'spps'));
    }

    /**
     * Simpan jadwal baru.
     * Sistem validasi time-clash: guru / murid tidak boleh dobel pada slot yang sama.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_guru'    => 'required|exists:gurus,id_guru',
            'id_spp'     => 'required|exists:spps,id_spp',
            'tanggal'    => 'required|date',
            'jam_mulai'  => 'required|date_format:H:i',
            'jam_selesai'=> 'required|date_format:H:i|after:jam_mulai',
            'sesi_ke'    => 'required|integer|min:1',
        ]);

        $spp = Spp::with('murid')->findOrFail($request->id_spp);

        // ── Cek time-clash guru ────────────────────────────────────
        $clashGuru = Jadwal::where('id_guru', $request->id_guru)
            ->whereDate('tanggal', $request->tanggal)
            ->where('is_active', true)
            ->where(fn($q) => $q
                ->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                ->orWhere(fn($q2) => $q2
                    ->where('jam_mulai', '<=', $request->jam_mulai)
                    ->where('jam_selesai', '>=', $request->jam_selesai)
                )
            )->exists();

        if ($clashGuru) {
            return back()->withInput()
                ->withErrors(['jam_mulai' => 'Guru sudah memiliki jadwal pada slot waktu tersebut.']);
        }

        // ── Cek time-clash murid (via SPP) ─────────────────────────
        $clashMurid = Jadwal::where('id_spp', $request->id_spp)
            ->whereDate('tanggal', $request->tanggal)
            ->where('is_active', true)
            ->where(fn($q) => $q
                ->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                ->orWhere(fn($q2) => $q2
                    ->where('jam_mulai', '<=', $request->jam_mulai)
                    ->where('jam_selesai', '>=', $request->jam_selesai)
                )
            )->exists();

        if ($clashMurid) {
            return back()->withInput()
                ->withErrors(['jam_mulai' => 'Murid sudah memiliki jadwal pada slot waktu tersebut.']);
        }

        $admin = Admin::where('id_user', Auth::id())->firstOrFail();

        Jadwal::create([
            'id_admin'    => $admin->id_admin,
            'id_guru'     => $request->id_guru,
            'id_spp'      => $request->id_spp,
            'tanggal'     => $request->tanggal,
            'jam_mulai'   => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'sesi_ke'     => $request->sesi_ke,
            'status_jadwal' => 'Sesuai Jadwal',
            'is_active'   => true,
        ]);

        return redirect()->route('admin.jadwals.index')
            ->with('success', 'Jadwal berhasil dibuat.');
    }

    /**
     * Form edit / reschedule jadwal.
     */
    public function edit(Jadwal $jadwal)
    {
        $gurus = Guru::where('status_aktif', true)->with('spesialisasis')->get();
        $spps  = Spp::with('murid')
            ->whereHas('murid', fn($q) => $q->where('status_aktif', true))
            ->latest()
            ->get();

        return view('admin.jadwals.edit', compact('jadwal', 'gurus', 'spps'));
    }

    /**
     * Update jadwal.
     * ↳ Jika tanggal/jam/guru/murid berubah → status_jadwal = 'Reschedule'.
     */
    public function update(Request $request, Jadwal $jadwal)
    {
        $request->validate([
            'id_guru'    => 'required|exists:gurus,id_guru',
            'id_spp'     => 'required|exists:spps,id_spp',
            'tanggal'    => 'required|date',
            'jam_mulai'  => 'required|date_format:H:i',
            'jam_selesai'=> 'required|date_format:H:i|after:jam_mulai',
            'sesi_ke'    => 'required|integer|min:1',
        ]);

        // ── Deteksi perubahan parameter penjadwalan ────────────────
        $adaPerubahan =
            $jadwal->id_guru     != $request->id_guru    ||
            $jadwal->id_spp      != $request->id_spp     ||
            $jadwal->tanggal->toDateString() != $request->tanggal ||
            substr($jadwal->jam_mulai, 0, 5)  != $request->jam_mulai  ||
            substr($jadwal->jam_selesai, 0, 5) != $request->jam_selesai;

        // ── Cek time-clash guru (kecualikan jadwal ini sendiri) ────
        $clashGuru = Jadwal::where('id_guru', $request->id_guru)
            ->whereDate('tanggal', $request->tanggal)
            ->where('is_active', true)
            ->where('id_jadwal', '!=', $jadwal->id_jadwal)
            ->where(fn($q) => $q
                ->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                ->orWhere(fn($q2) => $q2
                    ->where('jam_mulai', '<=', $request->jam_mulai)
                    ->where('jam_selesai', '>=', $request->jam_selesai)
                )
            )->exists();

        if ($clashGuru) {
            return back()->withInput()
                ->withErrors(['jam_mulai' => 'Guru sudah memiliki jadwal pada slot waktu tersebut.']);
        }

        // ── Cek time-clash murid ───────────────────────────────────
        $clashMurid = Jadwal::where('id_spp', $request->id_spp)
            ->whereDate('tanggal', $request->tanggal)
            ->where('is_active', true)
            ->where('id_jadwal', '!=', $jadwal->id_jadwal)
            ->where(fn($q) => $q
                ->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                ->orWhere(fn($q2) => $q2
                    ->where('jam_mulai', '<=', $request->jam_mulai)
                    ->where('jam_selesai', '>=', $request->jam_selesai)
                )
            )->exists();

        if ($clashMurid) {
            return back()->withInput()
                ->withErrors(['jam_mulai' => 'Murid sudah memiliki jadwal pada slot waktu tersebut.']);
        }

        $jadwal->update([
            'id_guru'      => $request->id_guru,
            'id_spp'       => $request->id_spp,
            'tanggal'      => $request->tanggal,
            'jam_mulai'    => $request->jam_mulai,
            'jam_selesai'  => $request->jam_selesai,
            'sesi_ke'      => $request->sesi_ke,
            // Jika ada perubahan parameter jadwal → otomatis Reschedule
            'status_jadwal' => $adaPerubahan ? 'Reschedule' : $jadwal->status_jadwal,
        ]);

        $pesan = $adaPerubahan
            ? 'Jadwal berhasil diperbarui dan ditandai sebagai Reschedule.'
            : 'Jadwal berhasil diperbarui.';

        return redirect()->route('admin.jadwals.index')->with('success', $pesan);
    }

    /**
     * Soft-delete jadwal (set is_active = false).
     */
    public function destroy(Jadwal $jadwal)
    {
        $jadwal->update(['is_active' => false]);

        return back()->with('success', 'Jadwal dinonaktifkan.');
    }
}