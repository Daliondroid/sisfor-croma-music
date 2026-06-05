<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Spp;
use App\Models\Transaksi;
use App\Models\Murid;
use App\Models\Admin;
use App\Models\ProgramKursus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SppController extends Controller
{
    /**
     * Daftar semua tagihan SPP dengan filter bulan & status.
     */
    public function index(Request $request)
    {
        $bulan  = $request->bulan  ?? now()->format('Y-m');
        $status = $request->status ?? '';

        $query = Spp::with(['murid', 'programKursus', 'transaksi'])
            ->whereYear('periode_tagihan', substr($bulan, 0, 4))
            ->whereMonth('periode_tagihan', substr($bulan, 5, 2));

        if ($status !== '') {
            $query->where('status_bayar', $status);
        }

        $spps = $query->latest('tanggal_jatuh_tempo')->paginate(25);

        // Ringkasan bulan ini
        $totalTagihan   = (clone $query->getQuery())->sum('nominal_tagihan');
        $totalMasuk     = Spp::where('status_bayar', 'Lunas')
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
            'bulan'        => 'required|date_format:Y-m',
            'nominal'      => 'nullable|numeric|min:0',
            'tipe_les'     => 'required|in:Onsite,Home Private',
            'id_program'   => 'required|exists:program_kursus,id_program',
        ]);

        $program = ProgramKursus::findOrFail($request->id_program);
        $nominal = $request->nominal ?? $program->biaya_kursus;

        $murids  = Murid::where('status_aktif', true)->get();
        $created = 0;

        foreach ($murids as $murid) {
            $spp = Spp::firstOrCreate(
                [
                    'id_murid'        => $murid->id_murid,
                    'id_program'      => $program->id_program,
                    'periode_tagihan' => $request->bulan . '-01',
                ],
                [
                    'nominal_tagihan'    => $nominal,
                    'tanggal_jatuh_tempo'=> now()->parse($request->bulan . '-01')->endOfMonth()->toDateString(),
                    'tipe_les'           => $request->tipe_les,
                    'status_bayar'       => 'Belum Lunas',
                ]
            );

            if ($spp->wasRecentlyCreated) {
                $created++;
            }
        }

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

        // Update status SPP
        $spp->update(['status_bayar' => 'Lunas']);

        // Update transaksi: isi admin, tanggal konfirmasi, catatan
        $transaksi->update([
            'id_admin'           => $admin->id_admin,
            'tanggal_konfirmasi' => now()->toDateString(),
            'catatan_admin'      => $request->catatan_admin,
        ]);

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

        // Hapus bukti transfer dari storage
        if ($transaksi->file_bukti_transfer) {
            \Illuminate\Support\Facades\Storage::disk('public')
                ->delete($transaksi->file_bukti_transfer);
        }

        $transaksi->delete();

        return back()->with('success', 'Bukti transfer ditolak dan dihapus. Murid dapat mengunggah ulang.');
    }
}