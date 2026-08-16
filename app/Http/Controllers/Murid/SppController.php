<?php

namespace App\Http\Controllers\Murid;

use App\Http\Controllers\Controller;
use App\Http\Requests\Murid\UploadBuktiSppRequest;
use App\Models\Murid;
use App\Models\Spp;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SppController extends Controller
{
    // Riwayat SPP murid
    public function index()
    {
        $murid = Murid::where('id_user', Auth::id())->firstOrFail();
        $spps = $murid->spps()->with('transaksi')->latest()->paginate(12);

        return view('murid.spp.index', compact('spps'));
    }

    // Upload bukti transfer
    public function uploadBukti(UploadBuktiSppRequest $request, Spp $spp)
    {
        $murid = Murid::where('id_user', Auth::id())->firstOrFail();

        // Pastikan SPP milik murid ini
        abort_unless($spp->id_murid === $murid->id_murid, 403, 'Akses ditolak: Data SPP ini bukan milik Anda.');

        $file = $request->file('bukti_transfer');
        $filename = 'bukti_'.$spp->id_spp.'_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();

        // Store in PRIVATE disk — inaccessible via direct public URL.
        // Admins view files through an authenticated streamed route.
        $path = $file->storeAs('bukti_transfer', $filename, 'local');

        DB::transaction(function () use ($spp, $path, $request) {
            // Lock SPP to prevent concurrent double-uploads / race conditions
            $lockedSpp = Spp::where('id_spp', $spp->id_spp)->lockForUpdate()->firstOrFail();

            // Hapus transaksi lama jika ada (re-upload)
            $oldTransaksi = Transaksi::where('id_spp', $lockedSpp->id_spp)->lockForUpdate()->first();
            if ($oldTransaksi) {
                if ($oldTransaksi->file_bukti_transfer) {
                    Storage::disk('local')->delete($oldTransaksi->file_bukti_transfer);
                }
                $oldTransaksi->delete();
            }

            Transaksi::create([
                'id_spp' => $lockedSpp->id_spp,
                'id_admin' => null,
                'file_bukti_transfer' => $path,
                'nominal_bayar' => $request->nominal_bayar,
                'tanggal_bayar' => $request->tanggal_bayar,
            ]);
        });

        return back()->with('success', 'Bukti transfer berhasil diunggah. Menunggu konfirmasi admin.');
    }

    /**
     * Stream payment proof file securely to the student who owns this SPP.
     */
    public function viewBukti(Spp $spp)
    {
        $murid = Murid::where('id_user', Auth::id())->firstOrFail();

        abort_unless($spp->id_murid === $murid->id_murid, 403, 'Akses ditolak: Data SPP ini bukan milik Anda.');

        $transaksi = $spp->transaksi;
        abort_unless($transaksi && $transaksi->file_bukti_transfer, 404, 'File bukti transfer tidak ditemukan.');

        $path = $transaksi->file_bukti_transfer;
        abort_unless(Storage::disk('local')->exists($path), 404, 'File bukti transfer tidak ditemukan pada server.');

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
