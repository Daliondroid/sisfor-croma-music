<?php

namespace App\Http\Controllers\Murid;

use App\Http\Controllers\Controller;
use App\Models\Murid;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    public function index()
    {
        $murid = Murid::where('id_user', Auth::id())->firstOrFail();

        // Mengambil seluruh jadwal aktif murid dengan relasi terkait
        $jadwals = $murid->jadwals()
            ->where('is_active', true)
            ->with(['guru', 'spp.programKursus'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_mulai', 'desc')
            ->get();

        return view('murid.jadwal.index', compact('murid', 'jadwals'));
    }
}