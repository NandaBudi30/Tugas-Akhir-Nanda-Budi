<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Buat roles
        $karyawan = Role::firstOrCreate(['name' => 'karyawan']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $superadmin = Role::firstOrCreate(['name' => 'superadmin']);

        // 🔹 User Karyawan
        $user1 = User::firstOrCreate(
            ['email' => 'karyawan2@gmail.com'],
            [
                'name' => 'Karyawan 2',
                'password' => bcrypt('password'),
                'no_karyawan' => 'K012',
                'no_telepon' => '0812345678910',
                'nama_perusahaan' => 'PT. Mundur Jaya',
            ]
        );
        $user1->assignRole($karyawan);

        // // 🔹 User Admin
        // $user2 = User::firstOrCreate(
        //     ['email' => 'admin@gmail.com'],
        //     [
        //         'name' => 'Admin',
        //         'password' => bcrypt('password'),
        //         'no_karyawan' => 'A001',
        //         'no_telepon' => '08129876543',
        //         'nama_perusahaan' => 'PT. Maju Jaya',
        //     ]
        // );
        // $user2->assignRole($admin);

        // // 🔹 User Superadmin
        // $user3 = User::firstOrCreate(
        //     ['email' => 'superadmin@gmail.com'],
        //     [
        //         'name' => 'Super Admin',
        //         'password' => bcrypt('password'),
        //         'no_karyawan' => 'S001',
        //         'no_telepon' => '08121212121',
        //         'nama_perusahaan' => 'PT. Maju Jaya',
        //     ]
        // );
        // $user3->assignRole($superadmin);
    }
}
