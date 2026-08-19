<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Satker;
use App\Models\User;
use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    /**
     * Menampilkan daftar Satuan Kerja (Satker)
     * URL: http://sapa.test/pengguna/satker
     */
    public function satker()
    {
        // Mengambil data Satker beserta total user/admin di dalamnya
        $satkers = Satker::withCount('User')->get();

        return view('Pengguna.Satker.index', [
            'title'   => 'Daftar Satuan Kerja - SAPA MS ACEH',
            'satkers' => $satkers,
        ]);
    }

    /**
     * Menampilkan daftar Pengguna/Admin dari setiap Satker
     * URL: http://sapa.test/pengguna/admin
     */
    public function admin(Request $request)
    {
        $query = User::with(['satker', 'role']);

        // Filter opsional berdasarkan Satker jika ada pencarian/pilihan
        if ($request->has('satker_id') && $request->satker_id != '') {
            $query->where('satker_id', $request->satker_id);
        }

        $users = $query->latest()->get();
        $satkers = Satker::all();

        return view('Pages.Pengguna.admin', [
            'title'   => 'Daftar Pengguna Satker - SAPA MS ACEH',
            'users'   => $users,
            'satkers' => $satkers,
            'selectedSatker' => $request->satker_id,
        ]);
    }
}