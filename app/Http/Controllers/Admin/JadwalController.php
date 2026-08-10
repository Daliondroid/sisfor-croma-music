<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Murid;
use App\Models\HonorGuru;
use App\Models\Guru;
use App\Models\ProgramKursus;
use App\Models\Spp;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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

        // Filter pencarian nama murid
        if ($request->filled('search')) {
            $query->whereHas('spp.murid', function($q) use ($request) {
                $q->where('nama_murid', 'like', '%' . $request->search . '%');
            });
        }

        // Filter opsional lainnya yang sudah ada
        if ($request->filled('id_guru')) {
            $query->where('id_guru', $request->id_guru);
        }
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        if ($request->filled('status')) {
            $query->where('status_jadwal', $request->status);
        }

        // Paginate dengan menyertakan seluruh query string agar paginasi tidak mereset filter
        $jadwals = $query->latest('tanggal')->paginate(100)->withQueryString();

        $gurus = Guru::where('status_aktif', true)->get();

        return view('admin.jadwals.index', compact('jadwals', 'gurus'));
    }
    /**
     * Form buat jadwal baru.
     */
    public function create()
    {
        $murids   = \App\Models\Murid::all();
        $gurus    = \App\Models\Guru::where('status_aktif', true)->get();
        $programs = \App\Models\ProgramKursus::where('is_active', true)->get();

        return view('admin.jadwals.create', compact('murids', 'gurus', 'programs'));
    }

    /**
     * Simpan jadwal baru using JadwalBuilderService.
     */
    public function store(\App\Http\Requests\Admin\StoreJadwalRequest $request, \App\Services\JadwalBuilderService $builderService)
    {
        try {
            $admin = Auth::user()->admin;
            if (!$admin) {
                throw new \Exception('Akses ditolak: User tidak memiliki profil Admin.');
            }

            $count = $builderService->createBulkJadwal($request->validated(), $admin->id_admin);

            return redirect()->route('admin.jadwals.index')
                ->with('success', "Berhasil menjadwalkan {$count} pertemuan KBM. Sistem juga telah membuat Tagihan SPP otomatis dan Draft Gaji Guru.");
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Proses pembuatan jadwal gagal: ' . $e->getMessage()]);
        }
    }

    // Fungsi Endpoint API untuk mengecek riwayat sesi secara asinkronus (Realtime UX)
    public function cekSesi(Request $request)
    {
        $spp = \App\Models\Spp::where('id_murid', $request->id_murid)
            ->where('id_program', $request->id_program)
            ->latest('id_spp')
            ->first();

        if ($spp) {
            $lastSesi = \App\Models\Jadwal::where('id_spp', $spp->id_spp)->max('sesi_ke') ?? 0;
            return response()->json(['last_sesi' => $lastSesi]);
        }

        return response()->json(['last_sesi' => 0]);
    }


/**
     * Tampilkan detail jadwal spesifik.
     */
    public function show(Jadwal $jadwal)
    {
        // Ubah spp.program menjadi spp.programKursus
        $jadwal->load(['guru', 'spp.murid', 'spp.programKursus']);

        return view('admin.jadwals.show', compact('jadwal'));
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
     * Jika tanggal/jam/guru berubah → status = 'Reschedule' & reset presensi.
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
            $jadwal->tanggal->toDateString() != $request->tanggal ||
            substr($jadwal->jam_mulai, 0, 5)  != $request->jam_mulai  ||
            substr($jadwal->jam_selesai, 0, 5) != $request->jam_selesai;

        // ── Cek time-clash guru (kecualikan jadwal ini sendiri) ────
        if (Jadwal::hasGuruClash($request->id_guru, $request->tanggal, $request->jam_mulai, $request->jam_selesai, $jadwal->id_jadwal)) {
            return back()->withInput()
                ->withErrors(['jam_mulai' => 'Guru sudah memiliki jadwal pada slot waktu tersebut.']);
        }

        // ── Cek time-clash murid ───────────────────────────────────
        if (Jadwal::hasMuridClash($request->id_spp, $request->tanggal, $request->jam_mulai, $request->jam_selesai, $jadwal->id_jadwal)) {
            return back()->withInput()
                ->withErrors(['jam_mulai' => 'Murid sudah memiliki jadwal pada slot waktu tersebut.']);
        }

        // ── Siapkan Data Update ────────────────────────────────────
        $dataUpdate = [
            'id_guru'      => $request->id_guru,
            'id_spp'       => $request->id_spp,
            'tanggal'      => $request->tanggal,
            'jam_mulai'    => $request->jam_mulai,
            'jam_selesai'  => $request->jam_selesai,
            'sesi_ke'      => $request->sesi_ke,
        ];

        // Jika jadwal berubah, ubah status dan bersihkan data presensi
        if ($adaPerubahan) {
            $dataUpdate['status_jadwal'] = 'Reschedule';
            $dataUpdate['status_kehadiran_murid'] = null;
            $dataUpdate['status_kehadiran_guru'] = null;
            $dataUpdate['waktu_presensi_diisi'] = null;
            $dataUpdate['presensi_diisi_oleh'] = null;
        }

        $jadwal->update($dataUpdate);

        $pesan = $adaPerubahan
            ? 'Jadwal berhasil diperbarui. Status menjadi Reschedule dan data presensi sebelumnya telah direset.'
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