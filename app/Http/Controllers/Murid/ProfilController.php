<?php

namespace App\Http\Controllers\Murid;

use App\Http\Controllers\Controller;
use App\Models\Murid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfilController extends Controller
{
    public function edit()
    {
        $murid = Murid::where('id_user', Auth::id())->with('user')->firstOrFail();
        return view('murid.profil', compact('murid'));
    }

    public function update(Request $request)
    {
        $murid = Murid::where('id_user', Auth::id())->firstOrFail();
        $request->validate([
            'nama_murid'     => 'required|string',
            'nomor_hp'       => 'nullable|string|max:20',
            'alamat'         => 'nullable|string',
            'nama_orang_tua' => 'nullable|string',
        ]);

        $murid->update($request->only(['nama_murid', 'nomor_hp', 'alamat', 'nama_orang_tua']));
        $murid->user->update(['name' => $request->nama_murid]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed', 'current_password' => 'required|current_password']);
            $murid->user->update(['password' => Hash::make($request->password)]);
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
