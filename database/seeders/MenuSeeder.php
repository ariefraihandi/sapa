<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Submenu;
use App\Models\MenuAccess;
use App\Models\SubmenuAccess;
use App\Models\Role;
use App\Models\Satker;
use Illuminate\Support\Str;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil Role Administrator dari MS Aceh (Superadmin)
        $msAceh = Satker::where('satker_vshort', 'ms-aceh')->first();
        if (!$msAceh) return;

        $superAdminRole = Role::where('satker_id', $msAceh->id)
                              ->where('role_name', 'administrator')
                              ->first();

        // 2. Ambil SELURUH Role untuk menu publik/umum
        $allRoles = Role::all();

        if (!$superAdminRole) return;

        // ==========================================================
        // 1. MENU UTAMA: INFORMASI & PENGADUAN (Order: 1, Semua Role)
        // ==========================================================
        $informasiMenu = Menu::create([
            'id'          => (string) Str::uuid(),
            'name'        => 'Informasi & Pengaduan',
            'icon'        => 'fa-solid fa-circle-info',
            'url'         => null,
            'is_dropdown' => true,
            'is_active'   => true,
            'order'       => 1,
        ]);

        // Berikan akses Menu Informasi & Pengaduan ke SELURUH Role
        foreach ($allRoles as $role) {
            MenuAccess::create([
                'id'      => (string) Str::uuid(),
                'role_id' => $role->id,
                'menu_id' => $informasiMenu->id,
            ]);
        }

        // PENYESUAIAN SUBMENU (Daftar Pengunjung di Urutan 1)
        $informasiSubmenus = [
            ['submenu' => 'Daftar Pengunjung', 'url' => 'ptsp/pengunjung', 'order' => 1],
            ['submenu' => 'Daftar Pengaduan',  'url' => 'ptsp/pengaduan',  'order' => 2], // Menu Baru di Bawah Pengunjung
            ['submenu' => 'Syarat Perkara',    'url' => 'ptsp/syarat-perkara', 'order' => 3],
            ['submenu' => 'Pengaturan PTSP',  'url' => 'ptsp/profil-ptsp', 'order' => 4],
        ];

        foreach ($informasiSubmenus as $sub) {
            $createdSub = Submenu::create([
                'id'      => (string) Str::uuid(),
                'menu_id' => $informasiMenu->id,
                'submenu' => $sub['submenu'],
                'url'     => $sub['url'],
                'order'   => $sub['order'],
            ]);

            // Berikan akses Submenu ke SELURUH Role
            foreach ($allRoles as $role) {
                SubmenuAccess::create([
                    'id'         => (string) Str::uuid(),
                    'role_id'    => $role->id,
                    'submenu_id' => $createdSub->id,
                ]);
            }
        }

        // ==========================================================
        // 2. MENU UTAMA: PENGGUNA (Order: 2, Semua Role)
        // ==========================================================
        $penggunaMenu = Menu::create([
            'id'          => (string) Str::uuid(),
            'name'        => 'Pengguna',
            'icon'        => 'fa-solid fa-users',
            'url'         => null,
            'is_dropdown' => true,
            'is_active'   => true,
            'order'       => 2,
        ]);

        // Berikan akses Menu Pengguna ke SELURUH Role
        foreach ($allRoles as $role) {
            MenuAccess::create([
                'id'      => (string) Str::uuid(),
                'role_id' => $role->id,
                'menu_id' => $penggunaMenu->id,
            ]);
        }

        $penggunaSubmenus = [
            ['submenu' => 'Profile', 'url' => 'pengguna/profile', 'order' => 1],
            ['submenu' => 'Profile Satker', 'url' => 'pengguna/satker-profile', 'order' => 2],
        ];

        foreach ($penggunaSubmenus as $sub) {
            $createdSub = Submenu::create([
                'id'      => (string) Str::uuid(),
                'menu_id' => $penggunaMenu->id,
                'submenu' => $sub['submenu'],
                'url'     => $sub['url'],
                'order'   => $sub['order'],
            ]);

            // Berikan akses Submenu ke SELURUH Role
            foreach ($allRoles as $role) {
                SubmenuAccess::create([
                    'id'         => (string) Str::uuid(),
                    'role_id'    => $role->id,
                    'submenu_id' => $createdSub->id,
                ]);
            }
        }

        // ==========================================================
        // 3. MENU UTAMA: SYSTEM (Order: 3, Hanya Superadmin)
        // ==========================================================
        $systemMenu = Menu::create([
            'id'          => (string) Str::uuid(),
            'name'        => 'System',
            'icon'        => 'fa-solid fa-gear',
            'url'         => null,
            'is_dropdown' => true,
            'is_active'   => true,
            'order'       => 3,
        ]);

        MenuAccess::create([
            'id'      => (string) Str::uuid(),
            'role_id' => $superAdminRole->id,
            'menu_id' => $systemMenu->id,
        ]);

        $systemSubmenus = [
            ['submenu' => 'Menu', 'url' => 'system/menu', 'order' => 1],
            ['submenu' => 'Submenu', 'url' => 'system/submenu', 'order' => 2],
            ['submenu' => 'Daftar Satker', 'url' => 'system/satker', 'order' => 3],
            ['submenu' => 'Daftar Pengguna', 'url' => 'system/users', 'order' => 4],
            ['submenu' => 'Hak Akses', 'url' => 'system/access', 'order' => 5],
        ];

        foreach ($systemSubmenus as $sub) {
            $createdSub = Submenu::create([
                'id'      => (string) Str::uuid(),
                'menu_id' => $systemMenu->id,
                'submenu' => $sub['submenu'],
                'url'     => $sub['url'],
                'order'   => $sub['order'],
            ]);

            // Akses submenu System hanya diberikan ke Superadmin
            SubmenuAccess::create([
                'id'         => (string) Str::uuid(),
                'role_id'    => $superAdminRole->id,
                'submenu_id' => $createdSub->id,
            ]);
        }
    }
}