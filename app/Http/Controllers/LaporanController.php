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
    public function terpopuler()
    {
        // Kita hitung jumlah peminjaman (COUNT) dikelompokkan berdasarkan buku_id
        // Menggunakan Eloquent Builder dengan subquery static count atau query manual via GroupBy
        $buku_populer = Buku::with('kategori')
            ->withCount(['peminjamans as total_dipinjam'])
            ->orderBy('total_dipinjam', 'desc')
            ->take(15) // Kita ambil top 15 buku terpopuler
            ->get();

        return view('admin.laporan.terpopuler', compact('buku_populer'));
    }
}