<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Murid;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    // Daftar semua murid
    public function indexMurid()
    {
        $murids = Murid::with('user')->latest()->paginate(20);
        return view('admin.murids.index', compact('murids'));
    }

    // Form tambah murid
    public function createMurid()
    {
        return view('admin.murids.create');
    }

    // Simpan murid baru
    public function storeMurid(Request $request)
    {
        $request->validate([
            'username'      => 'required|unique:users',
            'email'         => 'required|email|unique:users',
            'password'      => 'required|min:8',
            'nama_murid'    => 'required|string',
            'tipe_les'      => 'required|in:onsite,home_private',
            'tanggal_lahir' => 'nullable|date',
            'nomor_hp'      => 'nullable|string|max:20',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'username'  => $request->username,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'role'      => 'murid',
                'is_active' => true,
            ]);

            Murid::create([
                'id_user'       => $user->id_user,
                'nama_murid'    => $request->nama_murid,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat'        => $request->alamat,
                'nomor_hp'      => $request->nomor_hp,
                'nama_orang_tua'=> $request->nama_orang_tua,
                'tipe_les'      => $request->tipe_les,
            ]);
        });

        return redirect()->route('admin.murids.index')->with('success', 'Murid berhasil ditambahkan.');
    }

    // Nonaktifkan akun
    public function toggleAktif(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', 'Status akun berhasil diubah.');
    }

    // Daftar semua guru
    public function indexGuru()
    {
        $gurus = Guru::with('user')->latest()->paginate(20);
        return view('admin.gurus.index', compact('gurus'));
    }

    // Simpan guru baru
    public function storeGuru(Request $request)
    {
        $request->validate([
            'username'    => 'required|unique:users',
            'email'       => 'required|email|unique:users',
            'password'    => 'required|min:8',
            'nama_guru'   => 'required|string',
            'spesialisasi'=> 'nullable|string',
            'nomor_hp'    => 'nullable|string|max:20',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'username'  => $request->username,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'role'      => 'guru',
                'is_active' => true,
            ]);

            Guru::create([
                'id_user'     => $user->id_user,
                'nama_guru'   => $request->nama_guru,
                'spesialisasi'=> $request->spesialisasi,
                'nomor_hp'    => $request->nomor_hp,
            ]);
        });

        return redirect()->route('admin.gurus.index')->with('success', 'Guru berhasil ditambahkan.');
    }
}

