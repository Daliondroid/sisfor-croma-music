<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Spesialisasi;
use Illuminate\Http\Request;

class SpesialisasiController extends Controller
{
    public function index()
    {
        $spesialisasis = Spesialisasi::all();
        return view('admin.spesialisasi.index', compact('spesialisasis'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama_spesialisasi' => 'required|unique:spesialisasi,nama_spesialisasi']);
        Spesialisasi::create($request->all());
        return back()->with('success', 'Instrumen berhasil ditambahkan!');
    }

    public function update(Request $request, Spesialisasi $spesialisasi)
    {
        $request->validate(['nama_spesialisasi' => 'required|unique:spesialisasi,nama_spesialisasi,'.$spesialisasi->id_spesialisasi.',id_spesialisasi']);
        $spesialisasi->update($request->all());
        return back()->with('success', 'Spesialisasi berhasil diubah.');
    }

    public function destroy(Spesialisasi $spesialisasi)
    {
        // Cek apakah ada guru yang masih menggunakan spesialisasi ini
        if ($spesialisasi->gurus()->count() > 0) {
            return back()->with('error', 'Tidak bisa dihapus karena masih digunakan oleh guru.');
        }
        $spesialisasi->delete();
        return back()->with('success', 'Instrumen berhasil dihapus.');
    }
}