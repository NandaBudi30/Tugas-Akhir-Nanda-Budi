<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class MakeFilamentUser extends Command
{
    protected $signature = 'make:filament-user';
    protected $description = 'Create a new Filament user with extra fields';

    public function handle()
    {
        $name = $this->ask('Name');
        $email = $this->ask('Email address');
        $password = $this->secret('Password');
        $noKaryawan = $this->ask('Nomor Karyawan');
        $noTelepon = $this->ask('Nomor Telepon (opsional)');
        $namaPerusahaan = $this->ask('Nama Perusahaan');

        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->no_karyawan = $noKaryawan;
        $user->no_telepon = $noTelepon;
        $user->nama_perusahaan = $namaPerusahaan;
        $user->save();

        $this->info("Filament user [{$name}] created successfully!");
    }
}
