<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->string('nama_lengkap');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
        
            // Kolom spesifik Anggota (Bisa kosong / nullable jika user tersebut adalah Admin)
            $table->string('nisn')->nullable()->unique();
            $table->string('kelas')->nullable();
            $table->string('jurusan')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_hp')->nullable();
        
            // Kolom Foto Profil & Deskripsi
            $table->string('foto_profil')->nullable();
            $table->text('about_me')->nullable();
        
            // Hak Akses Sistem
            $table->enum('role', ['admin', 'anggota'])->default('anggota');
        
            $table->rememberToken();
            $table->timestamps(); // Menggenerate otomatis created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
