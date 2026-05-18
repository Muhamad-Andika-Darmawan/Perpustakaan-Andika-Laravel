<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{

    protected $table = 'user';
    // Menampilkan Halaman Profile
    public function index()
    {
        $user = Auth::user();
        return view('profile', compact('user'));
    }

    // Memproses Update Data Diri & Foto Profil
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'no_hp' => 'nullable|string|max:15',
            'about_me' => 'nullable|string|max:255',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Update data dasar
        $user->nama_lengkap = $request->nama_lengkap;
        $user->username = $request->username;
        $user->no_hp = $request->no_hp;
        $user->about_me = $request->about_me;

        // Jika user adalah anggota, ijinkan edit kelas/jurusan/nisn jika diperlukan
        if ($user->role === 'anggota') {
            $user->nisn = $request->nisn;
            $user->kelas = $request->kelas;
            $user->jurusan = $request->jurusan;
        }

        // Proses unggah foto profil baru jika ada
        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama jika ada
            if ($user->foto_profil && Storage::exists('public/profil/' . $user->foto_profil)) {
                Storage::delete('public/profil/' . $user->foto_profil);
            }

            $file = $request->file('foto_profil');
            $namaFoto = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/profil', $namaFoto);
            $user->foto_profil = $namaFoto;
        }

        $user->save();

        return redirect()->route('profile')->with('success', 'Profil Anda berhasil diperbarui!');
    }

    // Memproses Ubah Password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed', // field konfirmasi harus bernama new_password_confirmation
        ]);

        $user = Auth::user();

        // Cek apakah password lama sesuai
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('profile')->withErrors(['current_password' => 'Password lama yang Anda masukkan salah.']);
        }

        // Update password baru
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('profile')->with('success', 'Password berhasil diubah!');
    }

    // Memproses Hapus Akun Mandiri
    public function destroy()
    {
        $user = Auth::user();

        // Hapus foto profil dari storage agar hemat ruang penyimpanan
        if ($user->foto_profil && Storage::exists('public/profil/' . $user->foto_profil)) {
            Storage::delete('public/profil/' . $user->foto_profil);
        }

        // Hapus user dari database
        $user->delete();

        // Logout otomatis
        Auth::logout();

        return redirect()->route('login')->with('success', 'Akun Anda berhasil dihapus permanen.');
    }
}