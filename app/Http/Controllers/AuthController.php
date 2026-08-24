<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
 use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showAuthForm()
    {
        return view('Auth.login'); // Menggunakan 'Auth' dengan huruf kapital sesuai nama folder di explorer Anda
    }



    public function authenticate(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => 'Username wajib diisi!',
            'password.required' => 'Password wajib diisi!',
        ]);

        // 2. Cari user berdasarkan username
        $user = User::where('username', $request->username)->first();

        // 3. Cek autentikasi & keaktifan
        if ($user && Hash::check($request->password, $user->password)) {
            
            if (isset($user->is_active) && !$user->is_active) {
                return redirect()->back()->with('error', 'Akun Anda sedang tidak aktif!');
            }

            Auth::login($user);
            $request->session()->regenerate();

            // Redirect ke system/menu setelah login berhasil
            return redirect()->intended('pengguna/profile')->with('success', 'Selamat datang kembali, ' . $user->name);
        }

        return redirect()->back()->with('error', 'Username atau password yang Anda masukkan salah!');
    }

    /**
     * Memproses logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/auth');
    }
}