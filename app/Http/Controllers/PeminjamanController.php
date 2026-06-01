<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Buku;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    // =========================================================================
    // 1. FITUR SISI ANGGOTA (SISWA)
    // =========================================================================

    public function pinjamBuku(Request $request, $bukuId)
    {
        $user = Auth::user();
        $buku = Buku::findOrFail($bukuId);

        if ($buku->stok <= 0) {
            return back()->with('error', 'Maaf, stok buku "' . $buku->judul . '" sedang habis!');
        }

        $sudahPinjam = Peminjaman::where('user_id', $user->id)
            ->where('buku_id', $bukuId)
            ->whereIn('status', ['menunggu', 'dipinjam'])
            ->exists();

        if ($sudahPinjam) {
            return back()->with('error', 'Kamu sudah mengajukan atau sedang meminjam buku ini!');
        }

        Peminjaman::create([
            'user_id' => $user->id,
            'buku_id' => $buku->id,
            'tgl_pengajuan' => Carbon::now()->toDateString(),
            'tgl_kembali_seharusnya' => Carbon::now()->addDays(7)->toDateString(),
            'status' => 'menunggu',
            'total_denda' => 0
        ]);

        return redirect()->route('anggota.riwayat_pinjaman')->with('success', 'Berhasil mengajukan peminjaman! Silakan tunggu ACC dari admin.');
    }

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

    public function downloadStruk($id)
    {
        $peminjaman = Peminjaman::with(['user', 'buku'])->findOrFail($id);
        $pdf = Pdf::loadView('anggota.peminjaman_pdf', compact('peminjaman'));
        return $pdf->download('struk_pinjam_' . $peminjaman->id . '.pdf');
    }

    public function tagihanDendaAnggota()
    {
        $userId = Auth::id();

        // Mengambil semua data denda berjalan milik siswa (Anggota)
        $daftarDenda = Peminjaman::where('user_id', $userId)
            ->where('total_denda', '>', 0)
            ->with('buku')
            ->orderBy('created_at', 'desc')
            ->get();

        $grandTotal = $daftarDenda->sum('total_denda');
        $adaDendaAktif = $daftarDenda->count() > 0;

        return view('anggota.denda', compact('daftarDenda', 'grandTotal', 'adaDendaAktif'));
    }

    public function bukuTerpopulerAnggota()
    {
        $buku_populer = Buku::withCount(['peminjamans as total_dipinjam' => function($query) {
                $query->whereIn('status', ['dipinjam', 'kembali']);
            }])
            ->orderBy('total_dipinjam', 'desc')
            ->take(10)
            ->get();

        return view('admin.laporan.terpopuler', compact('buku_populer'));
    }

    // =========================================================================
    // 2. FITUR SISI ADMIN (MANAJEMEN & FLOW PEMINJAMAN)
    // =========================================================================

    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter_tingkat = $request->input('filter_tingkat');
        $filter_jurusan = $request->input('filter_jurusan');

        $query = Peminjaman::with(['user', 'buku'])->whereIn('status', ['menunggu', 'dipinjam']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($u) use ($search) {
                    $u->where('nama_lengkap', 'like', "%{$search}%");
                })->orWhereHas('buku', function($b) use ($search) {
                    $b->where('judul', 'like', "%{$search}%");
                });
            });
        }

        if ($filter_tingkat) {
            $query->whereHas('user', function($q) use ($filter_tingkat) {
                $q->where('kelas', $filter_tingkat);
            });
        }

        if ($filter_jurusan) {
            $query->whereHas('user', function($q) use ($filter_jurusan) {
                $q->where('jurusan', $filter_jurusan);
            });
        }

        $peminjamans = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('admin.peminjaman', compact('peminjamans', 'search', 'filter_tingkat', 'filter_jurusan'));
    }

    public function indexPengembalian(Request $request)
    {
        $search = $request->input('search');
        $filter_tingkat = $request->input('filter_tingkat');
        $filter_jurusan = $request->input('filter_jurusan');

        $query = Peminjaman::with(['user', 'buku'])->where('status', 'kembali');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($u) use ($search) {
                    $u->where('nama_lengkap', 'like', "%{$search}%");
                })->orWhereHas('buku', function($b) use ($search) {
                    $b->where('judul', 'like', "%{$search}%");
                });
            });
        }

        if ($filter_tingkat) {
            $query->whereHas('user', function($q) use ($filter_tingkat) {
                $q->where('kelas', $filter_tingkat);
            });
        }

        if ($filter_jurusan) {
            $query->whereHas('user', function($q) use ($filter_jurusan) {
                $q->where('jurusan', $filter_jurusan);
            });
        }

        $pengembalians = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('admin.pengembalian', compact('pengembalians', 'search', 'filter_tingkat', 'filter_jurusan'));
    }

    /**
     * Menampilkan Halaman Daftar Denda di Sisi Admin
     * Menampilkan data peminjaman yang total_denda > 0 agar bisa dilunasi cash
     */
    public function indexDenda(Request $request)
    {
        $search = $request->input('search');
        
        // Membaca tabel peminjaman yang memiliki nilai denda di atas 0
        $query = Peminjaman::with(['user', 'buku'])->where('total_denda', '>', 0);

        if ($search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        // Variable dipastikan tetap $dendas agar langsung terbaca di admin/denda.blade.php kamu
        $dendas = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();
        return view('admin.denda', compact('dendas', 'search'));
    }

    public function accPeminjaman($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $buku = Buku::findOrFail($peminjaman->buku_id);

        if ($buku->stok <= 0) {
            return back()->with('error', 'Stok buku habis!');
        }

        $buku->decrement('stok');

        $peminjaman->update([
            'status' => 'dipinjam',
            'tgl_pinjam' => Carbon::now()->toDateString(), 
            'tgl_kembali_seharusnya' => Carbon::now()->addDays(14)->toDateString() // Peminjaman aktif 2 minggu
        ]);

        return back()->with('success', 'Peminjaman berhasil di-ACC! Status berubah menjadi Sedang Dipinjam.');
    }

    public function tolakPeminjaman($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->update(['status' => 'ditolak']);

        return back()->with('success', 'Pengajuan peminjaman berhasil ditolak.');
    }

    /**
     * Proses Lunasi Denda Tunai/Cash oleh Admin di Meja Perpustakaan
     */
    public function lunasDenda($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        
        // Eksekusi pembaruan total_denda menjadi 0 di database
        $peminjaman->update([
            'total_denda' => 0
        ]);

        return back()->with('success', 'Pembayaran cash berhasil diterima! Status denda Anggota otomatis disinkronkan menjadi Lunas.');
    }

    public function dashboardAnggota()
{
    $userId = auth()->id();

    // 1. Buku yang sedang dipinjam (status = dipinjam)
    $totalDipinjam = Peminjaman::where('user_id', $userId)->where('status', 'dipinjam')->count();

    // 2. Mengambil akumulasi denda bersih dari database baru
    $totalDenda = Peminjaman::where('user_id', $userId)->sum('total_denda');

    // 3. Buku yang sudah selesai dibaca (status = kembali)
    $totalDibaca = Peminjaman::where('user_id', $userId)->where('status', 'kembali')->count();

    // 4. List pinjaman aktif siswa untuk unduh struk
    $pinjamanAktif = Peminjaman::where('user_id', $userId)->whereIn('status', ['menunggu', 'dipinjam'])->get();

    // 5. Query rekomendasi buku terpopuler (diambil dari buku yang paling banyak dipinjam)
    $bukuTerpopuler = \App\Models\Buku::with('kategori')
        ->withCount(['peminjamans as total_dipinjam' => function($query) {
            $query->where('status', 'kembali')->orWhere('status', 'dipinjam');
        }])
        ->orderBy('total_dipinjam', 'desc')
        ->take(12)
        ->get();

    return view('anggota.dashboard', compact('totalDipinjam', 'totalDenda', 'totalDibaca', 'pinjamanAktif', 'bukuTerpopuler'));
}

    /**
     * Alur Pengembalian Buku Mandiri oleh Siswa + Kalkulasi Denda Flat Otomatis
     */
    public function kembaliBukuMandiri(Request $request, $id)
    {
        $peminjaman = Peminjaman::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'dipinjam')
            ->firstOrFail();

        $buku = Buku::findOrFail($peminjaman->buku_id);

        // 1. Ambil Tanggal Murni Hari Ini & Batas Kembali Seharusnya
        $hariIni = Carbon::today();
        $batasKembali = Carbon::createFromFormat('Y-m-d', $peminjaman->tgl_kembali_seharusnya)->startOfDay();
        
        $dendaKeterlambatan = 0;
        $selisihHari = 0;

        // Hitung denda keterlambatan jika melewati batas (Flat Rp 1.000 per hari)
        if ($hariIni->gt($batasKembali)) {
            $selisihHari = $hariIni->diffInDays($batasKembali);
            $dendaKeterlambatan = $selisihHari * 1000;
        }

        // 2. Logika Unggah Berkas Struk & Denda Tambahan Tanpa Struk (Flat Rp 1.000)
        $dendaTanpaStruk = 0;
        $namaFile = null;

        if ($request->hasFile('struk_kembali')) {
            $file = $request->file('struk_kembali');
            $namaFile = time() . '_kembali_' . Auth::id() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/struk_kembali'), $namaFile);
        } else {
            // Jika siswa tidak mengunggah file struk pengembalian fisik
            $dendaTanpaStruk = 1000;
        }

        // Akumulasi total denda berjalan
        $totalDendaAkhir = $dendaKeterlambatan + $dendaTanpaStruk;

        // 3. Update Status Peminjaman & Kembalikan Stok Buku
        $peminjaman->update([
            'status' => 'kembali',
            'tgl_pengembalian' => Carbon::now()->toDateString(),
            'struk_kembali' => $namaFile,
            'total_denda' => $totalDendaAkhir
        ]);

        $buku->increment('stok');

        // Flash message respon interaktif untuk halaman riwayat siswa
        if ($totalDendaAkhir > 0) {
            $pesanInfo = 'Buku "' . $buku->judul . '" sukses dikembalikan. ';
            if ($dendaKeterlambatan > 0) {
                $pesanInfo .= 'Kamu terlambat ' . $selisihHari . ' hari (Denda: Rp ' . number_format($dendaKeterlambatan, 0, ',', '.') . '). ';
            }
            if ($dendaTanpaStruk > 0) {
                $pesanInfo .= 'Denda tanpa lampiran struk: Rp 1.000. ';
            }
            return back()->with('success', $pesanInfo . 'Silakan lakukan pelunasan cash di meja perpustakaan SMKN 40 ya!');
        }

        return back()->with('success', 'Terima kasih! Buku "' . $buku->judul . '" berhasil dikembalikan tepat waktu. Akun kamu bebas dari denda ✨');
    }
}