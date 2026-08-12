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

        // Tandai hanya yang tampil di halaman ini sebagai sudah dibaca
        $ids = $notifikasis->modelKeys();
        if (! empty($ids)) {
            Notifikasi::whereIn((new Notifikasi)->getKeyName(), $ids)
                ->where('status_baca', 'belum_dibaca')
                ->update(['status_baca' => 'sudah_dibaca']);
        }

        return view('notifikasi.index', compact('notifikasis'));
    }
}
