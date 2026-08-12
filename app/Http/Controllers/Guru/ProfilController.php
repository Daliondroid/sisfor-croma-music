<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\GuruSpesialisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    /**
     * Form edit profil guru.
     * Melihat profil pribadi.
     */
    public function edit()
    {
        $guru = Guru::where('id_user', Auth::id())
            ->with(['user', 'spesialisasis'])
            ->firstOrFail();

        return view('guru.profil', compact('guru'));
    }

    /**
     * Update data profil guru.
     * Spesialisasi disimpan sebagai baris HasMany di guru_spesialisasis
     * (sesuai ERD v12 — bukan pivot, tidak ada tabel master spesialisasi).
     */
    public function update(Request $request)
    {
        $guru = Guru::where('id_user', Auth::id())
            ->with(['user', 'spesialisasis'])
            ->firstOrFail();

        $request->validate([
            'nama_guru' => 'required|string|max:100',
            'nomor_hp' => 'nullable|string|max:20',
            'spesialisasi' => 'nullable|array',
            'spesialisasi.*' => 'string|max:100',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::transaction(function () use ($request, $guru) {
            // Update data user
            $userData = ['name' => $request->nama_guru];

            if ($request->hasFile('foto_profil')) {
                // Hapus foto lama
                if ($guru->user->foto_profil
                    && Storage::disk('public')->exists($guru->user->foto_profil)) {
                    Storage::disk('public')->delete($guru->user->foto_profil);
                }
                $userData['foto_profil'] = $request->file('foto_profil')
                    ->store('foto-profil', 'public');
            }

            // Ganti password jika diisi
            if ($request->filled('password')) {
                $request->validate([
                    'current_password' => 'required|current_password',
                    'password' => 'min:8|confirmed',
                ]);
                $userData['password'] = Hash::make($request->password);
            }

            $guru->user->update($userData);
            $guru->update(['nama_guru' => $request->nama_guru, 'nomor_hp' => $request->nomor_hp]);

            // Sync spesialisasi: hapus lama, insert baru
            // (konsisten dengan cara admin di UserController::updateGuru)
            GuruSpesialisasi::where('id_guru', $guru->id_guru)->delete();

            foreach ($request->spesialisasi ?? [] as $nama) {
                if (trim($nama) === '') {
                    continue;
                }
                GuruSpesialisasi::create([
                    'id_guru' => $guru->id_guru,
                    'nama_spesialisasi' => trim($nama),
                ]);
            }
        });

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
