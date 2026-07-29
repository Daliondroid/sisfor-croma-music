<?php

namespace App\Http\Controllers\Murid;

use App\Http\Controllers\Controller;
use App\Models\Spp;
use App\Models\Transaksi;
use App\Models\Murid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SppController extends Controller
{
    // Riwayat SPP murid
    public function index()
    {
        $murid = Murid::where('id_user', Auth::id())->firstOrFail();
        $spps  = $murid->spps()->with('transaksi')->latest()->paginate(12);
        return view('murid.spp.index', compact('spps'));
    }

    // Upload bukti transfer
    public function uploadBukti(\App\Http\Requests\Murid\UploadBuktiSppRequest $request, Spp $spp)
    {
        $murid = Murid::where('id_user', Auth::id())->firstOrFail();

        // Pastikan SPP milik murid ini
        abort_unless($spp->id_murid === $murid->id_murid, 403, 'Akses ditolak: Data SPP ini bukan milik Anda.');

        $file = $request->file('bukti_transfer');
        $filename = 'bukti_' . $spp->id_spp . '_' . time() . '_' . \Illuminate\Support\Str::random(8) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('bukti_transfer', $filename, 'public');

        // Hapus transaksi lama jika ada (re-upload)
        $oldTransaksi = Transaksi::where('id_spp', $spp->id_spp)->first();
        if ($oldTransaksi) {
            if ($oldTransaksi->file_bukti_transfer) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldTransaksi->file_bukti_transfer);
            }
            $oldTransaksi->delete();
        }

        Transaksi::create([
            'id_spp'              => $spp->id_spp,
            'id_admin'            => null,
            'file_bukti_transfer' => $path,
            'nominal_bayar'       => $request->nominal_bayar,
            'tanggal_bayar'       => $request->tanggal_bayar,
        ]);

        return back()->with('success', 'Bukti transfer berhasil diunggah. Menunggu konfirmasi admin.');
    }
}
