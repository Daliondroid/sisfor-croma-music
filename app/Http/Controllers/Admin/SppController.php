<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Spp;
use App\Models\Transaksi;
use App\Models\Murid;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SppController extends Controller
{
    // Daftar semua tagihan SPP
    public function index(Request $request)
    {
        $query = Spp::with('murid')
            ->when($request->bulan, fn($q) => $q->where('bulan_tagihan', $request->bulan))
            ->when($request->status, fn($q) => $q->where('status_bayar', $request->status))
            ->latest();

        $spps = $query->paginate(25);
        return view('admin.spp.index', compact('spps'));
    }

    // Generate tagihan SPP bulanan untuk semua murid aktif
    public function generateBulanan(Request $request)
    {
        $request->validate(['bulan' => 'required|date_format:Y-m', 'nominal' => 'required|numeric']);

        $murids = Murid::where('status_aktif', true)->get();
        foreach ($murids as $murid) {
            Spp::firstOrCreate(
                ['id_murid' => $murid->id_murid, 'bulan_tagihan' => $request->bulan],
                [
                    'nominal_tagihan'   => $request->nominal,
                    'tanggal_jatuh_tempo' => now()->parse($request->bulan)->endOfMonth(),
                    'status_bayar'      => 'belum_bayar',
                ]
            );
        }

        return back()->with('success', 'Tagihan SPP berhasil digenerate.');
    }

    // Validasi bukti bayar murid
    public function validasi(Request $request, Spp $spp)
    {
        $request->validate(['catatan_admin' => 'nullable|string']);

        $admin = Admin::where('id_user', Auth::id())->firstOrFail();

        // Update status SPP
        $spp->update(['status_bayar' => 'sudah_bayar']);

        // Update transaksi
        Transaksi::where('id_spp', $spp->id_spp)->update([
            'id_admin'           => $admin->id_admin,
            'tanggal_konfirmasi' => now(),
            'catatan_admin'      => $request->catatan_admin,
        ]);

        return back()->with('success', 'Pembayaran berhasil divalidasi.');
    }
}
