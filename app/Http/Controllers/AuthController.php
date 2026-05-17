<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Menampilkan halaman Login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Memproses Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Cek role untuk mengarahkan ke dashboard yang tepat
            if (Auth::user()->role === 'admin') {
                return redirect()->intended('admin/dashboard');
            }
            return redirect()->intended('anggota/dashboard');
        }

        // Jika gagal, balikkan ke login dengan pesan error
        return back()->withErrors([
            'login_error' => 'Username atau password yang kamu masukkan salah!',
        ])->withInput();
    }

    // Menampilkan halaman Register
    public function showRegister()
    {
        return view('auth.register');
    }

    // Memproses Register Anggota Mandiri
    public function register(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username' => 'required|string|unique:users|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:6|confirmed',
            'nisn' => 'required|string|unique:users',
            'kelas' => 'required|string',
            'jurusan' => 'required|string',
            'no_hp' => 'required|string',
            'alamat' => 'required|string',
        ]);

        // Buat user baru dengan otomatis role 'anggota'
        $user = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nisn' => $request->nisn,
            'kelas' => $request->kelas,
            'jurusan' => $request->jurusan,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'role' => 'anggota', // Otomatis anggota
        ]);

        // Langsung otomatis loginkan setelah register berhasil
        Auth::login($user);

        return redirect('anggota/dashboard')->with('success', 'Akun kamu berhasil terdaftar!');
    }

    // Memproses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}