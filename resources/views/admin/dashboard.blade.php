@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0">Dashboard</h4>
        <div class="text-muted small">
            <i class="bi bi-calendar3 me-2"></i>{{ \Carbon\Carbon::now()->isoFormat('dddd, DD MMMM YYYY') }}
        </div>
    </div>

    <div class="welcome-box shadow-sm">
        <h2 class="fw-bold mb-2">Selamat Datang, {{ auth()->user()->nama_lengkap ?? 'Admin' }}! 👋</h2>
        <p class="mb-0 opacity-75">Berikut ringkasan aktivitas perpustakaan hari ini – {{ \Carbon\Carbon::now()->isoFormat('DD MMMM YYYY') }}</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="custom-card">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-book"></i></div>
                <h2 class="fw-bold mb-0">{{ $totalBuku }}</h2>
                <div class="text-muted small">Total Koleksi Buku</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="custom-card">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-people"></i></div>
                <h2 class="fw-bold mb-0">{{ $totalAnggota }}</h2>
                <div class="text-muted small">Total Anggota Aktif</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="custom-card">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-arrow-left-right"></i></div>
                <h2 class="fw-bold mb-0">0</h2>
                <div class="text-muted small">Buku Dipinjam (Aktif)</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="custom-card">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-exclamation-triangle"></i></div>
                <h2 class="fw-bold mb-0">0</h2>
                <div class="text-muted small">Terlambat Dikembalikan</div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="custom-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold m-0">Peminjaman Terbaru</h5>
                        <small class="text-muted">Aktivitas transaksi terakhir</small>
                    </div>
                    <a href="#" class="btn-view-all">Lihat Semua</a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Anggota</th>
                                <th>Judul Buku</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Kembali</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada transaksi terbaru hari ini.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="custom-card">
                <h5 class="fw-bold mb-4">Aksi Cepat</h5>
                
                <a href="{{ route('admin.katalog') }}" class="text-decoration-none text-dark">
                    <div class="quick-item d-flex align-items-center">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning mb-0 me-3" style="width:40px; height:40px;">
                            <i class="bi bi-book-half" style="font-size: 16px;"></i>
                        </div>
                        <span class="fw-bold">Tambah Buku Baru</span>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </div>
                </a>

                <a href="{{ route('admin.anggota') }}" class="text-decoration-none text-dark">
                    <div class="quick-item d-flex align-items-center">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary mb-0 me-3" style="width:40px; height:40px;">
                            <i class="bi bi-person-plus"></i>
                        </div>
                        <span class="fw-bold">Daftarkan Anggota</span>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12">
            <div class="custom-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-fire text-danger me-2"></i>Koleksi Buku Terpopuler</h5>
                <div class="p-5 text-center bg-light rounded-4 border border-dashed">
                    <i class="bi bi-images text-muted opacity-50 mb-2" style="font-size: 2.5rem;"></i>
                    <p class="text-muted mb-0 small">Carousel data buku terpopuler akan bergeser di sini.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection