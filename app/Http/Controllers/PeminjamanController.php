<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Buku;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

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

    // Unduh Struk Peminjaman Format PDF
    public function downloadStruk($id)
{
    $peminjaman = Peminjaman::with(['user', 'buku'])->findOrFail($id);

    // Kita arahkan ke view khusus struk PDF
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('anggota.peminjaman_pdf', compact('peminjaman'));
    
    // Download file dengan nama yang rapi
    return $pdf->download('struk_pinjam_' . $peminjaman->id . '.pdf');
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

    // UBAH DI SINI: Menggunakan whereIn agar status 'menunggu' DAN 'dipinjam' masuk ke halaman ini
    $query = Peminjaman::with(['user', 'buku'])->whereIn('status', ['menunggu', 'dipinjam']);

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
        $pengembalians = Peminjaman::where('status', 'kembali')
        ->with(['user', 'buku'])
        ->orderBy('id', 'desc')
        ->paginate(10);

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
        return back()->with('error', 'Stok buku habis!');
    }

    $buku->decrement('stok');

    // Update tanggal pengajuan & deadline 14 hari (2 minggu) dari hari ini
    $peminjaman->update([
        'status' => 'dipinjam',
        'tgl_pengajuan' => \Carbon\Carbon::now()->toDateString(), 
        'tgl_kembali_seharusnya' => \Carbon\Carbon::now()->addDays(14)->toDateString()
    ]);

    return back()->with('success', 'Peminjaman berhasil di-ACC! Status berubah menjadi Sedang Dipinjam.');
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

    // =========================================================================
    // TAMBAHAN FITUR BARU SESUAI FLOW MANDIRI ANGGOTA
    // =========================================================================

    /**
     * Menampilkan Dashboard Anggota dengan data Pinjaman Aktif untuk Struk
     */
    public function dashboardAnggota()
    {
        $user = Auth::user();

        // Hitung counter untuk widget atas dashboard
        $countSelesai = Peminjaman::where('user_id', $user->id)->where('status', 'kembali')->count();
        
        // Ambil semua daftar pinjaman yang berstatus 'dipinjam' untuk ditaruh di kartu "Informasi Penting"
        $pinjamanAktif = Peminjaman::where('user_id', $user->id)
            ->where('status', 'dipinjam')
            ->with('buku')
            ->get();
            
        $countDipinjam = $pinjamanAktif->count();

        return view('anggota.dashboard', compact('countDipinjam', 'countSelesai', 'pinjamanAktif'));
    }

    /**
     * Proses Pengembalian Buku Mandiri oleh Anggota beserta Upload Bukti Struk Opsional
     */
    public function kembaliBukuMandiri(Request $request, $id)
    {
        $peminjaman = Peminjaman::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'dipinjam')
            ->firstOrFail();

        $buku = Buku::findOrFail($peminjaman->buku_id);

        // Logika penanganan file struk jika diunggah (Skenario A)
        if ($request->hasFile('struk_kembali')) {
            $file = $request->file('struk_kembali');
            $namaFile = time() . '_kembali_' . Auth::id() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/struk_kembali'), $namaFile);
        }

        // Eksekusi pembaruan data transaksi langsung menjadi kembali
        $peminjaman->update([
            'status' => 'kembali',
            'total_denda' => 0 
        ]);

        // Otomatis kembalikan jumlah stok fisik buku (+1)
        $buku->increment('stok');

        return back()->with('success', 'Buku "' . $buku->judul . '" berhasil dikembalikan secara mandiri! Stok buku otomatis bertambah.');
    }
}