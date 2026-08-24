<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Urutan eksekusi sangat penting (Relasi Foreign Key)
        $this->call([
            SatkerSeeder::class,       // 1. Buat Satker
            RoleSeeder::class,         // 2. Buat Role (butuh satker_id)
            UserSeeder::class,         // 3. Buat User (butuh satker_id & role_id)
            MenuSeeder::class,         // 4. Buat Menu, Submenu, & Access (butuh role_id dari Satker)
            JenisPerkaraSeeder::class, // 5. Buat Master Jenis Perkara (Master UUID Perkara)
            SyaratPerkaraSeeder::class,// 6. Buat Syarat Perkara (butuh satker_id & jenis_perkara_id)
        ]);
    }
}