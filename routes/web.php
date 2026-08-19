<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuTamuController;
use App\Http\Controllers\PenggunaController;

// Jalur Tampilan Awal / Login
Route::get('/', [BukuTamuController::class, 'bukuTamuIndex'])->name('buku-tamu-s');
Route::get('/buku-tamu', [BukuTamuController::class, 'bukuTamuIndex'])->name('buku-tamu');

Route::post('/buku-tamu', [BukuTamuController::class, 'store'])->name('buku-tamu.store');

Route::prefix('pengguna')->group(function () {
    Route::get('/satker', [PenggunaController::class, 'satker'])->name('pengguna.satker');
    Route::get('/admin', [PenggunaController::class, 'admin'])->name('pengguna.admin');
});

Route::get('/auth', [AuthController::class, 'showAuthForm'])->name('auth');

// Tambahkan Route POST ini untuk menangani proses logout dari tombol di header
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Halaman Menu Dokumen & Insidentil
Route::get('/dokumen', function () {
    return view('Pages.Dokumen.index');
});

Route::get('/layanan', function () {
    return view('Pages.Layanan.persyaratan');
});

Route::get('/cekberkas', function () {
    return view('Pages.Layanan.cek-berkas');
});

Route::get('/chat', function () {
    return view('Pages.Layanan.chat');
});