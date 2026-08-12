<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Murid;
use App\Models\Notifikasi;
use App\Models\ProgramKursus;
use App\Models\Spp;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SppController extends Controller
{
    /**
     * Daftar semua tagihan SPP dengan filter bulan & status.
     */
    public function index(Request $request)
    {
        $bulan = $request->bulan ?? now()->format('Y-m');
        $status = $request->status ?? '';

        $query = Spp::with(['murid', 'programKursus', 'transaksi'])
            ->whereYear('periode_tagihan', substr($bulan, 0, 4))
            ->whereMonth('periode_tagihan', substr($bulan, 5, 2));

        if ($status !== '') {
            $query->where('status_bayar', $status);
        }

        $spps = $query->latest('tanggal_jatuh_tempo')->paginate(25);

        // Ringkasan bulan ini
        $totalTagihan = (clone $query->getQuery())->sum('nominal_tagihan');
        $totalMasuk = Spp::where('status_bayar', 'Lunas')
            ->whereYear('periode_tagihan', substr($bulan, 0, 4))
            ->whereMonth('periode_tagihan', substr($bulan, 5, 2))
            ->sum('nominal_tagihan');
        $totalTunggakan = Spp::where('status_bayar', 'Belum Lunas')
            ->whereYear('periode_tagihan', substr($bulan, 0, 4))
            ->whereMonth('periode_tagihan', substr($bulan, 5, 2))
            ->sum('nominal_tagihan');

        return view('admin.spp.index', compact(
            'spps', 'bulan', 'status',
            'totalTagihan', 'totalMasuk', 'totalTunggakan'
        ));
    }

    /**
     * Generate tagihan SPP bulanan untuk semua murid aktif.
     * Nominal diambil dari biaya_kursus program kursus masing-masing murid,
     * atau bisa di-override via form.
     *
     * Aturan: firstOrCreate → tidak duplikat jika dijalankan ulang.
     */
    public function generateBulanan(Request $request)
    {
        $request->validate([
            'bulan' => 'required|date_format:Y-m',
            'nominal' => 'nullable|numeric|min:0',
            'tipe_les' => 'required|in:Onsite,Home Private',
            'id_program' => 'required|exists:program_kursus,id_program',
        ]);

        $program = ProgramKursus::findOrFail($request->id_program);
        $nominal = $request->nominal ?? $program->biaya_kursus;

        $murids = Murid::where('status_aktif', true)->get();
        $created = 0;

        DB::transaction(function () use ($murids, $program, $nominal, $request, &$created) {
            foreach ($murids as $murid) {
                $spp = Spp::firstOrCreate(
                    [
                        'id_murid' => $murid->id_murid,
                        'id_program' => $program->id_program,
                        'periode_tagihan' => $request->bulan.'-01',
                    ],
                    [
                        'nominal_tagihan' => $nominal,
                        'tanggal_jatuh_tempo' => now()->parse($request->bulan.'-01')->endOfMonth()->toDateString(),
                        'tipe_les' => $request->tipe_les,
                        'status_bayar' => 'Belum Lunas',
                    ]
                );

                if ($spp->wasRecentlyCreated) {
                    $created++;
                }
            }
        });

        return back()->with('success',
            "Tagihan SPP {$request->bulan} berhasil di-generate untuk {$created} murid baru."
        );
    }

    /**
     * Validasi bukti bayar: ubah status SPP → Lunas, isi tanggal_konfirmasi.
     */
    public function validasi(Request $request, Spp $spp)
    {
        $request->validate(['catatan_admin' => 'nullable|string|max:500']);

        if ($spp->status_bayar === 'Lunas') {
            return back()->with('error', 'SPP ini sudah lunas.');
        }

        $transaksi = $spp->transaksi()->latest()->first();

        if (! $transaksi) {
            return back()->with('error', 'Belum ada bukti transfer yang diunggah murid.');
        }

        $admin = Admin::where('id_user', Auth::id())->firstOrFail();

        DB::transaction(function () use ($spp, $transaksi, $admin, $request) {
            // Update status SPP
            $spp->update(['status_bayar' => 'Lunas']);

            // Update transaksi: isi admin, tanggal konfirmasi, catatan
            $transaksi->update([
                'id_admin' => $admin->id_admin,
                'tanggal_konfirmasi' => now()->toDateString(),
                'catatan_admin' => $request->catatan_admin,
            ]);
        });

        return back()->with('success', 'Pembayaran berhasil divalidasi dan SPP ditandai Lunas.');
    }

    /**
     * Tolak bukti transfer: hapus transaksi, status SPP tetap Belum Lunas.
     */
    public function tolak(Request $request, Spp $spp)
    {
        $request->validate(['catatan_admin' => 'nullable|string|max:500']);

        $transaksi = $spp->transaksi()->latest()->first();

        if (! $transaksi) {
            return back()->with('error', 'Tidak ada transaksi untuk ditolak.');
        }

        DB::transaction(function () use ($transaksi) {
            // Hapus bukti transfer dari private storage
            if ($transaksi->file_bukti_transfer) {
                Storage::disk('local')->delete($transaksi->file_bukti_transfer);
            }

            $transaksi->delete();
        });

        return back()->with('success', 'Bukti transfer ditolak dan dihapus. Murid dapat mengunggah ulang.');
    }

    /**
     * Kirim notifikasi pengingat pembayaran SPP ke murid tertentu.
     * Notifikasi disimpan ke tabel notifikasis milik user murid tersebut.
     */
    public function kirimNotifikasi(Request $request, Spp $spp)
    {
        // Pastikan SPP belum lunas — tidak perlu notifikasi kalau sudah bayar
        if ($spp->status_bayar === 'Lunas') {
            return back()->with('error', 'SPP ini sudah lunas, notifikasi tidak perlu dikirim.');
        }

        $murid = $spp->murid;

        if (! $murid || ! $murid->id_user) {
            return back()->with('error', 'Data murid tidak ditemukan.');
        }

        $periode = Carbon::parse($spp->periode_tagihan)->translatedFormat('F Y');
        $nominal = 'Rp '.number_format($spp->nominal_tagihan, 0, ',', '.');
        $jatuhTempo = $spp->tanggal_jatuh_tempo->format('d/m/Y');

        $pesanDefault = "Halo {$murid->nama_murid}, mohon segera melunasi tagihan SPP bulan {$periode} sebesar {$nominal} sebelum {$jatuhTempo}.";
        $pesan = $request->filled('pesan') ? $request->pesan : $pesanDefault;

        Notifikasi::create([
            'id_user' => $murid->id_user,
            'jenis_notifikasi' => 'tagihan_spp',
            'pesan' => $pesan,
            'status_baca' => 'belum_dibaca',
            'id_referensi' => $spp->id_spp,
        ]);

        return back()->with('success', "Notifikasi berhasil dikirim ke {$murid->nama_murid}.");
    }

    /**
     * Stream a payment proof file to the authenticated admin.
     *
     * The file is stored on the private disk and cannot be accessed
     * via a direct public URL. This method authorises the request,
     * resolves the file path, and streams it inline so the admin can
     * view or download it without ever exposing the real file path.
     */
    public function viewBukti(Transaksi $transaksi)
    {
        $path = $transaksi->file_bukti_transfer;

        abort_unless($path && Storage::disk('local')->exists($path), 404, 'File bukti transfer tidak ditemukan.');

        $mimeType = Storage::disk('local')->mimeType($path);
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        return response()->stream(function () use ($path) {
            echo Storage::disk('local')->get($path);
        }, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="bukti_transfer.'.$extension.'"',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
