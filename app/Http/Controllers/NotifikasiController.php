<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasis = Notifikasi::where('id_user', Auth::id())
            ->latest()
            ->paginate(20);

        // Tandai semua sebagai sudah dibaca
        Notifikasi::where('id_user', Auth::id())
            ->where('status_baca', 'belum_dibaca')
            ->update(['status_baca' => 'sudah_dibaca']);

        return view('notifikasi.index', compact('notifikasis'));
    }
}
