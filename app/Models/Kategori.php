<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $fillable = ['nama_kategori'];

    // Relasi: Satu Kategori memiliki banyak Buku
    public function bukus()
    {
        return $this->hasMany(Buku::class);
    }
}