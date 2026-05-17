<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuController;

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
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Dashboard khusus Anggota (Sementara kita arahkan ke view yang sama atau view anggota besok)
    Route::get('/anggota/dashboard', function () {
        return "Selamat datang di Dashboard Anggota! (Halaman ini akan kita buat besok)";
    })->name('anggota.dashboard');

    // Proses Keluar Aplikasi
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Route Utama: Katalog Buku
    Route::get('/katalog', [BukuController::class, 'index'])->name('katalog');
    
    // Route Action (Untuk Modal Tambah/Edit/Hapus Buku nanti)
    Route::post('/katalog/store', [BukuController::class, 'store'])->name('katalog.store');
    Route::put('/katalog/update/{id}', [BukuController::class, 'update'])->name('katalog.update');
    Route::delete('/katalog/delete/{id}', [BukuController::class, 'destroy'])->name('katalog.delete');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});