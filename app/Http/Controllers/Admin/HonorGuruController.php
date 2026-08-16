<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Guru;
use App\Models\HonorGuru;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HonorGuruController extends Controller
{
    /**
     * Daftar seluruh record gaji guru (termasuk draft otomatis dari penjadwalan).
     */
    public function index(Request $request)
    {
        $query = HonorGuru::with(['guru', 'jadwals.spp.murid', 'admin']);

        if ($request->filled('id_guru')) {
            $query->where('id_guru', $request->id_guru);
        }

        if ($request->filled('status')) {
            $query->where('status_bayar', $request->status);
        }

        $honors = $query->latest('created_at')->paginate(20)->withQueryString();
        $gurus = Guru::where('status_aktif', true)->get();

        return view('admin.honor_guru.index', compact('honors', 'gurus'));
    }

    /**
     * Form kelola gaji guru (Edit nilai nominal dan status).
     */
    public function edit(HonorGuru $honorGuru)
    {
        $honorGuru->load(['guru', 'jadwals.spp.murid', 'jadwals.spp.programKursus', 'admin']);

        return view('admin.honor_guru.edit', compact('honorGuru'));
    }

    /**
     * Simpan pembaruan gaji (Input nominal, catatan, dan bukti transfer).
     */
    public function update(Request $request, HonorGuru $honorGuru)
    {
        $request->validate([
            'jumlah_honor' => 'required|numeric|min:0',
            'status_bayar' => 'required|in:Belum Lunas,Siap Dibayar,Lunas',
            'catatan' => 'nullable|string|max:500',
            'file_bukti_transfer' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $admin = Admin::where('id_user', Auth::id())->firstOrFail();

        DB::transaction(function () use ($request, $honorGuru, $admin) {
            $lockedHonor = HonorGuru::where('id_honor', $honorGuru->id_honor)->lockForUpdate()->firstOrFail();

            $dataUpdate = [
                'id_admin' => $admin->id_admin,
                'jumlah_honor' => $request->jumlah_honor,
                'status_bayar' => $request->status_bayar,
                'catatan' => $request->catatan,
            ];

            if ($request->status_bayar === 'Lunas' && ! $lockedHonor->tanggal_pencairan) {
                $dataUpdate['tanggal_pencairan'] = now()->toDateString();
            } elseif ($request->status_bayar !== 'Lunas') {
                $dataUpdate['tanggal_pencairan'] = null;
            }

            if ($request->hasFile('file_bukti_transfer')) {
                if ($lockedHonor->file_bukti_transfer && Storage::disk('local')->exists($lockedHonor->file_bukti_transfer)) {
                    Storage::disk('local')->delete($lockedHonor->file_bukti_transfer);
                }
                $dataUpdate['file_bukti_transfer'] = $request->file('file_bukti_transfer')->store('bukti-honor', 'local');
            }

            $lockedHonor->update($dataUpdate);
        });

        return redirect()->route('admin.honor-guru.index')
            ->with('success', 'Data Gaji Guru berhasil diperbarui.');
    }

    /**
     * Hapus record honor jika diperlukan (opsional/pembatalan).
     */
    public function destroy(HonorGuru $honorGuru)
    {
        $result = DB::transaction(function () use ($honorGuru) {
            $lockedHonor = HonorGuru::where('id_honor', $honorGuru->id_honor)->lockForUpdate()->firstOrFail();

            if ($lockedHonor->status_bayar === 'Lunas') {
                return ['status' => 'error', 'message' => 'Gaji yang sudah berstatus Lunas tidak dapat dihapus.'];
            }

            if ($lockedHonor->file_bukti_transfer && Storage::disk('local')->exists($lockedHonor->file_bukti_transfer)) {
                Storage::disk('local')->delete($lockedHonor->file_bukti_transfer);
            }

            Jadwal::where('id_honor', $lockedHonor->id_honor)->update(['id_honor' => null]);

            $lockedHonor->delete();

            return ['status' => 'success', 'message' => 'Draft Gaji Guru berhasil dihapus.'];
        });

        return back()->with($result['status'], $result['message']);
    }

    /**
     * Stream payment proof file securely to authenticated admin.
     */
    public function viewBukti(HonorGuru $honorGuru)
    {
        $path = $honorGuru->file_bukti_transfer;

        abort_unless($path && Storage::disk('local')->exists($path), 404, 'File bukti transfer gaji tidak ditemukan.');

        $mimeType = Storage::disk('local')->mimeType($path);
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        return response()->stream(function () use ($path) {
            echo Storage::disk('local')->get($path);
        }, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="bukti_honor.'.$extension.'"',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
