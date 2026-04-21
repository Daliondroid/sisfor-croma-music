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
    public function uploadBukti(Request $request, Spp $spp)
    {
        $murid = Murid::where('id_user', Auth::id())->firstOrFail();

        // Pastikan SPP milik murid ini
        abort_unless($spp->id_murid === $murid->id_murid, 403);

        $request->validate([
            'bukti_transfer' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'nominal_bayar'  => 'required|numeric',
            'tanggal_bayar'  => 'required|date',
        ]);

        $path = $request->file('bukti_transfer')->store('bukti_transfer', 'public');

        // Hapus transaksi lama jika ada (re-upload)
        Transaksi::where('id_spp', $spp->id_spp)->delete();

        Transaksi::create([
            'id_spp'             => $spp->id_spp,
            'id_murid'           => $murid->id_murid,
            'id_admin'           => 1, // akan diisi admin saat validasi
            'file_bukti_transfer'=> $path,
            'nominal_bayar'      => $request->nominal_bayar,
            'tanggal_bayar'      => $request->tanggal_bayar,
        ]);

        return back()->with('success', 'Bukti transfer berhasil diunggah. Menunggu konfirmasi admin.');
    }
}
