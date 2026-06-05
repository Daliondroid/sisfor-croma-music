<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Guru;
use App\Models\ProgresMurid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProgresMuridController extends Controller
{
    /**
     * Daftar semua progres murid yang pernah diinput guru ini.
     * Bisa difilter per murid (via id_spp) atau per bulan.
     *
     * FR-19: Menginput Laporan KBM Harian
     * UC-19: Dependensi UC-18 (Kehadiran Guru sudah diisi)
     */
    public function index(Request $request)
    {
        $guru = Guru::where('id_user', Auth::id())->firstOrFail();

        // Ambil semua jadwal milik guru yang sudah ada progres
        $query = ProgresMurid::with(['jadwal.spp.murid', 'jadwal.spp.programKursus'])
            ->whereHas('jadwal', fn($q) => $q->where('id_guru', $guru->id_guru));

        if ($request->filled('id_spp')) {
            $query->whereHas('jadwal', fn($q) => $q->where('id_spp', $request->id_spp));
        }

        if ($request->filled('bulan')) {
            [$tahun, $bln] = explode('-', $request->bulan);
            $query->whereHas('jadwal', fn($q) => $q
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bln)
            );
        }

        $progres = $query->latest()->paginate(20);

        // Daftar murid yang diajar (untuk filter dropdown)
        $muridDiajar = Jadwal::with('spp.murid')
            ->where('id_guru', $guru->id_guru)
            ->where('is_active', true)
            ->get()
            ->unique('id_spp')
            ->pluck('spp');

        return view('guru.progres.index', compact('guru', 'progres', 'muridDiajar'));
    }

    /**
     * Form input laporan KBM harian untuk jadwal tertentu.
     * Hanya bisa diisi jika kehadiran guru SUDAH dicatat (status_kehadiran_guru IS NOT NULL).
     */
    public function create(Request $request)
    {
        $guru   = Guru::where('id_user', Auth::id())->firstOrFail();
        $jadwal = Jadwal::with(['spp.murid', 'spp.programKursus', 'progresMurid'])
            ->where('id_jadwal', $request->id_jadwal)
            ->where('id_guru', $guru->id_guru)
            ->firstOrFail();

        // Cek dependensi UC-18: kehadiran guru harus sudah diisi
        if (is_null($jadwal->status_kehadiran_guru)) {
            return redirect()
                ->route('guru.presensi.index', ['jadwal' => $jadwal->id_jadwal])
                ->with('error', 'Isi presensi kehadiran terlebih dahulu sebelum input laporan KBM.');
        }

        // Jika sudah ada progres, redirect ke edit
        if ($jadwal->progresMurid) {
            return redirect()->route('guru.progres.edit', $jadwal->progresMurid->id_progres);
        }

        return view('guru.progres.create', compact('guru', 'jadwal'));
    }

    /**
     * Simpan laporan KBM harian.
     * FR-19: Sistem insert ke tabel PROGRES_MURID dengan FK ke JADWAL.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_jadwal'            => 'required|exists:jadwals,id_jadwal',
            'materi_diajarkan'     => 'required|string|max:1000',
            'catatan_perkembangan' => 'required|string|max:2000',
            'url_foto'             => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $guru   = Guru::where('id_user', Auth::id())->firstOrFail();
        $jadwal = Jadwal::where('id_jadwal', $request->id_jadwal)
            ->where('id_guru', $guru->id_guru)
            ->firstOrFail();

        // Validasi dependensi
        if (is_null($jadwal->status_kehadiran_guru)) {
            return back()->with('error', 'Isi presensi kehadiran terlebih dahulu.');
        }

        // Cegah duplikat (relasi 1:1)
        if ($jadwal->progresMurid()->exists()) {
            return back()->with('error', 'Laporan KBM untuk jadwal ini sudah pernah diinput.');
        }

        $fotoPath = null;
        if ($request->hasFile('url_foto')) {
            $fotoPath = $request->file('url_foto')->store('progres-foto', 'public');
        }

        ProgresMurid::create([
            'id_jadwal'            => $jadwal->id_jadwal,
            'materi_diajarkan'     => $request->materi_diajarkan,
            'catatan_perkembangan' => $request->catatan_perkembangan,
            'url_foto'             => $fotoPath,
        ]);

        return redirect()
            ->route('guru.progres.index')
            ->with('success', 'Laporan KBM berhasil disimpan.');
    }

    /**
     * Form edit laporan KBM.
     */
    public function edit(ProgresMurid $progresMurid)
    {
        $guru = Guru::where('id_user', Auth::id())->firstOrFail();

        // Pastikan progres ini milik guru yang login
        abort_unless(
            $progresMurid->jadwal->id_guru === $guru->id_guru,
            403
        );

        $jadwal = $progresMurid->jadwal->load(['spp.murid', 'spp.programKursus']);

        return view('guru.progres.edit', compact('guru', 'progresMurid', 'jadwal'));
    }

    /**
     * Update laporan KBM.
     */
    public function update(Request $request, ProgresMurid $progresMurid)
    {
        $request->validate([
            'materi_diajarkan'     => 'required|string|max:1000',
            'catatan_perkembangan' => 'required|string|max:2000',
            'url_foto'             => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $guru = Guru::where('id_user', Auth::id())->firstOrFail();
        abort_unless($progresMurid->jadwal->id_guru === $guru->id_guru, 403);

        $data = $request->only(['materi_diajarkan', 'catatan_perkembangan']);

        if ($request->hasFile('url_foto')) {
            // Hapus foto lama
            if ($progresMurid->url_foto && Storage::disk('public')->exists($progresMurid->url_foto)) {
                Storage::disk('public')->delete($progresMurid->url_foto);
            }
            $data['url_foto'] = $request->file('url_foto')->store('progres-foto', 'public');
        }

        $progresMurid->update($data);

        return redirect()
            ->route('guru.progres.index')
            ->with('success', 'Laporan KBM berhasil diperbarui.');
    }

    /**
     * Lihat riwayat progres satu murid (via id_spp).
     * Ini juga dipakai untuk data yang dilihat murid di FR-20.
     */
    public function show(Request $request, int $idSpp)
    {
        $guru = Guru::where('id_user', Auth::id())->firstOrFail();

        // Validasi: id_spp harus punya jadwal milik guru ini
        $jadwals = Jadwal::with(['spp.murid', 'spp.programKursus', 'progresMurid'])
            ->where('id_guru', $guru->id_guru)
            ->where('id_spp', $idSpp)
            ->where('is_active', true)
            ->orderBy('tanggal')
            ->get();

        abort_if($jadwals->isEmpty(), 404);

        $murid   = $jadwals->first()->spp?->murid;
        $program = $jadwals->first()->spp?->programKursus;

        // Filter per bulan opsional
        if ($request->filled('bulan')) {
            [$tahun, $bln] = explode('-', $request->bulan);
            $jadwals = $jadwals->filter(fn($j) =>
                $j->tanggal->year == $tahun && $j->tanggal->month == $bln
            );
        }

        return view('guru.progres.show', compact('guru', 'jadwals', 'murid', 'program', 'idSpp'));
    }
}