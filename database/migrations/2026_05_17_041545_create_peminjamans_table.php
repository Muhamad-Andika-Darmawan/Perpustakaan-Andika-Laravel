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
        Schema::create('peminjamans', function (Blueprint $table) {
            $table->id(); // Primary Key
            // Relasi ke tabel users dan bukus
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('buku_id')->constrained('bukus')->onDelete('cascade');
        
            // Data Tanggal & Alur Transaksi
            $table->date('tgl_pengajuan');
            $table->date('tgl_pinjam')->nullable(); // Terisi setelah di-ACC admin
            $table->date('tgl_kembali_seharusnya')->nullable(); // Terisi setelah di-ACC admin (+7 hari)
            $table->date('tgl_pengembalian')->nullable(); // Terisi saat buku dibalikkan
        
            // Status & Denda
            $table->enum('status', ['menunggu', 'dipinjam', 'kembali', 'ditolak'])->default('menunggu');
            $table->integer('total_denda')->default(0); // Diisi Rp 1.000/hari kalau telat
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjamans');
    }
};
