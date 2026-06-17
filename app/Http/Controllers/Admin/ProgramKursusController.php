<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramKursus;
use Illuminate\Http\Request;

class ProgramKursusController extends Controller
{
    public function index()
    {
        $programs = ProgramKursus::withCount('spps')
            ->latest()
            ->paginate(20);

        return view('admin.program_kursus.index', compact('programs'));
    }

    public function create()
    {
        return view('admin.program_kursus.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_program' => 'required|string|max:100',
            'deskripsi'    => 'nullable|string',
            'tipe_les'     => 'required|in:onsite,home_private,keduanya',
            'biaya_kursus' => 'required|numeric|min:0',
        ]);

        ProgramKursus::create([
            'nama_program' => $request->nama_program,
            'deskripsi'    => $request->deskripsi,
            'tipe_les'     => $request->tipe_les,
            'biaya_kursus' => $request->biaya_kursus,
            'is_active'    => true,
        ]);

        return redirect()->route('admin.program-kursus.index')
            ->with('success', 'Program kursus berhasil ditambahkan.');
    }

    public function edit(ProgramKursus $programKursus)
    {
        return view('admin.program_kursus.edit', ['program' => $programKursus]);
    }

    public function update(Request $request, ProgramKursus $programKursus)
    {
        $request->validate([
            'nama_program' => 'required|string|max:100',
            'deskripsi'    => 'nullable|string',
            'tipe_les'     => 'required|in:onsite,home_private,keduanya',
            'biaya_kursus' => 'required|numeric|min:0',
        ]);

        $programKursus->update([
            'nama_program' => $request->nama_program,
            'deskripsi'    => $request->deskripsi,
            'tipe_les'     => $request->tipe_les,
            'biaya_kursus' => $request->biaya_kursus,
            'is_active'    => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.program-kursus.index')
            ->with('success', 'Program kursus berhasil diperbarui.');
    }

    public function destroy(ProgramKursus $programKursus)
    {
        // Cegah hapus jika masih ada SPP yang merujuk ke program ini
        if ($programKursus->spps()->exists()) {
            return back()->with(
                'error',
                'Program tidak dapat dihapus karena masih memiliki tagihan SPP terdaftar.'
            );
        }

        $programKursus->delete();

        return back()->with('success', 'Program kursus berhasil dihapus.');
    }
}
