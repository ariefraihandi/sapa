<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Menu;
use App\Models\Submenu;
use App\Models\MenuAccess;
use App\Models\SubmenuAccess;

class RollbackPengelolaBiayaProsesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Hapus User yang dibuat
        $usernames = ['panitera', 'ppk_biayaproses', 'petugas_biayaproses'];
        User::whereIn('username', $usernames)->delete();

        // 2. Hapus Menu & Submenu ATK beserta Access-nya
        $menu = Menu::where('name', 'Inventaris ATK')->first();
        if ($menu) {
            $submenus = Submenu::where('menu_id', $menu->id)->get();
            foreach ($submenus as $sub) {
                SubmenuAccess::where('submenu_id', $sub->id)->delete();
                $sub->delete();
            }
            MenuAccess::where('menu_id', $menu->id)->delete();
            $menu->delete();
        }

        // 3. Hapus Role Khusus Pengelola jika ada
        Role::where('role_name', 'pengelola_biayaproses')->delete();

        $this->command->info('Rollback data Pengelola Biaya Proses & Menu ATK berhasil!');
    }
}