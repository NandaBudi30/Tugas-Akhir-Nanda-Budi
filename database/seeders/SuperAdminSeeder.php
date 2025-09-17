<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // pastikan role superadmin ada
        $role = Role::firstOrCreate(['name' => 'superadmin']);

        // buat akun superadmin default
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'no_karyawan' => 'ADM001',
                'nama_perusahaan' => 'PT. Mundur Jaya',
                'password' => Hash::make('password123'), // ganti sesuai kebutuhan
            ]
        );

        // assign role
        if (! $superadmin->hasRole('superadmin')) {
            $superadmin->assignRole($role);
        }
    }
}
