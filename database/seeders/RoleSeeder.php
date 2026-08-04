<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Satker;
use App\Models\Role;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $satkers = Satker::all();

        foreach ($satkers as $satker) {
            // Role standar untuk seluruh Satker
            $roles = ['pimpinan', 'admin', 'staff'];

            // Khusus MS Aceh, tambahkan role 'administrator' (Dev)
            if ($satker->satker_vshort === 'ms-aceh') {
                $roles[] = 'administrator';
            }

            foreach ($roles as $roleName) {
                Role::create([
                    'id'        => (string) Str::uuid(),
                    'satker_id' => $satker->id,
                    'role_name' => $roleName,
                ]);
            }
        }
    }
}