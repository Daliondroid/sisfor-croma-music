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
        // Mengambil master data untuk kebutuhan dropdown
        $murids = \App\Models\Murid::all(); 
        
        // UBAH 'is_active' MENJADI 'status_aktif' DI BARIS BAWAH INI:
        $gurus = \App\Models\Guru::where('status_aktif', true)->get(); 
        
        // Catatan tambahan: Pastikan juga tabel ProgramKursus memang menggunakan kolom 'is_active'.
        // Jika ProgramKursus juga menggunakan 'status_aktif', Anda harus mengubahnya juga di baris bawah ini.
        $programs = \App\Models\ProgramKursus::where('is_active', true)->get();

        return view('admin.jadwals.create', compact('murids', 'gurus', 'programs'));
    }

    /**
     * Simpan jadwal baru.
     * Sistem validasi time-clash: guru / murid tidak boleh dobel pada slot yang sama.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $rules = [
            'id_murid'      => 'required|exists:murids,id_murid',
            'id_program'    => 'required|exists:program_kursus,id_program',
            'id_guru'       => 'required|exists:gurus,id_guru',
            'total_sesi'    => 'required|integer|in:4,8,12,16,20,24',
            'tipe_les'      => 'required|in:Onsite,Home Private',
            'tipe_jadwal'   => 'required|in:tetap,pola,manual',
            'tanggal_mulai' => 'required|date',
        ];

        if ($request->tipe_jadwal === 'tetap') {
            $rules['pola_tunggal.hari'] = 'required|string';
            $rules['pola_tunggal.jam_mulai'] = 'required|date_format:H:i';
            $rules['pola_tunggal.jam_selesai'] = 'required|date_format:H:i|after:pola_tunggal.jam_mulai';
        } elseif ($request->tipe_jadwal === 'pola') {
            $rules['pola'] = 'required|array|size:4';
            $rules['pola.*.hari'] = 'required|string';
            $rules['pola.*.jam_mulai'] = 'required|date_format:H:i';
            $rules['pola.*.jam_selesai'] = 'required|date_format:H:i|after:pola.*.jam_mulai';
        } elseif ($request->tipe_jadwal === 'manual') {
            $rules['jadwal_manual'] = 'required|array';
            $rules['jadwal_manual.*.tanggal'] = 'required|date';
            $rules['jadwal_manual.*.jam_mulai'] = 'required|date_format:H:i';
            $rules['jadwal_manual.*.jam_selesai'] = 'required|date_format:H:i|after:jadwal_manual.*.jam_mulai';
        }

        $request->validate($rules);

        $dayMapping = [
            'Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4,
            'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7
        ];

        DB::beginTransaction();
        try {
            $program = ProgramKursus::findOrFail($request->id_program);
            $idAdmin = Auth::user()->admin->id_admin ?? 1;

            // 2. Mengambil riwayat sesi_ke tertinggi untuk keberlanjutan progres
            $lastSession = Jadwal::whereHas('spp', function($q) use ($request) {
                                $q->where('id_murid', $request->id_murid)
                                  ->where('id_program', $request->id_program);
                            })->max('sesi_ke') ?? 0;

            // 3. Kalkulasi dan Kumpulkan Seluruh Jadwal dalam Array Terlebih Dahulu
            $generatedSessions = [];
            $lastDate = Carbon::parse($request->tanggal_mulai)->subWeek(); 

            for ($i = 1; $i <= $request->total_sesi; $i++) {
                
                if ($request->tipe_jadwal === 'manual') {
                    $tanggal = $request->jadwal_manual[$i - 1]['tanggal'];
                    $jamMulai = $request->jadwal_manual[$i - 1]['jam_mulai'];
                    $jamSelesai = $request->jadwal_manual[$i - 1]['jam_selesai'];
                } else {
                    if ($request->tipe_jadwal === 'tetap') {
                        $hari = $request->pola_tunggal['hari'];
                        $jamMulai = $request->pola_tunggal['jam_mulai'];
                        $jamSelesai = $request->pola_tunggal['jam_selesai'];
                    } else { // tipe_jadwal == 'pola'
                        $slotIndex = ($i - 1) % 4;
                        $hari = $request->pola[$slotIndex]['hari'];
                        $jamMulai = $request->pola[$slotIndex]['jam_mulai'];
                        $jamSelesai = $request->pola[$slotIndex]['jam_selesai'];
                    }

                    if ($i === 1) {
                        $tanggalCarbon = Carbon::parse($request->tanggal_mulai);
                        while ($tanggalCarbon->dayOfWeekIso !== $dayMapping[$hari]) {
                            $tanggalCarbon->addDay();
                        }
                    } else {
                        $tanggalCarbon = $lastDate->copy()->addWeek();
                        $tanggalCarbon->startOfWeek()->addDays($dayMapping[$hari] - 1);
                    }

                    $tanggal = $tanggalCarbon->format('Y-m-d');
                    $lastDate = $tanggalCarbon;
                }

                $generatedSessions[] = [
                    'tanggal'       => $tanggal,
                    'jam_mulai'     => $jamMulai,
                    'jam_selesai'   => $jamSelesai,
                    'sesi_ke'       => $lastSession + $i, 
                ];
            }

            // 4. Proses Pembuatan SPP, Draft Honor, dan Jadwal (Per 4 Pertemuan)
            $chunks = array_chunk($generatedSessions, 4);

            foreach ($chunks as $chunk) {
                $firstSessionDate = Carbon::parse($chunk[0]['tanggal']);

                // Generate SPP (Tagihan Murid)
                $spp = Spp::create([
                    'id_murid'            => $request->id_murid,
                    'id_program'          => $request->id_program,
                    'periode_tagihan'     => $firstSessionDate->copy()->startOfMonth()->format('Y-m-d'),
                    'nominal_tagihan'     => $program->biaya_kursus ?? 0,
                    'tanggal_jatuh_tempo' => $firstSessionDate->format('Y-m-d'),
                    'status_bayar'        => 'Belum Lunas'
                ]);

                // Generate Draft Honor Guru (Gaji Guru)
                $honor = HonorGuru::create([
                    'id_guru'          => $request->id_guru,
                    // Karena di-generate otomatis saat belum gajian, id_admin biarkan kosong sementara
                    'id_admin'         => $idAdmin, 
                    'tanggal_pencairan'=> null,
                    'jumlah_pertemuan' => count($chunk), // Dinamis, normalnya 4
                    'jumlah_honor'     => 0, // Di-set 0 (Admin akan input manual nanti)
                    'status_bayar'     => 'Belum Lunas', // Status bawaan ERD
                    'catatan'          => null
                ]);

                // Mengikat 4 jadwal dalam iterasi ini ke SPP dan Honor Guru
                $jadwalsToInsert = [];
                foreach ($chunk as $session) {
                    $jadwalsToInsert[] = [
                        'id_admin'      => $idAdmin,
                        'id_guru'       => $request->id_guru,
                        'id_spp'        => $spp->id_spp, 
                        'id_honor'      => $honor->id_honor, // <-- Tautkan ID Honor Guru
                        'tanggal'       => $session['tanggal'],
                        'jam_mulai'     => $session['jam_mulai'],
                        'jam_selesai'   => $session['jam_selesai'],
                        'sesi_ke'       => $session['sesi_ke'], 
                        'status_jadwal' => 'Sesuai Jadwal', 
                        'is_active'     => true,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];
                }

                // Eksekusi penyimpanan baris jadwal untuk siklus ini
                Jadwal::insert($jadwalsToInsert);
            }

            DB::commit();

            return redirect()->route('admin.jadwals.index')
                             ->with('success', 'Berhasil menjadwalkan ' . $request->total_sesi . ' pertemuan KBM. Sistem juga telah membuat Tagihan SPP otomatis dan Draft Gaji Guru.');

        } catch (\Exception $e) {
            DB::rollBack();
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