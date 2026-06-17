<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HonorGuru;
use App\Models\Guru;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HonorGuruController extends Controller
{
    /**
     * Daftar seluruh record gaji guru (termasuk draft otomatis dari penjadwalan).
     */
    public function index(Request $request)
    {
        $query = HonorGuru::with(['guru', 'jadwals.spp.murid', 'admin']);

        // Filter berdasarkan Guru
        if ($request->filled('id_guru')) {
            $query->where('id_guru', $request->id_guru);
        }

        // Filter berdasarkan Status Pembayaran
        if ($request->filled('status')) {
            $query->where('status_bayar', $request->status);
        }

        // Urutkan dari yang terbaru (draft baru akan muncul di atas)
        $honors = $query->latest('created_at')->paginate(20)->withQueryString();
        
        $gurus = Guru::where('status_aktif', true)->get();

        return view('admin.honor_guru.index', compact('honors', 'gurus'));
    }

    /**
     * Form kelola gaji guru (Edit nilai nominal dan status).
     */
    public function edit(HonorGuru $honorGuru)
    {
        // Muat relasi untuk menampilkan konteks mengajar pada form edit
        $honorGuru->load(['guru', 'jadwals.spp.murid', 'jadwals.spp.programKursus', 'admin']);
        
        return view('admin.honor_guru.edit', compact('honorGuru'));
    }

    /**
     * Simpan pembaruan gaji (Input nominal, catatan, dan bukti transfer).
     */
    public function update(Request $request, HonorGuru $honorGuru)
    {
        $request->validate([
            'jumlah_honor'        => 'required|numeric|min:0',
            'status_bayar'        => 'required|in:Belum Lunas,Siap Dibayar,Lunas',
            'catatan'             => 'nullable|string|max:500',
            'file_bukti_transfer' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $admin = Admin::where('id_user', Auth::id())->firstOrFail();

        $dataUpdate = [
            'id_admin'     => $admin->id_admin,
            'jumlah_honor' => $request->jumlah_honor,
            'status_bayar' => $request->status_bayar,
            'catatan'      => $request->catatan,
        ];

        // Jika status diubah menjadi Lunas, otomatis catat tanggal pencairan
        if ($request->status_bayar === 'Lunas' && !$honorGuru->tanggal_pencairan) {
            $dataUpdate['tanggal_pencairan'] = now()->toDateString();
        } elseif ($request->status_bayar !== 'Lunas') {
            $dataUpdate['tanggal_pencairan'] = null; // Reset jika status dikembalikan
        }

        // Proses unggah file bukti transfer jika ada
        if ($request->hasFile('file_bukti_transfer')) {
            // Hapus file lama jika ada
            if ($honorGuru->file_bukti_transfer && Storage::disk('public')->exists($honorGuru->file_bukti_transfer)) {
                Storage::disk('public')->delete($honorGuru->file_bukti_transfer);
            }
            $dataUpdate['file_bukti_transfer'] = $request->file('file_bukti_transfer')->store('bukti-honor', 'public');
        }

        $honorGuru->update($dataUpdate);

        return redirect()->route('admin.honor-guru.index')
                         ->with('success', 'Data Gaji Guru berhasil diperbarui.');
    }

    /**
     * Hapus record honor jika diperlukan (opsional/pembatalan).
     */
    public function destroy(HonorGuru $honorGuru)
    {
        if ($honorGuru->status_bayar === 'Lunas') {
            return back()->with('error', 'Gaji yang sudah berstatus Lunas tidak dapat dihapus.');
        }

        if ($honorGuru->file_bukti_transfer && Storage::disk('public')->exists($honorGuru->file_bukti_transfer)) {
            Storage::disk('public')->delete($honorGuru->file_bukti_transfer);
        }

        // Hapus kaitan id_honor di tabel jadwal sebelum menghapus honor
        \App\Models\Jadwal::where('id_honor', $honorGuru->id_honor)->update(['id_honor' => null]);
        
        $honorGuru->delete();

        return back()->with('success', 'Draft Gaji Guru berhasil dihapus.');
    }
}