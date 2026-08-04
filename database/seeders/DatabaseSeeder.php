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
            SatkerSeeder::class, // 1. Buat Satker
            RoleSeeder::class,   // 2. Buat Role (butuh satker_id)
            UserSeeder::class,   // 3. Buat User (butuh satker_id & role_id)
        ]);
    }
}