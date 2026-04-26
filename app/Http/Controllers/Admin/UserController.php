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
    // ── MURID ────────────────────────────────────────────────
    public function indexMurid(Request $request)
    {
        $murids = Murid::with('user')
            ->when($request->search, fn($q) => $q->where('nama_murid', 'like', "%{$request->search}%")
                ->orWhereHas('user', fn($u) => $u->where('email', 'like', "%{$request->search}%")))
            ->when($request->tipe,   fn($q) => $q->where('tipe_les', $request->tipe))
            ->when($request->status !== null && $request->status !== '',
                fn($q) => $q->where('status_aktif', $request->status))
            ->latest()->paginate(20);
        return view('admin.murids.index', compact('murids'));
    }

    public function createMurid()
    {
        return view('admin.murids.create');
    }

    public function storeMurid(Request $request)
    {
        $request->validate([
            'username'      => 'required|unique:users',
            'email'         => 'required|email|unique:users',
            'password'      => 'required|min:8|confirmed',
            'nama_murid'    => 'required|string',
            'tipe_les'      => 'required|in:onsite,home_private',
            'tanggal_lahir' => 'nullable|date',
            'nomor_hp'      => 'nullable|string|max:20',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'username'  => $request->username,
                'name'      => $request->nama_murid,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'role'      => 'murid',
                'is_active' => true,
            ]);
            Murid::create([
                'id_user'        => $user->id_user,
                'nama_murid'     => $request->nama_murid,
                'tanggal_lahir'  => $request->tanggal_lahir,
                'alamat'         => $request->alamat,
                'nomor_hp'       => $request->nomor_hp,
                'nama_orang_tua' => $request->nama_orang_tua,
                'tipe_les'       => $request->tipe_les,
            ]);
        });

        return redirect()->route('admin.murids.index')->with('success', 'Murid berhasil ditambahkan.');
    }

    public function editMurid(Murid $murid)
    {
        $murid->load('user');
        return view('admin.murids.edit', compact('murid'));
    }

    public function updateMurid(Request $request, Murid $murid)
    {
        $request->validate([
            'nama_murid'    => 'required|string',
            'tipe_les'      => 'required|in:onsite,home_private',
            'tanggal_lahir' => 'nullable|date',
            'nomor_hp'      => 'nullable|string|max:20',
            'email'         => 'required|email|unique:users,email,'.$murid->id_user.',id_user',
        ]);

        DB::transaction(function () use ($request, $murid) {
            $murid->user->update(['email' => $request->email, 'name' => $request->nama_murid]);
            $murid->update($request->only([
                'nama_murid', 'tanggal_lahir', 'alamat', 'nomor_hp', 'nama_orang_tua', 'tipe_les'
            ]));
            if ($request->filled('password')) {
                $request->validate(['password' => 'min:8|confirmed']);
                $murid->user->update(['password' => Hash::make($request->password)]);
            }
        });

        return redirect()->route('admin.murids.index')->with('success', 'Data murid berhasil diperbarui.');
    }

    // ── GURU ─────────────────────────────────────────────────
    public function indexGuru(Request $request)
    {
        $gurus = Guru::with('user')
            ->when($request->search, fn($q) => $q->where('nama_guru', 'like', "%{$request->search}%"))
            ->when($request->status !== null && $request->status !== '',
                fn($q) => $q->where('status_aktif', $request->status))
            ->latest()->paginate(20);
        return view('admin.gurus.index', compact('gurus'));
    }

    public function createGuru()
    {
        return view('admin.gurus.create');
    }

    public function storeGuru(Request $request)
    {
        $request->validate([
            'username'    => 'required|unique:users',
            'email'       => 'required|email|unique:users',
            'password'    => 'required|min:8|confirmed',
            'nama_guru'   => 'required|string',
            'spesialisasi'=> 'nullable|string',
            'nomor_hp'    => 'nullable|string|max:20',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'username'  => $request->username,
                'name'      => $request->nama_guru,
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

    public function editGuru(Guru $guru)
    {
        $guru->load('user');
        return view('admin.gurus.edit', compact('guru'));
    }

    public function updateGuru(Request $request, Guru $guru)
    {
        $request->validate([
            'nama_guru'   => 'required|string',
            'spesialisasi'=> 'nullable|string',
            'nomor_hp'    => 'nullable|string|max:20',
            'email'       => 'required|email|unique:users,email,'.$guru->id_user.',id_user',
        ]);

        DB::transaction(function () use ($request, $guru) {
            $guru->user->update(['email' => $request->email, 'name' => $request->nama_guru]);
            $guru->update($request->only(['nama_guru', 'spesialisasi', 'nomor_hp']));
            if ($request->filled('password')) {
                $request->validate(['password' => 'min:8|confirmed']);
                $guru->user->update(['password' => Hash::make($request->password)]);
            }
        });

        return redirect()->route('admin.gurus.index')->with('success', 'Data guru berhasil diperbarui.');
    }

    public function toggleAktif(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        if ($user->murid) $user->murid->update(['status_aktif' => $user->is_active]);
        if ($user->guru)  $user->guru->update(['status_aktif'  => $user->is_active]);
        return back()->with('success', 'Status akun berhasil diubah.');
    }

    public function destroyMurid(Murid $murid)
    {
        $nama = $murid->nama_murid;
        DB::transaction(function () use ($murid) {
            $user = $murid->user;
            $murid->delete();
            $user?->delete();
        });
        return redirect()->route('admin.murids.index')
            ->with('success', "Akun murid \"{$nama}\" berhasil dihapus.");
    }

    /**
     * Hapus akun guru beserta user-nya (hanya admin).
     */
    public function destroyGuru(Guru $guru)
    {
        $nama = $guru->nama_guru;
        DB::transaction(function () use ($guru) {
            $user = $guru->user;
            $guru->delete();
            $user?->delete();
        });
        return redirect()->route('admin.gurus.index')
            ->with('success', "Akun guru \"{$nama}\" berhasil dihapus.");
    }
}