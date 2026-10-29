<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Satker;
use App\Models\Role;
use App\Models\Menu;
use App\Models\Submenu;
use App\Models\MenuAccess;
use App\Models\SubmenuAccess;
use App\Models\AtkItem;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PengelolaBiayaProsesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ==========================================================
        // 1. VALIDASI SATKER MS ACEH
        // ==========================================================
        $msAceh = Satker::where('satker_vshort', 'ms-aceh')
            ->orWhere('satker_short_name', 'MS ACEH')
            ->first();

        if (!$msAceh) {
            $this->command->error('Satker MS Aceh tidak ditemukan!');
            return;
        }

        // ==========================================================
        // 2. AMBIL / BUAT ROLE
        // ==========================================================
        $roleSuperAdmin = Role::where('satker_id', $msAceh->id)
            ->where('role_name', 'administrator')
            ->first();

        $rolePimpinan = Role::where('satker_id', $msAceh->id)
            ->where('role_name', 'pimpinan')
            ->first();

        $rolePengelola = Role::firstOrCreate(
            [
                'satker_id' => $msAceh->id,
                'role_name' => 'pengelola_biayaproses'
            ],
            [
                'id' => (string) Str::uuid()
            ]
        );

        // ==========================================================
        // 3. SEED 3 AKUN JABATAN UTAMA
        // ==========================================================
        $akunJabatan = [
            [
                'username' => 'panitera',
                'name'     => 'Panitera MS Aceh',
                'jabatan'  => 'Panitera / Pengawas Biaya Proses',
                'email'    => 'panitera@ms-aceh.go.id',
                'password' => '12345678',
                'role_id'  => $rolePimpinan ? $rolePimpinan->id : $rolePengelola->id,
            ],
            [
                'username' => 'ppk_biayaproses',
                'name'     => 'Pejabat Pembuat Komitmen Biaya Proses',
                'jabatan'  => 'Pejabat Pembuat Komitmen (PPK)',
                'email'    => 'ppk.biayaproses@ms-aceh.go.id',
                'password' => '12345678',
                'role_id'  => $rolePengelola->id,
            ],
            [
                'username' => 'petugas_biayaproses',
                'name'     => 'Petugas Pengelola Biaya Proses',
                'jabatan'  => 'Petugas / Staf Pengelola Biaya Proses',
                'email'    => 'petugas.biayaproses@ms-aceh.go.id',
                'password' => '12345678',
                'role_id'  => $rolePengelola->id,
            ],
        ];

        foreach ($akunJabatan as $user) {
            User::updateOrCreate(
                ['username' => $user['username']],
                [
                    'id'        => (string) Str::uuid(),
                    'satker_id' => $msAceh->id,
                    'role_id'   => $user['role_id'],
                    'nip'       => null,
                    'name'      => $user['name'],
                    'jabatan'   => $user['jabatan'],
                    'email'     => $user['email'],
                    'phone'     => null,
                    'password'  => Hash::make($user['password']),
                    'is_active' => true,
                ]
            );
        }

        // ==========================================================
        // 4. BUAT MENU UTAMA & SUBMENU ATK
        // ==========================================================
        $atkMenu = Menu::updateOrCreate(
            ['name' => 'Inventaris ATK'],
            [
                'id'          => (string) Str::uuid(),
                'icon'        => 'fa-solid fa-boxes-stacked',
                'url'         => null,
                'is_dropdown' => true,
                'is_active'   => true,
                'order'       => 4,
            ]
        );

        $allowedMenuRoles = array_filter([
            $roleSuperAdmin ? $roleSuperAdmin->id : null,
            $rolePengelola ? $rolePengelola->id : null,
            $rolePimpinan ? $rolePimpinan->id : null,
        ]);

        foreach ($allowedMenuRoles as $roleId) {
            MenuAccess::firstOrCreate([
                'role_id' => $roleId,
                'menu_id' => $atkMenu->id,
            ], [
                'id' => (string) Str::uuid(),
            ]);
        }

        $atkSubmenus = [
            [
                'submenu' => 'Dashboard & Stok',
                'url'     => 'atk/dashboard',
                'order'   => 1,
                'roles'   => array_filter([
                    $roleSuperAdmin ? $roleSuperAdmin->id : null,
                    $rolePengelola ? $rolePengelola->id : null,
                    $rolePimpinan ? $rolePimpinan->id : null,
                ]),
            ],
            [
                'submenu' => 'Master Data Barang',
                'url'     => 'atk/master-barang',
                'order'   => 2,
                'roles'   => array_filter([
                    $roleSuperAdmin ? $roleSuperAdmin->id : null,
                    $rolePengelola ? $rolePengelola->id : null,
                ]),
            ],
            [
                'submenu' => 'Transaksi Stok',
                'url'     => 'atk/transaksi',
                'order'   => 3,
                'roles'   => array_filter([
                    $roleSuperAdmin ? $roleSuperAdmin->id : null,
                    $rolePengelola ? $rolePengelola->id : null,
                ]),
            ],
            [
                'submenu' => 'Laporan & Rekap',
                'url'     => 'atk/laporan',
                'order'   => 4,
                'roles'   => array_filter([
                    $roleSuperAdmin ? $roleSuperAdmin->id : null,
                    $rolePengelola ? $rolePengelola->id : null,
                    $rolePimpinan ? $rolePimpinan->id : null,
                ]),
            ],
        ];

        foreach ($atkSubmenus as $sub) {
            $createdSub = Submenu::updateOrCreate(
                [
                    'menu_id' => $atkMenu->id,
                    'submenu' => $sub['submenu'],
                ],
                [
                    'id'    => (string) Str::uuid(),
                    'url'   => $sub['url'],
                    'order' => $sub['order'],
                ]
            );

            foreach ($sub['roles'] as $roleId) {
                SubmenuAccess::firstOrCreate([
                    'role_id'    => $roleId,
                    'submenu_id' => $createdSub->id,
                ], [
                    'id' => (string) Str::uuid(),
                ]);
            }
        }

        // ==========================================================
        // 5. SEED MASTER 29 BARANG ATK (DEFAULT STOK = 0)
        // ==========================================================
        $daftarBarang = [
            ['nama_barang' => 'Kertas HVS A4', 'satuan' => 'rim'],
            ['nama_barang' => 'Map Perkara', 'satuan' => 'pcs'],
            ['nama_barang' => 'Map Arsip Perkara', 'satuan' => 'pcs'],
            ['nama_barang' => 'Pensil 2 B', 'satuan' => 'pcs'],
            ['nama_barang' => 'Lem kertas', 'satuan' => 'pcs'],
            ['nama_barang' => 'Binder Clip 260', 'satuan' => 'kotak'],
            ['nama_barang' => 'Binder Clip 111', 'satuan' => 'kotak'],
            ['nama_barang' => 'Binder Clip 107', 'satuan' => 'kotak'],
            ['nama_barang' => 'Binder Clip 155', 'satuan' => 'pcs'],
            ['nama_barang' => 'Binder Clip 200', 'satuan' => 'pcs'],
            ['nama_barang' => 'Binder Clip 280', 'satuan' => 'pcs'],
            ['nama_barang' => 'Pelubang Kertas', 'satuan' => 'kotak'],
            ['nama_barang' => 'Peruncing Pensil', 'satuan' => 'pcs'],
            ['nama_barang' => 'Staples No10-1M', 'satuan' => 'kotak'],
            ['nama_barang' => 'Staples 24/6', 'satuan' => 'pcs'],
            ['nama_barang' => 'Trigonal Clip No.3', 'satuan' => 'kotak'],
            ['nama_barang' => 'Stopmap', 'satuan' => 'pcs'],
            ['nama_barang' => 'Kertas Bufallo (Hijau)', 'satuan' => 'lembar'],
            ['nama_barang' => 'Benang Jagung', 'satuan' => 'gulung'],
            ['nama_barang' => 'Pena OHP', 'satuan' => 'pcs'],
            ['nama_barang' => 'Pena Boldliner', 'satuan' => 'pcs'],
            ['nama_barang' => 'Pena', 'satuan' => 'pcs'],
            ['nama_barang' => 'Stiker Minutasi', 'satuan' => 'pcs'],
            ['nama_barang' => 'Sticky Note', 'satuan' => 'pcs'],
            ['nama_barang' => 'Lem', 'satuan' => 'pcs'],
            ['nama_barang' => 'Jarum Sol', 'satuan' => 'pcs'],
            ['nama_barang' => 'Cartridge', 'satuan' => 'pcs'],
            ['nama_barang' => 'Penggaris', 'satuan' => 'pcs'],
            ['nama_barang' => 'Pembelian Buku Cek', 'satuan' => 'pcs'],
        ];

        foreach ($daftarBarang as $index => $item) {
            $kode = 'ATK-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

            AtkItem::updateOrCreate(
                [
                    'satker_id'   => $msAceh->id,
                    'nama_barang' => $item['nama_barang'],
                ],
                [
                    'kode_barang'   => $kode,
                    'satuan'        => $item['satuan'],
                    'stok_awal'     => 0, // Set default 0
                    'stok_saat_ini' => 0, // Set default 0
                    'stok_minimal'  => 5,
                ]
            );
        }

        $this->command->info('Seeder Pengelola Biaya Proses, Menu, Hak Akses, & 29 Master Barang ATK (Stok 0) berhasil dijalankan!');
    }
}