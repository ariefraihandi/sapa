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
        // 1. Akun Super Administrator (Developer) khusus MS Aceh
        $msAceh = Satker::where('satker_vshort', 'ms-aceh')->first();
        if ($msAceh) {
            $superAdminRole = Role::where('satker_id', $msAceh->id)
                                  ->where('role_name', 'administrator')
                                  ->first();

            if ($superAdminRole) {
                User::create([
                    'id'        => (string) Str::uuid(),
                    'satker_id' => $msAceh->id,
                    'role_id'   => $superAdminRole->id,
                    'nip'       => '199000000000000000',
                    'name'      => 'Developer Administrator',
                    'username'  => 'superadmin',
                    'jabatan'   => 'System Developer',
                    'email'     => 'admin@ms-aceh.go.id',
                    'phone'     => '081234567890',
                    'password'  => Hash::make('12345678'),
                    'is_active' => true,
                ]);
            }
        }

        // 2. Akun User per Satker (Pimpinan, Admin, Staff)
        $satkers = Satker::all();

        foreach ($satkers as $satker) {
            // Bersihkan vshort dari tanda strip untuk format username (misal: "ms-bna" -> "msbna")
            $cleanVshort = str_replace('-', '', $satker->satker_vshort);

            // Ambil semua role milik satker terkait
            $roles = Role::where('satker_id', $satker->id)
                         ->whereIn('role_name', ['pimpinan', 'admin', 'staff'])
                         ->get();

            foreach ($roles as $role) {
                $roleName = $role->role_name;

                // Format username: pimpinan_msbna, admin_msbna, staff_msbna
                $username = strtolower($roleName) . '_' . $cleanVshort;

                // Menentukan nama & jabatan dummy berdasarkan role
                switch ($roleName) {
                    case 'pimpinan':
                        $name = 'Pimpinan ' . $satker->satker_short_name;
                        $jabatan = 'Ketua Mahkamah Syar\'iyah';
                        break;
                    case 'admin':
                        $name = 'Admin ' . $satker->satker_short_name;
                        $jabatan = 'Administrator PTSP';
                        break;
                    case 'staff':
                    default:
                        $name = 'Petugas ' . $satker->satker_short_name;
                        $jabatan = 'Petugas Layanan PTSP';
                        break;
                }

                User::create([
                    'id'        => (string) Str::uuid(),
                    'satker_id' => $satker->id,
                    'role_id'   => $role->id,
                    'nip'       => null,
                    'name'      => $name,
                    'username'  => $username,
                    'jabatan'   => $jabatan,
                    'email'     => $username . '@ptsp.go.id',
                    'phone'     => null,
                    'password'  => Hash::make('12345678'),
                    'is_active' => true,
                ]);
            }
        }
    }
}