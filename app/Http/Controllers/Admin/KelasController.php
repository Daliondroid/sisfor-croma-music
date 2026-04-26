<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\ProgramKursus;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::with('program')->latest()->paginate(20);
        return view('admin.kelas.index', compact('kelas'));
    }

    public function create()
    {
        $programs = ProgramKursus::where('is_active', true)->get();
        return view('admin.kelas.create', compact('programs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_program' => 'required|exists:program_kursus,id_program',
            'nama_kelas' => 'required|string|max:100',
            'kapasitas'  => 'required|integer|min:1',
            'tipe_les'   => 'required|in:onsite,home_private',
        ]);

        Kelas::create($request->only(['id_program', 'nama_kelas', 'kapasitas', 'tipe_les']));
        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit(Kelas $kelas)
    {
        $programs = ProgramKursus::where('is_active', true)->get();
        return view('admin.kelas.edit', compact('kelas', 'programs'));
    }

    public function update(Request $request, Kelas $kelas)
    {
        $request->validate([
            'id_program' => 'required|exists:program_kursus,id_program',
            'nama_kelas' => 'required|string|max:100',
            'kapasitas'  => 'required|integer|min:1',
            'tipe_les'   => 'required|in:onsite,home_private',
        ]);

        $kelas->update($request->only(['id_program', 'nama_kelas', 'kapasitas', 'tipe_les']));
        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas)
    {
        // Cek apakah kelas sedang dipakai jadwal aktif
        if ($kelas->jadwals()->where('is_active', true)->exists()) {
            return back()->with('error', 'Kelas tidak dapat dihapus karena masih digunakan jadwal aktif.');
        }
        $kelas->delete();
        return back()->with('success', 'Kelas berhasil dihapus.');
    }
}
