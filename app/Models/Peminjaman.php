<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjamans';
    protected $fillable = [
        'user_id', 'buku_id', 'tgl_pengajuan', 'tgl_pinjam', 'tgl_kembali_seharusnya', 'tgl_pengembalian', 'status', 'total_denda'
    ];

    // Relasi: Transaksi peminjaman ini milik seorang User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Transaksi peminjaman ini mencatat sebuah Buku
    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }
}