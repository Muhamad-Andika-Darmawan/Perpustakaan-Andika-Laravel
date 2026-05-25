<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Buku;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PeminjamanController extends Controller
{
    // 1. Fungsi untuk memproses pengajuan pinjaman dari Anggota
    public function pinjamBuku(Request $request, $bukuId)
    {
        $user = Auth::user();
        $buku = Buku::findOrFail($bukuId);

        // Validasi Stok Buku
        if ($buku->stok <= 0) {
            return back()->with('error', 'Maaf, stok buku "' . $buku->judul . '" sedang habis!');
        }

        // Validasi Duplikasi Pengajuan
        $sudahPinjam = Peminjaman::where('user_id', $user->id)
            ->where('buku_id', $bukuId)
            ->whereIn('status', ['menunggu', 'dipinjam'])
            ->exists();

        if ($sudahPinjam) {
            return back()->with('error', 'Kamu sudah mengajukan atau sedang meminjam buku ini!');
        }

        // Simpan data
        Peminjaman::create([
            'user_id' => $user->id,
            'buku_id' => $buku->id,
            'tgl_pengajuan' => Carbon::now()->toDateString(),
            'tgl_kembali_seharusnya' => Carbon::now()->addDays(7)->toDateString(),
            'status' => 'menunggu', 
            'total_denda' => 0
        ]);

        return redirect()->route('anggota.riwayat_pinjaman')->with('success', 'Pengajuan peminjaman buku berhasil dikirim!');
    }

    // 2. Fungsi untuk membatalkan pengajuan (Cukup satu ini saja)
    public function batalPinjam($id)
    {
        // Ambil data peminjaman berdasarkan ID dan pastikan ini milik user yang login
        $peminjaman = Peminjaman::where('id', $id)
                                ->where('user_id', auth()->id())
                                ->firstOrFail();

        // Cek keamanan, pastikan statusnya memang masih 'menunggu'
        if ($peminjaman->status !== 'menunggu') {
            return back()->with('error', 'Pengajuan tidak bisa dibatalkan karena sudah diproses admin!');
        }

        // Hapus data dari database
        $peminjaman->delete();

        return back()->with('success', 'Pengajuan peminjaman buku berhasil dibatalkan.');
    }
}