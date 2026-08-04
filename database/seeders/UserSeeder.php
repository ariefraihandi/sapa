<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Satker;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Administrator (Developer) khusus MS Aceh
        $msAceh = Satker::where('satker_vshort', 'ms-aceh')->first();
        if ($msAceh) {
            $adminRole = Role::where('satker_id', $msAceh->id)
                             ->where('role_name', 'administrator')
                             ->first();

            if ($adminRole) {
                User::create([
                    'id'        => (string) Str::uuid(), // <--- Generate UUID eksplisit
                    'satker_id' => $msAceh->id,
                    'role_id'   => $adminRole->id,
                    'nip'       => '199000000000000000',
                    'name'      => 'Developer Administrator',
                    'jabatan'   => 'System Developer',
                    'email'     => 'admin@ms-aceh.go.id',
                    'phone'     => '081234567890',
                    'password'  => Hash::make('password'),
                    'is_active' => true,
                ]);
            }
        }

        // 2. Akun Sample Admin untuk setiap Satker
        $satkers = Satker::all();
        foreach ($satkers as $satker) {
            $roleAdmin = Role::where('satker_id', $satker->id)
                             ->where('role_name', 'admin')
                             ->first();

            if ($roleAdmin) {
                User::create([
                    'id'        => (string) Str::uuid(), // <--- Generate UUID eksplisit
                    'satker_id' => $satker->id,
                    'role_id'   => $roleAdmin->id,
                    'nip'       => null,
                    'name'      => 'Admin ' . $satker->satker_short_name,
                    'jabatan'   => 'Administrator PTSP',
                    'email'     => 'admin.' . $satker->satker_vshort . '@ptsp.go.id',
                    'phone'     => null,
                    'password'  => Hash::make('password'),
                    'is_active' => true,
                ]);
            }
        }
    }
}