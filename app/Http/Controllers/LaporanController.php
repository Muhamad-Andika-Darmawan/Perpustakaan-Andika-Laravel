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

        // Ambil data peminjaman dalam rentang tanggal pengajuan
        $laporan = Peminjaman::with(['user', 'buku'])
            ->whereBetween('tgl_pengajuan', [$tgl_mulai, $tgl_selesai])
            ->orderBy('tgl_pengajuan', 'desc')
            ->get();

        // REKONSILIASI HITUNG DENDA HISTORIS UNTUK LAPORAN
        $total_denda_semua = 0;

        foreach ($laporan as $l) {
            $dendaRow = 0;

            // KONDISI 1: Jika denda aktif masih ada di database (> 0)
            if ($l->total_denda > 0) {
                $dendaRow = $l->total_denda;
            } 
            // KONDISI 2: Jika denda sudah di-nol-kan admin (Lunas), kita hitung ulang selisih historisnya
            elseif ($l->status === 'kembali' && $l->tgl_pengembalian && $l->tgl_kembali_seharusnya) {
                $tglSeharusnya = Carbon::parse($l->tgl_kembali_seharusnya)->startOfDay();
                $tglRealisasi = Carbon::parse($l->tgl_pengembalian)->startOfDay();

                if ($tglRealisasi->gt($tglSeharusnya)) {
                    $selisihHari = $tglRealisasi->diffInDays($tglSeharusnya, true);
                    // Hitung denda keterlambatan historis + denda struk jika filenya tidak ada
                    $dendaKeterlambatan = $selisihHari * 1000;
                    $dendaTanpaStruk = $l->struk_kembali ? 0 : 1000;
                    
                    $dendaRow = $dendaKeterlambatan + $dendaTanpaStruk;
                } else {
                    // Cek jika tepat waktu tapi tidak ada struk (tetap kena denda struk Rp 1.000)
                    $dendaRow = $l->struk_kembali ? 0 : 1000;
                }
            }

            // Simpan nominal denda historis ini ke dalam property temporary model agar bisa dibaca di Blade
            $l->denda_historis = $dendaRow;

            // Akumulasikan ke total kas denda di bagian bawah laporan
            $total_denda_semua += $dendaRow;
        }

        return view('admin.laporan.peminjaman', compact('laporan', 'tgl_mulai', 'tgl_selesai', 'total_denda_semua'));
    }

    // 2. Halaman Buku Terpopuler (Paling sering dipinjam)
    public function terpopuler()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // PERBAIKAN LOGIKA TOTAL: Kunci status valid & saring agar buku 0x dipinjam tidak masuk list
        $buku_populer = Buku::with('kategori')
            ->withCount(['peminjamans as total_dipinjam' => function($query) {
                $query->whereIn('status', ['kembali', 'dipinjam']);
            }])
            // KUNCI: Hanya ambil buku yang memiliki minimal 1 transaksi valid
            ->whereHas('peminjamans', function($query) {
                $query->whereIn('status', ['kembali', 'dipinjam']);
            })
            ->orderBy('total_dipinjam', 'desc')
            ->take(10) // Kuota maksimal top 10 besar
            ->get();

        return view('admin.laporan.terpopuler', compact('buku_populer'));
    }
}