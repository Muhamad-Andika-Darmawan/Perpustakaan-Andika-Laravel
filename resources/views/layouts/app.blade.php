<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Perpustakaan Digital SMKN 40</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        /* STYLE ASLI 100% DARI UI PERPUSTAKAAN PHP NATIVE (FONT POPPINS DIHAPUS) */
        body { background: #f8fafc; font-size: 14px; color: #334155; }
        
        /* Layout Wrapper Utama */
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        
        /* Sidebar Styling - Meniru Karakter col-md-2 p-4 dengan presisi */
        #sidebar { 
            width: 16.666667%; /* Setara dengan col-md-2 */
            min-width: 240px; 
            background: #0b1b35; 
            height: 100vh; /* Kunci tinggi sidebar pas satu layar penuh */
            position: sticky; 
            top: 0; 
            color: white; 
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }
        .sidebar-top {
            overflow-y: auto; /* Menu atas bisa scroll sendiri kalau kepanjangan */
            flex: 1;
        }
        /* Opsional: Sembunyikan scrollbar menu biar sidebar tetap terlihat bersih bersih */
        .sidebar-top::-webkit-scrollbar {
            width: 0px;
        }
        
        .logo-box { width: 45px; height: auto; }
        .sidebar-title { font-weight: 600; font-size: 16px; }
        .sidebar-sub { font-size: 12px; color: #94a3b8; }
        
        /* Nav Link Style Langsung Tanpa UL LI (Sama dengan PHP Native) */
        #sidebar .nav-link { 
            color: #cbd5e1; 
            padding: 10px 15px; 
            border-radius: 10px; 
            margin: 4px 0; 
            display: flex; 
            align-items: center; 
            gap: 10px; 
            text-decoration: none;
            font-size: 14px;
        }
        #sidebar .nav-link:hover { background: rgba(255,255,255,0.1); color: white; }
        #sidebar .nav-link.active { background: #f59e0b; color: white; }
        
        .menu-section { 
            font-size: 11px; 
            letter-spacing: 1px; 
            color: #94a3b8; 
            margin-top: 20px; 
            margin-bottom: 8px; 
            font-weight: 600;
        }
        
        /* Layout Area Konten (col-md-10 p-5) */
        #main-section { flex: 1; min-width: 0; }
        #content { padding: 48px !important; } /* Padding 48px setara p-5 */

        /* UI DASHBOARD COMPONENT STYLE */
        .welcome-box { 
            background: linear-gradient(135deg, #0b1b35 0%, #1e293b 100%); 
            color: white; border-radius: 24px; padding: 40px; margin-bottom: 30px; position: relative; overflow: hidden; 
        }
        .welcome-box::after {
            content: "📚"; position: absolute; right: 40px; top: 50%; transform: translateY(-50%); font-size: 80px; opacity: 0.1;
        }

        .custom-card {
            border: none; border-radius: 24px; background: white; padding: 25px; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .stat-icon { width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 20px; }
        
        .btn-view-all { background: #fffbeb; color: #b45309; border: none; font-weight: 700; border-radius: 12px; padding: 8px 20px; text-decoration: none; font-size: 12px; transition: 0.2s; }
        .btn-view-all:hover { background: #fef3c7; }
        
        .badge-status { padding: 6px 16px; border-radius: 10px; font-weight: 600; font-size: 11px; text-transform: uppercase; }
        .bg-light-blue { background: #eff6ff; color: #3b82f6; }
        .bg-light-green { background: #f0fdf4; color: #22c55e; }
        
        .quick-item { border: 1px solid #f1f5f9; border-radius: 18px; padding: 15px; margin-bottom: 12px; transition: 0.2s; cursor: pointer; }
        .quick-item:hover { border-color: #f59e0b; background: #fffcf5; }
        
        /* STYLE TABEL DENGAN GARIS TEPI (ORIGINAL NATIVE) */
        .table thead th { border: none; color: #94a3b8; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px; border-right: 1px solid #f1f5f9; }
        .table thead th:last-child { border-right: none; }
        .table tbody td { padding: 15px; border-top: 1px solid #f1f5f9; border-right: 1px solid #f1f5f9; vertical-align: middle; }
        .table tbody td:last-child { border-right: none; }
        .table { border-collapse: separate; border-spacing: 0; }

        /* CSS Tambahan untuk Footer Profil Sidebar */
        .sidebar-footer {
            background: rgba(0, 0, 0, 0.2);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        .avatar-sidebar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #f59e0b;
        }
        .avatar-sidebar-placeholder {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f59e0b;
            color: #0b1b35;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
            border: 2px solid #f59e0b;
        }
        .btn-danger-sidebar {
            background: #e63946;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }
        .btn-danger-sidebar:hover {
            background: #cb2d3b;
            color: white;
        }
        .quick-profile-link:hover {
            background: rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body>

<div class="wrapper">
    <nav id="sidebar" class="d-flex flex-column justify-content-between">
        
        <div class="sidebar-top p-4 w-100">
            <div class="d-flex align-items-center mb-4">
                <img src="{{ asset('logo-smk.png') }}" alt="Logo" class="me-3 logo-box">
                <div>
                    <div class="sidebar-title">Perpustakaan 40</div>
                    <div class="sidebar-sub">{{ auth()->user()->role == 'admin' ? 'Admin Panel' : 'Anggota Panel' }}</div>
                </div>
            </div>

            @if(auth()->user()->role == 'admin')
                <div class="menu-section">UTAMA</div>
                <a class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a class="nav-link {{ Route::is('admin.katalog') ? 'active' : '' }}" href="{{ route('admin.katalog') }}">
                    <i class="bi bi-book"></i> Katalog Buku
                </a>
                <a class="nav-link {{ Route::is('admin.anggota') ? 'active' : '' }}" href="{{ route('admin.anggota') }}">
                    <i class="bi bi-people"></i> Data Anggota & Staff
                </a>

                <div class="menu-section">TRANSAKSI</div>
                <a class="nav-link {{ Route::is('admin.peminjaman') ? 'active' : '' }}" href="{{ route('admin.peminjaman') }}">
                    <i class="bi bi-arrow-left-right"></i> Peminjaman
                </a>
                <a class="nav-link {{ Route::is('admin.pengembalian') ? 'active' : '' }}" href="{{ route('admin.pengembalian') }}">
                    <i class="bi bi-arrow-return-left"></i> Pengembalian
                </a>
                <a class="nav-link {{ Route::is('admin.denda') ? 'active' : '' }}" href="{{ route('admin.denda') }}">
                    <i class="bi bi-exclamation-triangle"></i> Denda
                </a>

                <div class="menu-section">LAPORAN</div>
                <a class="nav-link {{ Route::is('admin.laporan.peminjaman') ? 'active' : '' }}" href="{{ route('admin.laporan.peminjaman') }}">
                    <i class="bi bi-bar-chart"></i> Laporan Peminjaman
                </a>
                <a class="nav-link {{ Route::is('admin.laporan.terpopuler') ? 'active' : '' }}" href="{{ route('admin.laporan.terpopuler') }}">
                    <i class="bi bi-graph-up"></i> Buku Terpopuler
                </a>
            @else
                <div class="menu-section">UTAMA</div>
                <a class="nav-link {{ Route::is('anggota.dashboard') ? 'active' : '' }}" href="{{ route('anggota.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a class="nav-link {{ Route::is('anggota.katalog') ? 'active' : '' }}" href="{{ route('anggota.katalog') }}">
                    <i class="bi bi-book"></i> Katalog Buku
                </a>
                <a class="nav-link {{ Route::is('anggota.data_anggota') ? 'active' : '' }}" href="{{ route('anggota.data_anggota') }}">
                    <i class="bi bi-people"></i> Data Anggota & Staff
                </a>

                <div class="menu-section">AKTIVITAS SAYA</div>
                <a class="nav-link" href="#">
                    <i class="bi bi-clock-history"></i> Riwayat & Pinjaman
                </a>
                <a class="nav-link" href="#">
                    <i class="bi bi-wallet2"></i> Tagihan Denda
                </a>
                <a class="nav-link" href="#">
                    <i class="bi bi-fire"></i> Buku Terpopuler
                </a>
            @endif

        </div> <div class="sidebar-footer p-3 w-100 mt-auto">
            <a href="{{ route('profile') }}" class="d-flex align-items-center mb-3 text-decoration-none quick-profile-link p-2" style="border-radius: 12px; transition: 0.2s;">
                <div class="me-2">
                    @if(auth()->user()->foto_profil && Storage::exists('public/profil/' . auth()->user()->foto_profil))
                        <img src="{{ asset('storage/profil/' . auth()->user()->foto_profil) }}" alt="Foto Profil" class="avatar-sidebar">
                    @else
                        <div class="avatar-sidebar-placeholder">
                            {{ strtoupper(substr(auth()->user()->nama_lengkap, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="text-truncate" style="max-width: 140px;">
                    <h6 class="m-0 fw-bold text-white text-truncate" style="font-size: 13px;">{{ auth()->user()->nama_lengkap }}</h6>
                    <small class="text-warning d-block text-truncate" style="font-size: 11px;">@​{{ auth()->user()->username }}</small>
                </div>
            </a>

            <a class="btn btn-danger-sidebar w-100 d-flex align-items-center justify-content-center gap-2 py-2" href="#" onclick="event.preventDefault(); if(confirm('Yakin ingin keluar dari aplikasi?')) { document.getElementById('logout-form').submit(); }">
                <i class="bi bi-box-arrow-right"></i> Log Out
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div> </nav>

    <div id="main-section" class="d-flex flex-column">
        <div id="content">
            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>