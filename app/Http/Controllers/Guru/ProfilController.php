<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfilController extends Controller
{
    public function edit()
    {
        $guru = Guru::where('id_user', Auth::id())->with('user')->firstOrFail();
        return view('guru.profil', compact('guru'));
    }

    public function update(Request $request)
    {
        $guru = Guru::where('id_user', Auth::id())->firstOrFail();
        $request->validate([
            'nama_guru'    => 'required|string',
            'nomor_hp'     => 'nullable|string|max:20',
            'spesialisasi' => 'nullable|string',
        ]);

        $guru->update($request->only(['nama_guru', 'nomor_hp', 'spesialisasi']));
        $guru->user->update(['name' => $request->nama_guru]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed', 'current_password' => 'required|current_password']);
            $guru->user->update(['password' => Hash::make($request->password)]);
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}