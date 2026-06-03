<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; // Tambahkan ini untuk memproses query populer
use Carbon\Carbon;

class AnggotaController extends Controller
{
    public function dashboardAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
        
        // 1. Data statistik dasar
        $totalBuku = Buku::count();
        $totalAnggota = User::where('role', 'anggota')->count();
        
        // 2. Menghitung Buku Dipinjam Aktif (status = dipinjam)
        $bukuDipinjamAktif = Peminjaman::where('status', 'dipinjam')->count();
        
        // 3. REVISI GURU: Menghitung Total Nominal Denda yang Sedang Berjalan (Aktif)
        $hariIni = Carbon::today();
        $peminjamanTerlambat = Peminjaman::where('status', 'dipinjam')
                                        ->where('tgl_kembali_seharusnya', '<', $hariIni->toDateString())
                                        ->get();

        $totalDendaBerjalan = 0;
        foreach ($peminjamanTerlambat as $p) {
            $targetKembali = Carbon::parse($p->tgl_kembali_seharusnya);
            
            // Hitung selisih hari murni dari tanggal seharusnya sampai hari ini
            $selisihHari = $targetKembali->diffInDays($hariIni);
            
            // MENYUTIKKAN DENDA KETERLAMBATAN MURNI (Rp 1.000 / Hari)
            $dendaKeterlambatan = $selisihHari * 1000;
            
            // Akumulasi (Denda struk dihapus agar simulasi pas Rp 3.000)
            $totalDendaBerjalan += $dendaKeterlambatan;
        }

        // 4. Mengambil 5 Transaksi Peminjaman Terbaru beserta relasinya
        $peminjamanTerbaru = Peminjaman::with(['user', 'buku'])
                                        ->orderBy('id', 'desc')
                                        ->take(5)
                                        ->get();

        // 5. REVISI GURU: Carousel buku terpopuler dibatasi jadi 5 saja (take(5))
        $bukuTerpopuler = Buku::with('kategori')
            ->withCount(['peminjamans as total_dipinjam' => function($query) {
                $query->whereIn('status', ['kembali', 'dipinjam']);
            }])
            ->whereHas('peminjamans', function($query) {
                $query->whereIn('status', ['kembali', 'dipinjam']);
            })
            ->orderBy('total_dipinjam', 'desc')
            ->take(5) // Berubah dari 10 ke 5
            ->get();

        // Oper variabel baru ke view dashboard admin
        return view('admin.dashboard', compact(
            'totalBuku', 
            'totalAnggota', 
            'bukuDipinjamAktif', 
            'totalDendaBerjalan', // Variabel baru
            'peminjamanTerbaru', 
            'bukuTerpopuler'
        ));
    }
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter_jurusan = $request->input('jurusan');
        $filter_kelas = $request->input('kelas');

        $query = User::query();

        // Filter Pencarian berdasarkan Nama Lengkap atau NISN
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan Jurusan
        if ($filter_jurusan) {
            $query->where('jurusan', $filter_jurusan);
        }

        // Filter berdasarkan Kelas
        if ($filter_kelas) {
            $query->where('kelas', $filter_kelas);
        }

        // Ambil data dengan pagination 10 data per halaman sesuai flow
        $users = $query->orderBy('role', 'asc')
                       ->orderBy('nama_lengkap', 'asc')
                       ->paginate(10)
                       ->withQueryString();

        return view('admin.anggota', compact('users', 'search', 'filter_jurusan', 'filter_kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'nama_lengkap' => 'required',
            'role' => 'required|in:admin,anggota',
            'password' => 'required|min:6',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $namaFoto = null;
        if ($request->hasFile('foto_profil')) {
            $file = $request->file('foto_profil');
            $namaFoto = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/profil', $namaFoto);
        }

        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'nama_lengkap' => $request->nama_lengkap,
            'nisn' => $request->nisn,
            'kelas' => $request->kelas,
            'jurusan' => $request->jurusan,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'foto_profil' => $namaFoto,
        ]);

        return redirect()->route('admin.anggota')->with('success', 'User/Anggota baru berhasil didaftarkan!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'required|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'nama_lengkap' => 'required',
            'role' => 'required|in:admin,anggota',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user->username = $request->username;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->nama_lengkap = $request->nama_lengkap;
        $user->nisn = $request->nisn;
        $user->kelas = $request->kelas;
        $user->jurusan = $request->jurusan;
        $user->no_hp = $request->no_hp;
        $user->alamat = $request->alamat;

        // Jika admin menginput password baru (opsional saat edit)
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Ganti foto profil jika ada berkas baru
        if ($request->hasFile('foto_profil')) {
            if ($user->foto_profil && Storage::exists('public/profil/' . $user->foto_profil)) {
                Storage::delete('public/profil/' . $user->foto_profil);
            }

            $file = $request->file('foto_profil');
            $namaFoto = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/profil', $namaFoto);
            $user->foto_profil = $namaFoto;
        }

        $user->save();

        return redirect()->route('admin.anggota')->with('success', 'Data user/anggota berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Cegah admin menghapus dirinya sendiri secara tidak sengaja
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.anggota')->with('error', 'Anda tidak bisa menghapus akun Anda sendiri dari halaman ini!');
        }

        if ($user->foto_profil && Storage::exists('public/profil/' . $user->foto_profil)) {
            Storage::delete('public/profil/' . $user->foto_profil);
        }

        $user->delete();

        return redirect()->route('admin.anggota')->with('success', 'Akun user/anggota berhasil dihapus!');
    }

    // --- HALAMAN DAFTAR ANGGOTA & STAFF SISI ANGGOTA (10 DATA PER HALAMAN) ---
    public function dataAnggotaMasyarakat(Request $request)
    {
        if (auth()->user()->role !== 'anggota') {
            abort(403, 'Halaman ini khusus untuk Anggota/Siswa.');
        }
        $search = $request->input('search');
        $filter_kelas = $request->input('kelas');
        $filter_jurusan = $request->input('jurusan');

        // Query model User
        $query = User::query();

        // Logika Pencarian Nama atau NISN
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        // Logika Filter Kelas
        if ($filter_kelas) {
            $query->where('kelas', $filter_kelas);
        }

        // Logika Filter Jurusan
        if ($filter_jurusan) {
            $query->where('jurusan', $filter_jurusan);
        }

        // Urutkan berdasarkan role admin/staff dulu baru anggota, lalu paginate 10 data
        $users = $query->orderByRaw("FIELD(role, 'admin', 'staff', 'anggota')")
                       ->orderBy('name', 'asc')
                       ->paginate(10)
                       ->withQueryString();

        return view('anggota.data_anggota', compact('users', 'search', 'filter_kelas', 'filter_jurusan'));
    }
}