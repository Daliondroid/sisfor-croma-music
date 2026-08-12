<?php

namespace App\Http\Controllers\Murid;

use App\Http\Controllers\Controller;
use App\Http\Requests\Murid\UploadBuktiSppRequest;
use App\Models\Murid;
use App\Models\Spp;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;
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

        // Hapus transaksi lama jika ada (re-upload)
        $oldTransaksi = Transaksi::where('id_spp', $spp->id_spp)->first();
        if ($oldTransaksi) {
            if ($oldTransaksi->file_bukti_transfer) {
                Storage::disk('local')->delete($oldTransaksi->file_bukti_transfer);
            }
            $oldTransaksi->delete();
        }

        Transaksi::create([
            'id_spp' => $spp->id_spp,
            'id_admin' => null,
            'file_bukti_transfer' => $path,
            'nominal_bayar' => $request->nominal_bayar,
            'tanggal_bayar' => $request->tanggal_bayar,
        ]);

        return back()->with('success', 'Bukti transfer berhasil diunggah. Menunggu konfirmasi admin.');
    }
}
