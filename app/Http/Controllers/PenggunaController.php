<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Satker;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PenggunaController extends Controller
{
    // Menampilkan Halaman Profil Pengguna
    public function profile()
    {
        $user = Auth::user();
        return view('Pages.Pengguna.profile', compact('user'));
    }

    // Memperbarui Data Profil Pengguna
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'name'    => 'required|string|max:255',
            'nip'     => 'nullable|string|max:30',
            'jabatan' => 'nullable|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'avatar'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = [
            'name'    => $request->name,
            'nip'     => $request->nip,
            'jabatan' => $request->jabatan,
            'phone'   => $request->phone,
        ];

        // Proses unggah avatar: simpan file ke folder dan hanya catat namanya ke database
        if ($request->hasFile('avatar')) {
            $destinationPath = public_path('assets/images/profile');

            // Hapus file avatar lama jika ada
            if ($user->avatar && file_exists($destinationPath . '/' . $user->avatar)) {
                @unlink($destinationPath . '/' . $user->avatar);
            }

            // Buat nama file unik
            $file = $request->file('avatar');
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Pindahkan file ke folder tujuan
            $file->move($destinationPath, $fileName);

            // Simpan HANYA NAMA FILE ke database
            $data['avatar'] = $fileName;
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function satkerProfile()
    {
        $user = Auth::user();
        
        // Pastikan user memiliki relasi ke Satker
        if (!$user->satker_id) {
            return redirect()->back()->with('error', 'Akun Anda tidak terhubung dengan Satuan Kerja manapun.');
        }

        $satker = Satker::findOrFail($user->satker_id);

        return view('Pages.Pengguna.satker-profile', compact('satker'));
    }

    // Memperbarui Data Satker milik User Login
    public function updateSatkerProfile(Request $request)
    {
        $user = Auth::user();

        if (!$user->satker_id) {
            return redirect()->back()->with('error', 'Akses ditolak. Akun Anda tidak terhubung dengan Satuan Kerja.');
        }

        $satker = Satker::findOrFail($user->satker_id);

        $request->validate([
            'satker_name'       => 'required|string|max:255',
            'satker_short_name' => 'required|string|max:100',
            'email'             => 'nullable|email|max:255',
            'telepon'           => 'nullable|string|max:50',
            'whatsapp'          => 'nullable|string|max:50',
            'alamat'            => 'nullable|string',
            'logo'              => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = [
            'satker_name'       => $request->satker_name,
            'satker_short_name' => $request->satker_short_name,
            'email'             => $request->email,
            'telepon'           => $request->telepon,
            'whatsapp'          => $request->whatsapp,
            'alamat'            => $request->alamat,
        ];

        // Proses Upload Logo ke public/assets/images/satker
        if ($request->hasFile('logo')) {
            $destinationPath = public_path('assets/images/satker');

            // Hapus logo lama jika ada (dan bukan default 'logo.png')
            if ($satker->logo && $satker->logo !== 'logo.png') {
                $oldFilePath = $destinationPath . '/' . $satker->logo;
                if (file_exists($oldFilePath)) {
                    @unlink($oldFilePath);
                }
            }

            // Generate nama file unik
            $file = $request->file('logo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Pindahkan file fisik ke public/assets/images/satker
            $file->move($destinationPath, $filename);

            // Simpan hanya NAMA FILE ke database
            $data['logo'] = $filename;
        }

        $satker->update($data);

        return redirect()->back()->with('success', 'Data Satuan Kerja berhasil diperbarui!');
    }
}