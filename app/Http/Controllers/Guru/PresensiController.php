<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\MateriKbm;
use App\Models\VideoProgres;
use App\Models\Jadwal;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    // Daftar jadwal guru hari ini
    public function index()
    {
        $guru    = Guru::where('id_user', Auth::id())->firstOrFail();
        $jadwals = Jadwal::where('id_guru', $guru->id_guru)
            ->where('hari', now()->locale('id')->dayName) // sesuaikan locale
            ->where('is_active', true)
            ->with('murid')
            ->get();

        return view('guru.presensi.index', compact('jadwals'));
    }

    // Simpan presensi + materi + video sekaligus
    public function store(Request $request)
    {
        $request->validate([
            'id_jadwal'             => 'required|exists:jadwals,id_jadwal',
            'id_murid'              => 'required|exists:murids,id_murid',
            'tanggal'               => 'required|date',
            'sesi_ke'               => 'required|integer|min:1',
            'status_murid'          => 'required|in:hadir,alpa,izin',
            'materi_diajarkan'      => 'required_if:status_murid,hadir|string',
            'catatan_perkembangan'  => 'nullable|string',
            'tingkat_progres'       => 'nullable|integer|min:0|max:100',
            'url_video'             => 'nullable|url',
            'platform'              => 'nullable|in:google_drive,youtube_private,lainnya',
        ]);

        $guru = Guru::where('id_user', Auth::id())->firstOrFail();

        $presensi = Presensi::create([
            'id_jadwal'    => $request->id_jadwal,
            'id_guru'      => $guru->id_guru,
            'id_murid'     => $request->id_murid,
            'tanggal'      => $request->tanggal,
            'sesi_ke'      => $request->sesi_ke,
            'status_murid' => $request->status_murid,
            'input_by'     => Auth::id(),
        ]);

        // Simpan materi jika murid hadir
        if ($request->status_murid === 'hadir' && $request->materi_diajarkan) {
            MateriKbm::create([
                'id_presensi'          => $presensi->id_presensi,
                'materi_diajarkan'     => $request->materi_diajarkan,
                'catatan_perkembangan' => $request->catatan_perkembangan,
                'tingkat_progres'      => $request->tingkat_progres ?? 0,
            ]);
        }

        // Simpan video jika ada
        if ($request->url_video) {
            VideoProgres::create([
                'id_presensi'    => $presensi->id_presensi,
                'url_video'      => $request->url_video,
                'platform'       => $request->platform ?? 'google_drive',
                'deskripsi_video'=> $request->deskripsi_video,
            ]);
        }

        return back()->with('success', 'Presensi dan materi berhasil disimpan.');
    }
}

