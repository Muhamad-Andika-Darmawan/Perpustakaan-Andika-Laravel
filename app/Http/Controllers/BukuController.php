<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Sementara pakai DB Facade / sesuaikan jika sudah ada Model Buku

class BukuController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $kategori_filter = $request->input('kategori');

        // Query dasar mengambil data buku dan join dengan kategori jika ada
        $query = DB::table('bukus'); 

        // Logika Fitur Search (Judul, Penulis, Penerbit)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('penulis', 'like', "%{$search}%")
                  ->orWhere('penerbit', 'like', "%{$search}%");
            });
        }

        // Logika Fitur Filter Kategori
        if ($kategori_filter) {
            $query->where('kategori_id', $kategori_filter);
        }

        // Pagination: Batasi hanya 10 data per halaman sesuai flow-mu
        $buku = $query->paginate(10)->withQueryString();

        // Ambil list semua kategori untuk isi dropdown filter & modal select
        $categories = DB::table('bukus')->get(); 

        return view('admin.katalog', compact('buku', 'categories', 'search', 'kategori_filter'));
    }

    public function store(Request $request) { /* Logika simpan modal tambah */ }
    public function update(Request $request, $id) { /* Logika simpan modal edit */ }
    public function destroy($id) { /* Logika hapus */ }
}