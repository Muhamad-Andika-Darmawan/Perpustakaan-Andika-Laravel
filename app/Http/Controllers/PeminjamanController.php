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

    public function accPeminjaman($id)
{
    $peminjaman = Peminjaman::findOrFail($id);

    // Keamanan: Pastikan status transaksi memang masih 'menunggu'
    if ($peminjaman->status !== 'menunggu') {
        return back()->with('error', 'Transaksi ini sudah diproses sebelumnya!');
    }

    $buku = $peminjaman->buku;

    // Validasi ekstra: Pastikan stok fisik buku masih ada sebelum dikurangi
    if ($buku->stok <= 0) {
        return back()->with('error', 'Gagal ACC! Stok buku "' . $buku->judul . '" saat ini sedang kosong.');
    }

    // 1. Kurangi stok buku sebanyak 1
    $buku->decrement('stok');

    // 2. Update status transaksi menjadi 'dipinjam'
    $peminjaman->update([
        'status' => 'dipinjam'
    ]);

    return back()->with('success', 'Pengajuan peminjaman berhasil disetujui (ACC). Stok buku telah berkurang.');
}

/**
 * Menolak Pengajuan Peminjaman oleh Admin
 */
public function tolakPeminjaman($id)
{
    $peminjaman = Peminjaman::findOrFail($id);

    if ($peminjaman->status !== 'menunggu') {
        return back()->with('error', 'Transaksi ini sudah diproses sebelumnya!');
    }

    // Update status transaksi menjadi 'ditolak' (stok tidak perlu berkurang)
    $peminjaman->update([
        'status' => 'ditolak'
    ]);

    return back()->with('success', 'Pengajuan peminjaman buku telah ditolak.');
}

/**
 * Memproses Pengembalian Buku oleh Admin
 */
public function prosesPengembalian($id)
{
    $peminjaman = Peminjaman::findOrFail($id);

    // Pastikan buku yang dikembalikan berstatus 'dipinjam'
    if ($peminjaman->status !== 'dipinjam') {
        return back()->with('error', 'Buku ini tidak dalam status sedang dipinjam!');
    }

    // 1. Tambah kembali stok buku sebanyak 1
    $peminjaman->buku->increment('stok');

    // 2. Update status transaksi menjadi 'kembali'
    $peminjaman->update([
        'status' => 'kembali'
    ]);

    return back()->with('success', 'Buku berhasil dikembalikan ke rak. Stok buku otomatis bertambah.');
}

/**
 * Mengunduh Struk Bukti Peminjaman Resmi (Format file .txt)
 */
public function downloadStruk($id)
{
    $peminjaman = Peminjaman::with(['user', 'buku'])->findOrFail($id);

    // Proteksi: Struk hanya boleh diunduh jika statusnya sudah di-ACC (dipinjam atau kembali)
    if (!in_array($peminjaman->status, ['dipinjam', 'kembali'])) {
        return back()->with('error', 'Struk belum diterbitkan karena pengajuan belum disetujui oleh admin.');
    }

    $text = "========================================\n";
    $text .= "       STRUK BUKTI PEMINJAMAN BUKU      \n";
    $text .= "            PERPUSTAKAAN ANDIKA         \n";
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
    $text .= "Status Buku  : " . strtoupper($peminjaman->status) . " (DI-ACC ADMIN)\n\n";
    $text .= "========================================\n";
    $text .= "  * Bawa struk ini & tunjukkan kepada   \n";
    $text .= "    admin saat mengembalikan buku.      \n";
    $text .= "========================================\n";

    $filename = "struk-peminjaman-" . $peminjaman->id . ".txt";

    return response($text, 200)
        ->header('Content-Type', 'text/plain')
        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
}
}