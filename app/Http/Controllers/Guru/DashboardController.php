<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Jadwal;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $guru = Guru::where('id_user', Auth::id())->firstOrFail();

        $jadwalHariIni = Jadwal::where('id_guru', $guru->id_guru)
            ->where('is_active', true)
            ->with(['murid', 'kelas'])
            ->get();

        return view('guru.dashboard', compact('guru', 'jadwalHariIni'));
    }
}
