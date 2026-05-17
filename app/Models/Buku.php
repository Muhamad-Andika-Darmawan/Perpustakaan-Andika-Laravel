<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    protected $fillable = [
        'kategori_id', 'judul', 'penulis', 'penerbit', 'tahun_terbit', 'stok', 'deskripsi', 'cover'
    ];

    // Relasi: Buku ini dimiliki oleh sebuah Kategori
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    // Relasi: Satu Buku bisa dipinjam berkali-kali dalam transaksi
    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }
}