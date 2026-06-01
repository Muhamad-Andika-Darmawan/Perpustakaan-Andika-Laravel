<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BukuController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $kategori_filter = $request->input('kategori');

        // Query Model Buku dengan Eager Loading Kategori
        $query = Buku::with('kategori');

        // Logika Filter Search dari PHP Native (Judul, Penulis, Penerbit)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('penulis', 'like', "%{$search}%")
                  ->orWhere('penerbit', 'like', "%{$search}%");
            });
        }

        // Logika Filter Kategori
        if ($kategori_filter) {
            $query->where('kategori_id', $kategori_filter);
        }

        // Pagination 10 data per halaman sesuai flow
        $bukus = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        
        // Ambil semua data kategori untuk dropdown & list modal
        $kategoris = Kategori::all();

        // Mengirimkan data dengan variabel filterKategori yang konsisten untuk UI Blade
        return view('admin.katalog', compact('bukus', 'kategoris', 'search', 'kategori_filter'));
    }

    // Proses Simpan Buku (Form Langsung di Halaman)
    public function storeBuku(Request $request)
    {
        $request->validate([
            'judul' => 'required',
            'kategori_id' => 'required',
            'penulis' => 'required',
            'penerbit' => 'required',
            'tahun_terbit' => 'required|numeric',
            'stok' => 'required|numeric',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'deskripsi' => 'nullable'
        ]);

        $namaCover = null;
        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $namaCover = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/covers', $namaCover);
        }

        Buku::create([
            'judul' => $request->judul,
            'kategori_id' => $request->kategori_id,
            'penulis' => $request->penulis,
            'penerbit' => $request->penerbit,
            'tahun_terbit' => $request->tahun_terbit,
            'stok' => $request->stok,
            'deskripsi' => $request->deskripsi,
            'cover' => $namaCover
        ]);

        return redirect()->route('admin.katalog')->with('success', 'Buku berhasil ditambahkan!');
    }

    // Proses Simpan Kategori Baru (Dari dalam Modal Pop-Up)
    public function storeKategori(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|unique:kategoris,nama_kategori'
        ]);

        Kategori::create([
            'nama_kategori' => $request->nama_kategori,
            'slug' => Str::slug($request->nama_kategori)
        ]);

        return redirect()->route('admin.katalog')->with('success', 'Kategori baru berhasil disimpan!');
    }

    public function updateBuku(Request $request, $id)
{
    $request->validate([
        'judul' => 'required',
        'kategori_id' => 'required',
        'penulis' => 'required',
        'penerbit' => 'required',
        'tahun_terbit' => 'required|numeric',
        'stok' => 'required|numeric',
        'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    $buku = Buku::findOrFail($id);

    // Ambil data inputan lama / baru
    $buku->judul = $request->judul;
    $buku->kategori_id = $request->kategori_id;
    $buku->penulis = $request->penulis;
    $buku->penerbit = $request->penerbit;
    $buku->tahun_terbit = $request->tahun_terbit;
    $buku->stok = $request->stok;
    $buku->deskripsi = $request->deskripsi;

    // Jika admin mengunggah cover baru
    if ($request->hasFile('cover')) {
        // Hapus cover lama dari storage jika ada agar tidak memenuhi server
        if ($buku->cover && Storage::exists('public/covers/' . $buku->cover)) {
            Storage::delete('public/covers/' . $buku->cover);
        }

        $file = $request->file('cover');
        $namaCover = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('public/covers', $namaCover);
        
        $buku->cover = $namaCover;
    }

    $buku->save();

    return redirect()->route('admin.katalog')->with('success', 'Data buku berhasil diperbarui!');
}

// --- PROSES HAPUS KATEGORI ---
    public function destroyKategori($id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();

        return redirect()->route('admin.katalog')->with('success', 'Kategori berhasil dihapus!');
    }

    // --- PROSES HAPUS BUKU ---
    public function destroyBuku($id)
    {
        $buku = Buku::findOrFail($id);

        // Hapus file cover dari storage jika ada, agar tidak memenuhi server
        if ($buku->cover && \Illuminate\Support\Facades\Storage::exists('public/covers/' . $buku->cover)) {
            \Illuminate\Support\Facades\Storage::delete('public/covers/' . $buku->cover);
        }

        $buku->delete();

        return redirect()->route('admin.katalog')->with('success', 'Buku berhasil dihapus!');
    }
} // Batas penutup class controller kamu