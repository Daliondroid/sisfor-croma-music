<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Murid;
use App\Models\Guru;
use App\Models\Spesialisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // ── MURID ────────────────────────────────────────────────
    public function indexMurid(Request $request)
    {
        $murids = Murid::with('user')
            ->when($request->search, fn($q) => $q->where('nama_murid', 'like', "%{$request->search}%")
                ->orWhereHas('user', fn($u) => $u->where('email', 'like', "%{$request->search}%")))
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
            'tanggal_lahir' => 'nullable|date',
            'nomor_hp'      => 'nullable|string|max:20',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::transaction(function () use ($request) {
            $path = null;
            if ($request->hasFile('foto_profil')) {
                $path = $request->file('foto_profil')->store('foto-profil', 'public');
            }

            $user = User::create([
                'username'  => $request->username,
                'name'      => $request->nama_murid,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'role'      => 'murid',
                'is_active' => true,
                'foto_profil' => $path,
            ]);
            Murid::create([
                'id_user'        => $user->id_user,
                'nama_murid'     => $request->nama_murid,
                'tanggal_lahir'  => $request->tanggal_lahir,
                'alamat'         => $request->alamat,
                'nomor_hp'       => $request->nomor_hp,
                'nama_orang_tua' => $request->nama_orang_tua,
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
            'tanggal_lahir' => 'nullable|date',
            'nomor_hp'      => 'nullable|string|max:20',
            'email'         => 'required|email|unique:users,email,'.$murid->id_user.',id_user',
            'foto_profil'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::transaction(function () use ($request, $murid) {
            $murid->user->update(['email' => $request->email, 'name' => $request->nama_murid]);

            if ($request->hasFile('foto_profil')) {
                // Hapus foto lama dari storage jika ada
                if ($murid->user->foto_profil && Storage::disk('public')->exists($murid->user->foto_profil)) {
                    Storage::disk('public')->delete($murid->user->foto_profil);
                }
                // Simpan foto baru
                $path = $request->file('foto_profil')->store('foto-profil', 'public');
                $murid->user->update(['foto_profil' => $path]);
            }

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
        $gurus = Guru::with('user', 'spesialisasis') // Tambahkan eager loading spesialisasis
            ->when($request->search, fn($q) => $q->where('nama_guru', 'like', "%{$request->search}%"))
            ->when($request->status !== null && $request->status !== '',
                fn($q) => $q->where('status_aktif', $request->status))
            ->latest()->paginate(20);

        // Ambil semua data spesialisasi untuk ditampilkan di card management
        $spesialisasis = \App\Models\Spesialisasi::all(); 

        return view('admin.gurus.index', compact('gurus', 'spesialisasis'));
    }

    public function createGuru()
    {
        $spesialisasis = Spesialisasi::all(); // Ambil semua instrumen
        return view('admin.gurus.create', compact('spesialisasis'));
    }

    public function storeGuru(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'username'      => 'required|unique:users',
            'email'         => 'required|email|unique:users',
            'password'      => 'required|min:8|confirmed',
            'nama_guru'     => 'required|string',
            'spesialisasi_ids' => 'required|array', // Mengganti spesialisasi string ke array ID
            'spesialisasi_ids.*' => 'exists:spesialisasi,id_spesialisasi',
            'nomor_hp'      => 'nullable|string|max:20',
            'foto_profil'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::transaction(function () use ($request) {
            // 2. Proses Upload Foto Profil
            $path = null;
            if ($request->hasFile('foto_profil')) {
                $path = $request->file('foto_profil')->store('foto-profil', 'public');
            }

            // 3. Simpan Data ke Tabel Users
            $user = User::create([
                'username'    => $request->username,
                'name'        => $request->nama_guru,
                'email'       => $request->email,
                'password'    => Hash::make($request->password),
                'role'        => 'guru',
                'is_active'   => true,
                'foto_profil' => $path,
            ]);

            // 4. Simpan Data ke Tabel Gurus (Tanpa kolom spesialisasi)
            $guru = Guru::create([
                'id_user'   => $user->id_user,
                'nama_guru' => $request->nama_guru,
                'nomor_hp'  => $request->nomor_hp,
            ]);

            // 5. Hubungkan Guru dengan Spesialisasi di Tabel Pivot
            $guru->spesialisasis()->attach($request->spesialisasi_ids);
        });

        return redirect()->route('admin.gurus.index')->with('success', 'Guru berhasil ditambahkan.');
    }

    public function editGuru(Guru $guru)
    {
        $guru->load('user', 'spesialisasis');
        $spesialisasis = Spesialisasi::all();
        return view('admin.gurus.edit', compact('guru', 'spesialisasis'));
    }

    public function updateGuru(Request $request, Guru $guru)
    {
        // 1. Validasi Input
        $request->validate([
            'nama_guru'     => 'required|string',
            'spesialisasi_ids' => 'required|array', // Mengganti spesialisasi string ke array ID
            'spesialisasi_ids.*' => 'exists:spesialisasi,id_spesialisasi',
            'nomor_hp'      => 'nullable|string|max:20',
            'email'         => 'required|email|unique:users,email,'.$guru->id_user.',id_user',
            'foto_profil'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::transaction(function () use ($request, $guru) {
            
            // 2. Siapkan data untuk diupdate ke tabel users
            $userData = [
                'email' => $request->email, 
                'name'  => $request->nama_guru
            ];
            
            // 3. Proses Foto Profil (Jika ada unggahan baru)
            if ($request->hasFile('foto_profil')) {
                if ($guru->user->foto_profil) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($guru->user->foto_profil);
                }
                $userData['foto_profil'] = $request->file('foto_profil')->store('foto-profil', 'public');
            }

            // 4. Proses Password (Jika diisi)
            if ($request->filled('password')) {
                $request->validate(['password' => 'min:8|confirmed']);
                $userData['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
            }

            // 5. Update data ke tabel users
            $guru->user->update($userData);

            // 6. Update data ke tabel gurus (Hapus 'spesialisasi' dari request->only)
            $guru->update($request->only(['nama_guru', 'nomor_hp']));

            // 7. Sinkronisasi Data Spesialisasi di Tabel Pivot
            $guru->spesialisasis()->sync($request->spesialisasi_ids);
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