<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\User;
use App\Models\Buku;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter_tingkat = $request->input('tingkat');
        $filter_jurusan = $request->input('jurusan');

        // Mengambil data peminjaman dengan status 'menunggu' atau 'dipinjam'
        $query = Peminjaman::with(['user', 'buku'])->whereIn('status', ['menunggu', 'dipinjam']);

        // Filter Search (Bisa cari berdasarkan nama anggota atau judul buku)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($u) use ($search) {
                    $u->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%");
                })->orWhereHas('buku', function($b) use ($search) {
                    $b->where('judul', 'like', "%{$search}%");
                });
            });
        }

        // Filter berdasarkan Tingkat Kelas (Contoh: X, XI, XII)
        if ($filter_tingkat) {
            $query->whereHas('user', function($u) use ($filter_tingkat) {
                $u->where('kelas', 'like', $filter_tingkat . '%');
            });
        }

        // Filter berdasarkan Jurusan (Contoh: RPL, TKJ, AKL)
        if ($filter_jurusan) {
            $query->whereHas('user', function($u) use ($filter_jurusan) {
                $u->where('jurusan', $filter_jurusan);
            });
        }

        // Tampilkan 10 data per halaman sesuai alur website
        $peminjamans = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.peminjaman', compact('peminjamans', 'search', 'filter_tingkat', 'filter_jurusan'));
    }

    // Proses ACC / Setujui Peminjaman Buku
    public function accPeminjaman($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        
        // Cek stok buku
        $buku = \App\Models\Buku::find($peminjaman->buku_id);
        if (!$buku || $buku->stok <= 0) {
            return redirect()->back()->with('error', 'Stok buku ini sudah habis atau tidak ditemukan, tidak bisa ACC!');
        }

        // Update menggunakan nama kolom asli di file Model/Database kamu
        $peminjaman->update([
            'status' => 'dipinjam',
            'tgl_pinjam' => Carbon::now()->toDateString(),
            'tgl_kembali_seharusnya' => Carbon::now()->addDays(7)->toDateString(),
        ]);

        // Kurangi stok buku
        $buku->decrement('stok');

        return redirect()->route('admin.peminjaman')->with('success', 'Peminjaman buku berhasil disetujui (ACC)!');
    }

    // Proses Tolak Peminjaman Buku
    public function tolakPeminjaman($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        
        // Jika ditolak, kita bisa hapus datanya atau ubah status jadi ditolak (Disini kita hapus agar rapi)
        $peminjaman->delete();

        return redirect()->route('admin.peminjaman')->with('success', 'Permintaan peminjaman buku telah ditolak!');
    }

    // Mengambil data detail Anggota untuk Modal Pop-up via AJAX
    public function detailAnggota($id)
    {
        $user = User::findOrFail($id);

        // Hitung total buku sedang dipinjam, total telat, dan total denda (logika dasar)
        $total_dipinjam = Peminjaman::where('user_id', $user->id)->where('status', 'dipinjam')->count();
        
        // Hitung denda aktif (Jika ada kolom denda atau logika denda di tabel)
        // Sementara kita return data basic user dulu untuk ditampilkan di Modal
        return response()->json([
            'nama_lengkap' => $user->nama_lengkap,
            'username' => $user->username,
            'nisn' => $user->nisn ?? '-',
            'kelas' => $user->kelas ?? '-',
            'jurusan' => $user->jurusan ?? '-',
            'no_telp' => $user->no_telp ?? '-',
            'foto_profil' => $user->foto_profil ? asset('storage/profil/' . $user->foto_profil) : null,
            'total_dipinjam' => $total_dipinjam,
        ]);
    }
}