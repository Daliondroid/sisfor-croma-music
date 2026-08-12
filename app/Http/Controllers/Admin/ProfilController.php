<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    public function edit()
    {
        /** @var User $user */ // Bagian ini ditambahkan
        $user = Auth::user();
        $admin = Admin::where('id_user', $user->id_user)->firstOrFail();

        return view('admin.profil', compact('user', 'admin'));
    }

    public function update(Request $request)
    {
        /** @var User $user */ // Bagian ini ditambahkan
        $user = Auth::user();
        $admin = Admin::where('id_user', $user->id_user)->firstOrFail();

        $request->validate([
            'nama_admin' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,'.$user->id_user.',id_user',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $userData = [
            'name' => $request->nama_admin,
            'email' => $request->email,
        ];

        if ($request->hasFile('foto_profil')) {
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            $userData['foto_profil'] = $request->file('foto_profil')
                ->store('foto-profil', 'public');
        }

        if ($request->filled('password')) {
            $request->validate([
                'current_password' => 'required|current_password',
                'password' => 'min:8|confirmed',
            ]);
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);
        $admin->update(['nama_admin' => $request->nama_admin]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
