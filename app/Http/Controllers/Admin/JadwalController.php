<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Guru;
use App\Models\Murid;
use App\Models\Kelas;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    public function index()
    {
        $jadwals = Jadwal::with(['guru', 'murid', 'kelas'])
            ->where('is_active', true)
            ->latest()
            ->paginate(20);
        return view('admin.jadwals.index', compact('jadwals'));
    }

    public function create()
    {
        $gurus  = Guru::where('status_aktif', true)->get();
        $murids = Murid::where('status_aktif', true)->get();
        $kelas  = Kelas::all();
        return view('admin.jadwals.create', compact('gurus', 'murids', 'kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_guru'         => 'required|exists:gurus,id_guru',
            'id_murid'        => 'required|exists:murids,id_murid',
            'id_kelas'        => 'required|exists:kelas,id_kelas',
            'hari'            => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai'       => 'required|date_format:H:i',
            'jam_selesai'     => 'required|date_format:H:i|after:jam_mulai',
            'tanggal_berlaku' => 'required|date',
            'lokasi'          => 'nullable|string',
        ]);

        $admin = Admin::where('id_user', Auth::id())->firstOrFail();

        Jadwal::create([
            ...$request->only(['id_guru','id_murid','id_kelas','hari','jam_mulai','jam_selesai','lokasi','tanggal_berlaku']),
            'id_admin'  => $admin->id_admin,
            'is_active' => true,
        ]);

        return redirect()->route('admin.jadwals.index')->with('success', 'Jadwal berhasil dibuat.');
    }

    public function destroy(Jadwal $jadwal)
    {
        $jadwal->update(['is_active' => false]);
        return back()->with('success', 'Jadwal dinonaktifkan.');
    }
}