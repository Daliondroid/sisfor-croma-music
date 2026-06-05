<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Murid;
use App\Models\Guru;
use App\Models\GuruSpesialisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // ══════════════════════════════════════════════════════════════
    //  MURID
    // ══════════════════════════════════════════════════════════════

    public function indexMurid(Request $request)
    {
        $murids = Murid::with('user')
            ->when(
                $request->search,
                fn($q) => $q->where('nama_murid', 'like', "%{$request->search}%")
                    ->orWhereHas('user', fn($u) => $u->where('email', 'like', "%{$request->search}%"))
            )
            ->when(
                $request->status !== null && $request->status !== '',
                fn($q) => $q->where('status_aktif', $request->status)
            )
            ->latest()
            ->paginate(20);

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
            'tanggal_lahir' => 'nullable|date',
            'nomor_hp'      => 'nullable|string|max:20',
            'alamat'        => 'nullable|string',
            'nama_orang_tua'=> 'nullable|string',
            'foto_profil'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::transaction(function () use ($request) {
            $path = null;
            if ($request->hasFile('foto_profil')) {
                $path = $request->file('foto_profil')->store('foto-profil', 'public');
            }

            $user = User::create([
                'username'    => $request->username,
                'name'        => $request->nama_murid,
                'email'       => $request->email,
                'password'    => Hash::make($request->password),
                'role'        => 'murid',
                'is_active'   => true,
                'foto_profil' => $path,
            ]);

            Murid::create([
                'id_user'        => $user->id_user,
                'nama_murid'     => $request->nama_murid,
                'tanggal_lahir'  => $request->tanggal_lahir,
                'alamat'         => $request->alamat,
                'nomor_hp'       => $request->nomor_hp,
                'nama_orang_tua' => $request->nama_orang_tua,
                'status_aktif'   => true,
            ]);
        });

        return redirect()->route('admin.murids.index')
            ->with('success', 'Murid berhasil ditambahkan.');
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
            'tanggal_lahir' => 'nullable|date',
            'nomor_hp'      => 'nullable|string|max:20',
            'alamat'        => 'nullable|string',
            'nama_orang_tua'=> 'nullable|string',
            'email'         => 'required|email|unique:users,email,' . $murid->id_user . ',id_user',
            'foto_profil'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::transaction(function () use ($request, $murid) {
            $userData = ['email' => $request->email, 'name' => $request->nama_murid];

            if ($request->hasFile('foto_profil')) {
                if ($murid->user->foto_profil
                    && Storage::disk('public')->exists($murid->user->foto_profil)) {
                    Storage::disk('public')->delete($murid->user->foto_profil);
                }
                $userData['foto_profil'] = $request->file('foto_profil')
                    ->store('foto-profil', 'public');
            }

            if ($request->filled('password')) {
                $request->validate(['password' => 'min:8|confirmed']);
                $userData['password'] = Hash::make($request->password);
            }

            $murid->user->update($userData);
            $murid->update($request->only([
                'nama_murid', 'tanggal_lahir', 'alamat',
                'nomor_hp', 'nama_orang_tua',
            ]));
        });

        return redirect()->route('admin.murids.index')
            ->with('success', 'Data murid berhasil diperbarui.');
    }

    public function destroyMurid(Murid $murid)
    {
        $nama = $murid->nama_murid;

        DB::transaction(function () use ($murid) {
            $user = $murid->user;
            // Soft-delete: nonaktifkan saja, jangan cascade hapus jadwal/transaksi
            $murid->update(['status_aktif' => false]);
            $user?->update(['is_active' => false]);
        });

        return redirect()->route('admin.murids.index')
            ->with('success', "Akun murid \"{$nama}\" berhasil dinonaktifkan.");
    }

    // ══════════════════════════════════════════════════════════════
    //  GURU
    // ══════════════════════════════════════════════════════════════

    public function indexGuru(Request $request)
    {
        $gurus = Guru::with(['user', 'spesialisasis'])
            ->when(
                $request->search,
                fn($q) => $q->where('nama_guru', 'like', "%{$request->search}%")
                    ->orWhereHas('user', fn($u) => $u->where('email', 'like', "%{$request->search}%"))
            )
            ->when(
                $request->status !== null && $request->status !== '',
                fn($q) => $q->where('status_aktif', $request->status)
            )
            ->latest()
            ->paginate(20);

        return view('admin.gurus.index', compact('gurus'));
    }

    public function createGuru()
    {
        return view('admin.gurus.create');
    }

    /**
     * Simpan akun guru baru.
     * Spesialisasi: admin bisa pilih lebih dari 1; disimpan sebagai
     * baris terpisah di guru_spesialisasis (HasMany, bukan pivot).
     */
    public function storeGuru(Request $request)
    {
        $request->validate([
            'username'         => 'required|unique:users',
            'email'            => 'required|email|unique:users',
            'password'         => 'required|min:8|confirmed',
            'nama_guru'        => 'required|string',
            'nomor_hp'         => 'nullable|string|max:20',
            'spesialisasi'     => 'required|array|min:1',
            'spesialisasi.*'   => 'required|string|max:100',
            'foto_profil'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::transaction(function () use ($request) {
            $path = null;
            if ($request->hasFile('foto_profil')) {
                $path = $request->file('foto_profil')->store('foto-profil', 'public');
            }

            $user = User::create([
                'username'    => $request->username,
                'name'        => $request->nama_guru,
                'email'       => $request->email,
                'password'    => Hash::make($request->password),
                'role'        => 'guru',
                'is_active'   => true,
                'foto_profil' => $path,
            ]);

            $guru = Guru::create([
                'id_user'     => $user->id_user,
                'nama_guru'   => $request->nama_guru,
                'nomor_hp'    => $request->nomor_hp,
                'status_aktif'=> true,
            ]);

            // Simpan setiap spesialisasi sebagai baris baru
            foreach ($request->spesialisasi as $nama) {
                if (trim($nama) === '') {
                    continue;
                }
                GuruSpesialisasi::create([
                    'id_guru'           => $guru->id_guru,
                    'nama_spesialisasi' => trim($nama),
                ]);
            }
        });

        return redirect()->route('admin.gurus.index')
            ->with('success', 'Guru berhasil ditambahkan.');
    }

    public function editGuru(Guru $guru)
    {
        $guru->load('user', 'spesialisasis');
        return view('admin.gurus.edit', compact('guru'));
    }

    /**
     * Update data guru.
     * Spesialisasi: hapus semua yang lama, lalu insert ulang daftar baru.
     * Ini memungkinkan admin menambah / menghapus / mengubah spesialisasi secara bebas.
     */
    public function updateGuru(Request $request, Guru $guru)
    {
        $request->validate([
            'nama_guru'        => 'required|string',
            'nomor_hp'         => 'nullable|string|max:20',
            'email'            => 'required|email|unique:users,email,' . $guru->id_user . ',id_user',
            'spesialisasi'     => 'required|array|min:1',
            'spesialisasi.*'   => 'required|string|max:100',
            'foto_profil'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::transaction(function () use ($request, $guru) {
            $userData = ['email' => $request->email, 'name' => $request->nama_guru];

            if ($request->hasFile('foto_profil')) {
                if ($guru->user->foto_profil
                    && Storage::disk('public')->exists($guru->user->foto_profil)) {
                    Storage::disk('public')->delete($guru->user->foto_profil);
                }
                $userData['foto_profil'] = $request->file('foto_profil')
                    ->store('foto-profil', 'public');
            }

            if ($request->filled('password')) {
                $request->validate(['password' => 'min:8|confirmed']);
                $userData['password'] = Hash::make($request->password);
            }

            $guru->user->update($userData);
            $guru->update($request->only(['nama_guru', 'nomor_hp']));

            // Sync spesialisasi: hapus lama, insert baru
            GuruSpesialisasi::where('id_guru', $guru->id_guru)->delete();

            foreach ($request->spesialisasi as $nama) {
                if (trim($nama) === '') {
                    continue;
                }
                GuruSpesialisasi::create([
                    'id_guru'           => $guru->id_guru,
                    'nama_spesialisasi' => trim($nama),
                ]);
            }
        });

        return redirect()->route('admin.gurus.index')
            ->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroyGuru(Guru $guru)
    {
        $nama = $guru->nama_guru;

        DB::transaction(function () use ($guru) {
            $user = $guru->user;
            // Soft-delete: nonaktifkan saja, pertahankan historis jadwal/honor
            $guru->update(['status_aktif' => false]);
            $user?->update(['is_active' => false]);
        });

        return redirect()->route('admin.gurus.index')
            ->with('success', "Akun guru \"{$nama}\" berhasil dinonaktifkan.");
    }

    // ══════════════════════════════════════════════════════════════
    //  TOGGLE AKTIF
    // ══════════════════════════════════════════════════════════════

    /**
     * Toggle is_active pada user dan flag status_aktif pada murid/guru terkait.
     */
    public function toggleAktif(User $user)
    {
        $newStatus = ! $user->is_active;

        $user->update(['is_active' => $newStatus]);

        if ($user->murid) {
            $user->murid->update(['status_aktif' => $newStatus]);
        }

        if ($user->guru) {
            $user->guru->update(['status_aktif' => $newStatus]);
        }

        $label = $newStatus ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Status akun berhasil {$label}.");
    }
}