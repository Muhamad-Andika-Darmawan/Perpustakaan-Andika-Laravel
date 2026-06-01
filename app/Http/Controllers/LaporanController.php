<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Buku;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    // 1. Halaman Laporan Peminjaman & Pengembalian Komplit
    public function peminjaman(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
        // Default filter tanggal: Dari awal bulan ini sampai hari ini
        $tgl_mulai = $request->input('tgl_mulai', Carbon::now()->startOfMonth()->toDateString());
        $tgl_selesai = $request->input('tgl_selesai', Carbon::now()->endOfMonth()->toDateString());

        // Ambil data peminjaman yang berada dalam rentang tanggal pengajuan/peminjaman
        // Kita gunakan eager loading ke user dan buku agar query ringan
        $laporan = Peminjaman::with(['user', 'buku'])
            ->whereBetween('tgl_pengajuan', [$tgl_mulai, $tgl_selesai])
            ->orderBy('tgl_pengajuan', 'desc')
            ->get();

        // Hitung total denda terkumpul yang sudah lunas atau tercatat di rentang waktu tersebut
        // Meniru logika summary denda di file native kamu
        $total_denda_semua = $laporan->sum('total_denda');

        return view('admin.laporan.peminjaman', compact('laporan', 'tgl_mulai', 'tgl_selesai', 'total_denda_semua'));
    }

    // 2. Halaman Buku Terpopuler (Paling sering dipinjam)
    // 2. Halaman Buku Terpopuler (Paling sering dipinjam)
    public function terpopuler()
    {
        if (auth()->user()->role !== 'admin') {
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
        // PERBAIKAN LOGIKA: Ditambahkan filter status agar hanya menghitung yang VALID (dipinjam & kembali)
        $buku_populer = Buku::with('kategori')
            ->withCount(['peminjamans as total_dipinjam' => function($query) {
                $query->whereIn('status', ['dipinjam', 'kembali']);
            }])
            ->orderBy('total_dipinjam', 'desc')
            ->take(10)
            ->get();

        return view('admin.laporan.terpopuler', compact('buku_populer'));
    }
}