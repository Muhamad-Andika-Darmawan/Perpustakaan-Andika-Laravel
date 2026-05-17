<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nama_lengkap' => 'Admin Utama Perpustakaan',
            'username' => 'admin',
            'email' => 'admin@perpus.com',
            'password' => Hash::make('admin123'), // Password otomatis terenkripsi aman
            'role' => 'admin',
        ]);
    }
}