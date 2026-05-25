<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Buku;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PeminjamanController extends Controller
{
    // =========================================================================
    // 1. FITUR SISI ANGGOTA (SISWA)
    // =========================================================================

    // Proses klik "Pinjam Sekarang" di Katalog Buku Anggota
    public function pinjamBuku(Request $request, $bukuId)
    {
        $user = Auth::user();
        $buku = Buku::findOrFail($bukuId);

        // Validasi Stok Buku Fisik
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

        // Simpan data (Batas kembali otomatis 7 hari ke depan)
        Peminjaman::create([
            'user_id' => $user->id,
            'buku_id' => $buku->id,
            'tgl_pengajuan' => Carbon::now()->toDateString(),
            'tgl_kembali_seharusnya' => Carbon::now()->addDays(7)->toDateString(),
            'status' => 'menunggu',
            'status_denda' => 0
        ]);

        return redirect()->route('anggota.riwayat_pinjaman')->with('success', 'Berhasil mengajukan peminjaman! Silakan tunggu ACC dari admin.');
    }

    // Proses Pembatalan Pinjaman oleh Anggota (Selama status masih 'menunggu')
    public function batalPinjam($id)
    {
        $peminjaman = Peminjaman::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($peminjaman->status !== 'menunggu') {
            return back()->with('error', 'Pengajuan tidak bisa dibatalkan karena sudah diproses admin!');
        }

        $peminjaman->delete();
        return back()->with('success', 'Pengajuan peminjaman buku berhasil dibatalkan.');
    }

    // Unduh Struk Peminjaman Format TXT/Nota
    public function downloadStruk($id)
    {
        $peminjaman = Peminjaman::with(['user', 'buku'])->findOrFail($id);

        $text  = "========================================\n";
        $text .= "       STRUK PEMINJAMAN PERPUSTAKAAN    \n";
        $text .= "========================================\n\n";
        $text .= "ID Transaksi : " . $peminjaman->id . "\n";
        $text .= "Nama Anggota : " . ($peminjaman->user->nama_lengkap ?? $peminjaman->user->name) . "\n";
        $text .= "NISN         : " . ($peminjaman->user->nisn ?? '-') . "\n";
        $text .= "Kelas/Jurusan: " . ($peminjaman->user->kelas ?? '-') . " " . ($peminjaman->user->jurusan ?? '') . "\n";
        $text .= "----------------------------------------\n";
        $text .= "Judul Buku   : " . $peminjaman->buku->judul . "\n";
        $text .= "Penulis      : " . $peminjaman->buku->penulis . "\n";
        $text .= "Penerbit     : " . $peminjaman->buku->penerbit . "\n";
        $text .= "----------------------------------------\n";
        $text .= "Tgl Pengajuan: " . Carbon::parse($peminjaman->tgl_pengajuan)->format('d-m-Y') . "\n";
        $text .= "Batas Kembali: " . Carbon::parse($peminjaman->tgl_kembali_seharusnya)->format('d-m-Y') . "\n";
        $text .= "Status Buku  : " . strtoupper($peminjaman->status) . "\n\n";
        $text .= "========================================\n";
        $text .= " Harap kembalikan buku tepat waktu ya!  \n";
        $text .= "========================================\n";

        $filename = "struk_peminjaman_" . $peminjaman->id . ".txt";

        return response($text, 200)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }


    // =========================================================================
    // 2. FITUR SISI ADMIN (MANAJEMEN & FLOW PEMINJAMAN)
    // =========================================================================

    // Menampilkan Halaman Transaksi Peminjaman (Menunggu ACC)
    public function index(Request $request)
    {
        // Tangkap inputan search dan filter dari form UI
        $search = $request->input('search');
        $filter_tingkat = $request->input('filter_tingkat');
        $filter_jurusan = $request->input('filter_jurusan');

        // Query dasar untuk status 'menunggu'
        $query = Peminjaman::with(['user', 'buku'])->where('status', 'menunggu');

        // Logika Fitur Pencarian (Nama Anggota / Judul Buku)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($u) use ($search) {
                    $u->where('nama_lengkap', 'like', "%{$search}%");
                })->orWhereHas('buku', function($b) use ($search) {
                    $b->where('judul', 'like', "%{$search}%");
                });
            });
        }

        // Logika Fitur Filter Tingkat Kelas (10, 11, 12)
        if ($filter_tingkat) {
            $query->whereHas('user', function($q) use ($filter_tingkat) {
                $q->where('kelas', $filter_tingkat);
            });
        }

        // Logika Fitur Filter Jurusan (RPL, TKJ, dll)
        if ($filter_jurusan) {
            $query->whereHas('user', function($q) use ($filter_jurusan) {
                $q->where('jurusan', $filter_jurusan);
            });
        }

        // Eksekusi pagination dan pertahankan query string di URL browser
        $peminjamans = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        // Kirim SEMUA variabel yang dibutuhkan oleh peminjaman.blade.php
        return view('admin.peminjaman', compact('peminjamans', 'search', 'filter_tingkat', 'filter_jurusan'));
    }

    // Menampilkan Halaman Transaksi Pengembalian (Buku yang sedang dipinjam)
    public function indexPengembalian(Request $request)
    {
        // Tangkap inputan search dan filter dari form UI
        $search = $request->input('search');
        $filter_tingkat = $request->input('filter_tingkat');
        $filter_jurusan = $request->input('filter_jurusan');

        // Query dasar untuk status 'dipinjam'
        $query = Peminjaman::with(['user', 'buku'])->where('status', 'dipinjam');

        // Logika Fitur Pencarian (Nama Anggota / Judul Buku)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($u) use ($search) {
                    $u->where('nama_lengkap', 'like', "%{$search}%");
                })->orWhereHas('buku', function($b) use ($search) {
                    $b->where('judul', 'like', "%{$search}%");
                });
            });
        }

        // Logika Fitur Filter Tingkat Kelas (10, 11, 12)
        if ($filter_tingkat) {
            $query->whereHas('user', function($q) use ($filter_tingkat) {
                $q->where('kelas', $filter_tingkat);
            });
        }

        // Logika Fitur Filter Jurusan (RPL, TKJ, dll)
        if ($filter_jurusan) {
            $query->whereHas('user', function($q) use ($filter_jurusan) {
                $q->where('jurusan', $filter_jurusan);
            });
        }

        // Eksekusi pagination dan pertahankan query string di URL browser
        $pengembalians = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        // Kirim SEMUA variabel yang dibutuhkan oleh pengembalian.blade.php
        return view('admin.pengembalian', compact('pengembalians', 'search', 'filter_tingkat', 'filter_jurusan'));
    }

    // Menampilkan Halaman Daftar Denda Admin
    // SINKRON dengan Route::get('/admin/denda', [PeminjamanController::class, 'halamanDendaAdmin'])
    public function halamanDendaAdmin(Request $request)
    {
        $search = $request->input('search');
        $query = Peminjaman::with(['user', 'buku'])->where('denda', '>', 0);

        if ($search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        // Variabel harus $dendas agar sinkron dengan denda.blade.php
        $dendas = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();
        return view('admin.denda', compact('dendas', 'search'));
    }

    // Proses Admin klik tombol ACC (Stok Berkurang 1)
    public function accPeminjaman($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $buku = Buku::findOrFail($peminjaman->buku_id);

        if ($buku->stok <= 0) {
            return back()->with('error', 'Gagal ACC, stok buku saat ini kosong!');
        }

        // Kurangi stok buku
        $buku->decrement('stok');

        // Ubah status transaksi menjadi dipinjam
        $peminjaman->update([
            'status' => 'dipinjam',
            'tgl_pengajuan' => Carbon::now()->toDateString(), // Mulai hitung tanggal pinjam aktif hari ini
            'tgl_kembali_seharusnya' => Carbon::now()->addDays(7)->toDateString()
        ]);

        return back()->with('success', 'Peminjaman berhasil di-ACC! Stok buku berkurang.');
    }

    // Proses Admin klik tombol Tolak
    public function tolakPeminjaman($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->update(['status' => 'ditolak']);

        return back()->with('success', 'Pengajuan peminjaman berhasil ditolak.');
    }

    // Proses Admin menerima Pengembalian Buku (Stok Kembali Bertambah 1)
    public function kembaliBuku($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $buku = Buku::findOrFail($peminjaman->buku_id);

        // Tambah kembali stok buku
        $buku->increment('stok');

        // Set denda ke 0 dulu sesuai request (langkahi logika denda)
        $peminjaman->update([
            'status' => 'kembali',
            'denda' => 0
        ]);

        return back()->with('success', 'Buku berhasil dikembalikan tepat waktu! Stok bertambah kembali.');
    }

    // Proses Lunasi Denda Manual Cash
    public function lunasiDenda($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->update(['denda' => 0]);

        return back()->with('success', 'Denda anggota berhasil dilunasi.');
    }
}