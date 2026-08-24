<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\SyaratPerkaraController;
use App\Http\Middleware\CheckMenuAccess;

/*
|--------------------------------------------------------------------------
| Public Routes (Akses Tanpa Login)
|--------------------------------------------------------------------------
*/
Route::get('/', [LayananController::class, 'bukuTelepon'])->name('buku-tamu-s');
Route::get('/buku-tamu', [LayananController::class, 'bukuTelepon'])->name('buku-tamu');
Route::post('/buku-tamu', [LayananController::class, 'store'])->name('buku-tamu.store');
Route::get('/layanan/persyaratan-perkara', [LayananController::class, 'persyaratanPerkara'])->name('public.persyaratan-perkara');
Route::get('/layanan/persyaratan-perkara/{satker_vshort}', [LayananController::class, 'detailPersyaratanPerkara'])->name('public.persyaratan-perkara.detail');


// Auth Routes
Route::get('/auth', [AuthController::class, 'showAuthForm'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public Layanan & Informasi Masyarakat
// Route::get('/layanan/', [SyaratPerkaraController::class, 'publicIndex'])->name('public.syarat-perkara');

Route::prefix('layanan')->group(function () {
    Route::get('/validasi-akta', fn() => abort(503));
    Route::get('/legalisir-akta', fn() => abort(503));
    Route::get('/cek-berkas', fn() => abort(503));
    Route::get('/helpdesk', fn() => abort(503));
});

// View Prototypes
Route::get('/dokumen', function () { return view('Pages.Dokumen.index'); });
// Route::get('/layanan', function () { return view('Pages.Layanan.persyaratan'); });
Route::get('/cekberkas', function () { return view('Pages.Layanan.cek-berkas'); });
Route::get('/chat', function () { return view('Pages.Layanan.chat'); });


/*
|--------------------------------------------------------------------------
| Authenticated Routes (Wajib Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // 1. ROUTE ACTION / API (Bebas dari CheckMenuAccess agar tidak terbentur redirect saat Submit Form)
    Route::prefix('system')->group(function () {
        Route::post('/reorder', [SystemController::class, 'reorder'])->name('system.reorder');
        Route::post('/menu/store', [SystemController::class, 'storeMenu'])->name('system.menu.store');
        Route::post('/submenu/store', [SystemController::class, 'storeSubmenu'])->name('system.submenu.store');
        Route::delete('/menu/{id}', [SystemController::class, 'destroyMenu'])->name('system.menu.destroy');           Route::post('/access/toggle-menu', [SystemController::class, 'toggleMenuAccess'])->name('system.access.toggle-menu');
        Route::post('/access/toggle-submenu', [SystemController::class, 'toggleSubmenuAccess'])->name('system.access.toggle-submenu');
    });

    Route::prefix('ptsp')->group(function () {
        Route::post('/syarat-perkara', [SyaratPerkaraController::class, 'store'])->name('ptsp.syarat-perkara.store');
        Route::put('/syarat-perkara/{id}', [SyaratPerkaraController::class, 'update'])->name('ptsp.syarat-perkara.update');
        Route::delete('/syarat-perkara/{id}', [SyaratPerkaraController::class, 'destroy'])->name('ptsp.syarat-perkara.destroy');
        Route::put('/syarat-perkara/{id}/approve', [SyaratPerkaraController::class, 'approve'])->name('ptsp.syarat-perkara.approve');
        Route::post('/syarat-perkara/toggle-status', [SyaratPerkaraController::class, 'toggleStatus'])->name('ptsp.syarat-perkara.toggle-status');
        
        // PERBAIKAN: Ditambahkan awalan 'ptsp.' pada nama route
        Route::post('/syarat-perkara/jenis-perkara/store', [SyaratPerkaraController::class, 'storeJenisPerkara'])->name('ptsp.syarat-perkara.store-jenis');
        Route::delete('/syarat-perkara/jenis-perkara/{jenisPerkaraId}', [SyaratPerkaraController::class, 'destroyJenisPerkara'])->name('ptsp.syarat-perkara.destroy-jenis');
    });


    // 2. ROUTE HALAMAN WEB (Diproteksi CheckMenuAccess)
    Route::middleware([CheckMenuAccess::class])->group(function () {

        // Dashboard
        Route::get('/dashboard', [PenggunaController::class, 'dashboard'])->name('dashboard');

        // Pengguna & Profile
        Route::get('/pengguna/profile', [PenggunaController::class, 'profile'])->name('profile');
        Route::put('/profile/update', [PenggunaController::class, 'updateProfile'])->name('profile.update');
        Route::get('/pengguna/satker-profile', [PenggunaController::class, 'satkerProfile'])->name('pengguna.satker-profile');
        Route::put('/pengguna/satker-profile/update', [PenggunaController::class, 'updateSatkerProfile'])->name('pengguna.satker-profile.update');

        // System Group
        Route::prefix('system')->group(function () {
            Route::get('/menu', [SystemController::class, 'menu'])->name('system.menu');
            Route::get('/submenu', [SystemController::class, 'submenu'])->name('system.submenu');
            Route::get('/satker', [SystemController::class, 'satker'])->name('system.satker');
            Route::get('/users', [SystemController::class, 'admin'])->name('system.users');
            Route::get('/access', [SystemController::class, 'access'])->name('system.access');
        });

        // PTSP / Informasi & Pengaduan Group
        Route::prefix('ptsp')->group(function () {
            Route::get('/syarat-perkara', [SyaratPerkaraController::class, 'index'])->name('ptsp.syarat-perkara.index');
            Route::get('/syarat-perkara/edit', [SyaratPerkaraController::class, 'edit'])->name('ptsp.syarat-perkara.edit');            
        });

    });

});