<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HonorGuru;
use App\Models\Guru;
use App\Models\Admin;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HonorGuruController extends Controller
{
    /**
     * Daftar rekap honor semua guru + filter bulan.
     */
    public function index(Request $request)
    {
        $bulan = $request->bulan ?? now()->format('Y-m');

        $gurus = Guru::with(['user', 'spesialisasis'])
            ->where('status_aktif', true)
            ->get()
            ->map(function (Guru $guru) use ($bulan) {
                // Hitung sesi 'DONE' (kehadiran guru tercatat) dalam bulan tsb
                $sesiSelesai = Jadwal::where('id_guru', $guru->id_guru)
                    ->where('status_jadwal', 'Sesuai Jadwal')
                    ->whereNotNull('status_kehadiran_guru')
                    ->where('status_kehadiran_guru', 'Hadir')
                    ->whereYear('tanggal', substr($bulan, 0, 4))
                    ->whereMonth('tanggal', substr($bulan, 5, 2))
                    ->count();

                $guru->sesi_bulan_ini   = $sesiSelesai;
                // Kelompokkan ke dalam blok 4 pertemuan yang sudah selesai
                $guru->blok_terbayarkan = floor($sesiSelesai / 4);

                // Ambil record honor yang sudah dibuat untuk bulan ini
                $honor = HonorGuru::where('id_guru', $guru->id_guru)
                    ->whereYear('tanggal_pencairan', substr($bulan, 0, 4))
                    ->whereMonth('tanggal_pencairan', substr($bulan, 5, 2))
                    ->latest()
                    ->first();

                $guru->honor_record = $honor;

                return $guru;
            });

        return view('admin.honor_guru.index', compact('gurus', 'bulan'));
    }

    /**
     * Form tambah record honor untuk satu guru.
     * Admin hanya boleh memasukkan kelipatan 4 pertemuan.
     */
    public function create(Request $request)
    {
        $guru  = Guru::with(['user', 'spesialisasis'])->findOrFail($request->id_guru);
        $bulan = $request->bulan ?? now()->format('Y-m');

        // Hitung sesi hadir guru di bulan tsb yang belum tercover honor
        $sesiTercover = HonorGuru::where('id_guru', $guru->id_guru)
            ->whereYear('tanggal_pencairan', substr($bulan, 0, 4))
            ->whereMonth('tanggal_pencairan', substr($bulan, 5, 2))
            ->sum('jumlah_pertemuan');

        $sesiHadir = Jadwal::where('id_guru', $guru->id_guru)
            ->where('status_kehadiran_guru', 'Hadir')
            ->whereYear('tanggal', substr($bulan, 0, 4))
            ->whereMonth('tanggal', substr($bulan, 5, 2))
            ->count();

        // Sesi yang belum dibayar (belum masuk di honor record manapun)
        $sisaSesi = $sesiHadir - $sesiTercover;

        // Hanya blok 4 yang penuh yang boleh dibayar
        $blokBisaDibayar = floor($sisaSesi / 4);

        return view('admin.honor_guru.create', compact('guru', 'bulan', 'sisaSesi', 'blokBisaDibayar', 'sesiHadir', 'sesiTercover'));
    }

    /**
     * Simpan record honor baru.
     * Validasi: jumlah_pertemuan harus kelipatan 4.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_guru'            => 'required|exists:gurus,id_guru',
            'jumlah_pertemuan'   => [
                'required',
                'integer',
                'min:4',
                function ($attribute, $value, $fail) {
                    if ($value % 4 !== 0) {
                        $fail('Jumlah pertemuan harus kelipatan 4 (4, 8, 12, ...).');
                    }
                },
            ],
            'jumlah_honor'       => 'required|numeric|min:0',
            'tanggal_pencairan'  => 'required|date',
            'catatan'            => 'nullable|string|max:500',
        ]);

        $admin = Admin::where('id_user', Auth::id())->firstOrFail();

        HonorGuru::create([
            'id_guru'           => $request->id_guru,
            'id_admin'          => $admin->id_admin,
            'tanggal_pencairan' => $request->tanggal_pencairan,
            'jumlah_pertemuan'  => $request->jumlah_pertemuan,
            'jumlah_honor'      => $request->jumlah_honor,
            'status_bayar'      => 'Siap Dibayar',
            'catatan'           => $request->catatan,
        ]);

        return redirect()->route('admin.honor-guru.index')
            ->with('success', 'Record honor berhasil ditambahkan.');
    }

    /**
     * Detail / rekap honor satu guru lintas bulan.
     */
    public function show(Guru $guru)
    {
        $honors = HonorGuru::with('admin.user')
            ->where('id_guru', $guru->id_guru)
            ->latest('tanggal_pencairan')
            ->paginate(20);

        return view('admin.honor_guru.show', compact('guru', 'honors'));
    }

    /**
     * Upload bukti transfer & tandai Lunas.
     */
    public function bayar(Request $request, HonorGuru $honorGuru)
    {
        $request->validate([
            'file_bukti_transfer' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // Hapus file lama jika ada
        if ($honorGuru->file_bukti_transfer
            && Storage::disk('public')->exists($honorGuru->file_bukti_transfer)) {
            Storage::disk('public')->delete($honorGuru->file_bukti_transfer);
        }

        $path = $request->file('file_bukti_transfer')
            ->store('bukti-honor', 'public');

        $admin = Admin::where('id_user', Auth::id())->firstOrFail();

        $honorGuru->update([
            'id_admin'            => $admin->id_admin,
            'file_bukti_transfer' => $path,
            'status_bayar'        => 'Lunas',
            'tanggal_pencairan'   => $honorGuru->tanggal_pencairan ?? now()->toDateString(),
        ]);

        return back()->with('success', 'Honor guru berhasil dibayar dan bukti tersimpan.');
    }

    /**
     * Ubah status honor secara manual (Belum Lunas → Siap Dibayar → Lunas).
     */
    public function updateStatus(Request $request, HonorGuru $honorGuru)
    {
        $request->validate([
            'status_bayar' => 'required|in:Belum Lunas,Siap Dibayar,Lunas',
        ]);

        $honorGuru->update(['status_bayar' => $request->status_bayar]);

        return back()->with('success', 'Status honor diperbarui.');
    }

    /**
     * Hapus record honor (hanya jika belum Lunas).
     */
    public function destroy(HonorGuru $honorGuru)
    {
        if ($honorGuru->status_bayar === 'Lunas') {
            return back()->with('error', 'Record honor yang sudah lunas tidak dapat dihapus.');
        }

        if ($honorGuru->file_bukti_transfer
            && Storage::disk('public')->exists($honorGuru->file_bukti_transfer)) {
            Storage::disk('public')->delete($honorGuru->file_bukti_transfer);
        }

        $honorGuru->delete();

        return back()->with('success', 'Record honor dihapus.');
    }
}