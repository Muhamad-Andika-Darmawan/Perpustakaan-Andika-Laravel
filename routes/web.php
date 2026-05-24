<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\ProfileController;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\User;

// Jika user menembak URL utama (/), langsung arahkan ke halaman login
Route::get('/', function () {
    return redirect()->route('login');
});

// Jalur Masuk (Auth GUEST - Hanya bisa diakses jika BELUM login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.proses');
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.proses');
});

// Jalur Dashboard & Fitur (Hanya bisa diakses jika SUDAH login)
Route::middleware('auth')->group(function () {
    
    // Dashboard khusus Admin
    Route::get('/admin/dashboard', function () {
    $totalBuku = Buku::count();
    $totalAnggota = User::count();
    return view('admin.dashboard', compact('totalBuku', 'totalAnggota'));
})->name('admin.dashboard');
    

    // Rute untuk Dashboard Anggota
    Route::get('/anggota/dashboard', function () {
        // Sementara kita pasang angka statis dulu seperti rencana simulasimu
        return view('anggota.dashboard');
    })->name('anggota.dashboard')->middleware('auth');

    // Proses Keluar Aplikasi
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Pastikan dimasukkan di dalam grup middleware 'auth' Anda yang sudah berjalan
    Route::get('/admin/katalog', [BukuController::class, 'index'])->name('admin.katalog');
    Route::post('/admin/katalog/buku', [BukuController::class, 'storeBuku'])->name('admin.katalog.storeBuku');
    Route::post('/admin/katalog/kategori', [BukuController::class, 'storeKategori'])->name('admin.katalog.storeKategori');

    // FIX REVISI KATEGORI: Di katalog.blade.php form hapus menggunakan method POST (tanpa @method('DELETE'))
    // Jadi di route wajib kita ganti dari Route::delete menjadi Route::post agar tidak error!
    Route::post('/admin/katalog/kategori/{id}', [BukuController::class, 'destroyKategori'])->name('admin.katalog.deleteKategori');

    // Tambahkan Route baru ini untuk memproses Update/Edit Buku dari Modal Pop-Up
    Route::put('/admin/katalog/update/{id}', [BukuController::class, 'updateBuku'])->name('admin.katalog.updateBuku');

    // Pastikan diletakkan di dalam grup middleware('auth')
    Route::get('/admin/anggota', [App\Http\Controllers\AnggotaController::class, 'index'])->name('admin.anggota');
    Route::post('/admin/anggota', [App\Http\Controllers\AnggotaController::class, 'store'])->name('admin.anggota.store');
    Route::put('/admin/anggota/update/{id}', [App\Http\Controllers\AnggotaController::class, 'update'])->name('admin.anggota.update');
    Route::delete('/admin/anggota/delete/{id}', [App\Http\Controllers\AnggotaController::class, 'destroy'])->name('admin.anggota.delete');

    // Transaksi Peminjaman (Admin)
    Route::get('/admin/peminjaman', [PeminjamanController::class, 'index'])->name('admin.peminjaman');
    Route::post('/admin/peminjaman/acc/{id}', [PeminjamanController::class, 'accPeminjaman'])->name('admin.peminjaman.acc');
    Route::post('/admin/peminjaman/tolak/{id}', [PeminjamanController::class, 'tolakPeminjaman'])->name('admin.peminjaman.tolak');
    Route::get('/admin/peminjaman/detail-anggota/{id}', [PeminjamanController::class, 'detailAnggota'])->name('admin.peminjaman.detailAnggota');

    // Tambahan Baru: Transaksi Pengembalian (Admin)
    Route::get('/admin/pengembalian', [PeminjamanController::class, 'indexPengembalian'])->name('admin.pengembalian');
    Route::post('/admin/pengembalian/proses/{id}', [PeminjamanController::class, 'prosesPengembalian'])->name('admin.pengembalian.proses');

    // Tambahan Baru: Transaksi Denda (Admin)
    Route::get('/admin/denda', [PeminjamanController::class, 'indexDenda'])->name('admin.denda');
    Route::post('/admin/denda/lunas/{id}', [PeminjamanController::class, 'lunasDenda'])->name('admin.denda.lunas');

    // Jalur Menu LAPORAN (Admin)
    Route::get('/admin/laporan/peminjaman', [App\Http\Controllers\LaporanController::class, 'peminjaman'])->name('admin.laporan.peminjaman');
    Route::get('/admin/laporan/terpopuler', [App\Http\Controllers\LaporanController::class, 'terpopuler'])->name('admin.laporan.terpopuler');

    // Jalur Fitur Profile Pengaturan (Bisa diakses Admin & Anggota)
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile/delete', [ProfileController::class, 'destroy'])->name('profile.delete');

    Route::get('/anggota/katalog', function (Illuminate\Http\Request $request) {
    $search = $request->input('search');
    $filterKategori = $request->input('kategori');

    // Query buku dengan filter search & kategori
    $query = Buku::with('kategori');

    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('judul', 'like', "%{$search}%")
              ->orWhere('penulis', 'like', "%{$search}%")
              ->orWhere('penerbit', 'like', "%{$search}%");
        });
    }

    if ($filterKategori) {
        $query->where('kategori_id', $filterKategori);
    }

    // Ambil data buku dengan pagination 10 data
    $bukus = $query->paginate(10)->withQueryString();
    
    // Ambil semua kategori untuk menu dropdown filter
    $kategoris = Kategori::all();

    return view('anggota.katalog', compact('bukus', 'kategoris', 'search', 'filterKategori'));
    })->name('anggota.katalog')->middleware('auth');

    Route::get('/anggota/data-anggota', function (Illuminate\Http\Request $request) {
    $search = $request->input('search');
    $filter_kelas = $request->input('kelas'); 
    // 1. AMBIL DATA JURUSAN DARI DROPDOWN HTML (Sebelumnya bagian ini hilang)
    $filter_jurusan = $request->input('jurusan'); 

    $query = App\Models\User::query();

    // Fitur Search Multi-Kolom
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('nama_lengkap', 'like', "%{$search}%")
              ->orWhere('username', 'like', "%{$search}%")
              ->orWhere('nisn', 'like', "%{$search}%")
              ->orWhere('role', 'like', "%{$search}%");
        });
    }

    // Logika Filter Kelas
    if ($filter_kelas) {
        $query->where('kelas', $filter_kelas);
    }

    // Logika Filter Jurusan
    if ($filter_jurusan) {
        $query->where('jurusan', $filter_jurusan);
    }

    // Ambil data dengan pagination dan pertahankan parameter URL query
    $users = $query->orderBy('role', 'asc')->paginate(10)->withQueryString();
    
    // 2. MASUKKAN 'filter_jurusan' KE DALAM COMPACT AGAR DIOPER KE BLADE
    return view('anggota.data_anggota', compact('users', 'search', 'filter_kelas', 'filter_jurusan'));
})->name('anggota.data_anggota');
}
);