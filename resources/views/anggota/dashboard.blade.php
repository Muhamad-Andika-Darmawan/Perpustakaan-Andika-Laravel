@extends('layouts.app')

@section('title', 'Dashboard Anggota')

@section('content')
<style>
    /* SINKRONISASI STYLE ASLI 100% SAMA DENGAN DASHBOARD ADMIN */
    .book-card-carousel {
        border: none; 
        border-radius: 14px;
        background: white; 
        transition: all 0.3s ease;
        height: 100%; 
        display: flex; 
        flex-direction: column; 
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .book-card-carousel:hover { 
        transform: translateY(-6px); 
        box-shadow: 0 15px 20px -5px rgba(0, 0, 0, 0.1); 
    }
    
    /* Aspek Rasio Tetap 4:5 Namun Box Diturunkan Maksimal Lebarnya */
    .cover-wrapper-carousel { 
        position: relative; 
        width: 100%; 
        padding-top: 125%; /* Rasio 4:5 */ 
        overflow: hidden; 
        background-color: #f8f9fa;
    }
    .book-cover-carousel { 
        position: absolute; 
        top: 0; 
        left: 0; 
        width: 100%; 
        height: 100%; 
        object-fit: cover; 
        border-bottom: 1px solid #f1f5f9; 
    }
    
    .badge-kategori-carousel { 
        position: absolute; 
        top: 10px; 
        left: 10px; 
        background: rgba(11, 27, 53, 0.85); 
        backdrop-filter: blur(4px); 
        color: white; 
        border-radius: 6px; 
        font-size: 10px;
        padding: 3px 8px; 
        z-index: 5; 
    }

    .carousel-control-prev:hover span, .carousel-control-next:hover span {
        color: #0b1b35 !important;
    }

    /* Helper Grid Kustom untuk Menampilkan 5 Kolom Buku per Slide */
    .col-custom-5 {
        flex: 0 0 auto;
        width: 20%;
    }
    @media (max-width: 768px) {
        .col-custom-5 {
            width: 50%;
        }
    }
</style>

<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0">Dashboard</h4>
        <div class="text-muted small">
            <i class="bi bi-calendar3 me-2"></i>{{ \Carbon\Carbon::now()->isoFormat('dddd, DD MMMM YYYY') }}
        </div>
    </div>

    <div class="welcome-box shadow-sm mb-4">
        <h2 class="fw-bold mb-2">Halo, {{ auth()->user()->nama_lengkap }}! 👋</h2>
        <p class="mb-0 opacity-75">
            {{ auth()->user()->kelas }} - {{ auth()->user()->jurusan }} 
            <span class="mx-2">|</span> 
            NISN: {{ auth()->user()->nisn ?? '-' }}
        </p>
    </div>

    <!-- TIGA KOTAK STATISTIK UTAMA ANGGOTA -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="custom-card">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-book"></i></div>
                <h2 class="fw-bold mb-0">{{ $totalDipinjam }}</h2>
                <div class="text-muted small">Buku Sedang Dipinjam</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="custom-card">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-exclamation-triangle"></i></div>
                <h2 class="fw-bold mb-0 text-danger">Rp {{ number_format($totalDenda, 0, ',', '.') }}</h2>
                <div class="text-muted small">Total Denda Kamu</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="custom-card">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-check-circle"></i></div>
                <h2 class="fw-bold mb-0">{{ $totalDibaca }}</h2>
                <div class="text-muted small">Buku Telah Dibaca</div>
            </div>
        </div>
    </div>

    <!-- AREA INFORMASI PENTING (PINJAMAN AKTIF & ALERTI DENDA JIKA > 0) -->
    <div class="custom-card mb-4">
        <h5 class="fw-bold mb-3">Informasi Penting</h5>

        <!-- Peringatan Jika Anggota Memiliki Denda -->
        @if($totalDenda > 0)
            <div class="alert alert-danger border-0 mb-3 p-3 shadow-sm text-dark" style="border-radius: 12px; background-color: #fdf2f2;">
                <div class="d-flex align-items-start">
                    <i class="bi bi-exclamation-triangle-fill me-3 fs-4 text-danger"></i>
                    <div>
                        <strong class="d-block text-danger mb-1">Kamu Memiliki Tunggakan Denda! ⚠️</strong>
                        <span class="small text-muted d-block mb-2">Total denda berjalan akun kamu saat ini adalah <strong class="text-danger">Rp {{ number_format($totalDenda, 0, ',', '.') }}</strong>. Silakan segera lakukan penyelesaian administrasi.</span>
                        <div class="p-2 bg-white rounded border border-danger border-opacity-20 small text-secondary" style="font-size: 11.5px; font-style: italic;">
                            <i class="bi bi-info-circle text-danger me-1"></i> <strong>Cara Pembayaran:</strong> Silakan datangi meja pelayanan admin perpustakaan di SMKN 40 untuk melakukan pembayaran secara tunai kepada petugas perpustakaan yang berjaga. Pastikan meminta bukti lunas setelah membayar ya!
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Informasi Pinjaman Aktif -->
        @if(isset($pinjamanAktif) && $pinjamanAktif->isNotEmpty())
            <div class="alert alert-warning border-0 mb-0 p-3" style="border-radius: 12px;">
                <strong class="d-block mb-2 text-warning-emphasis">Kamu memiliki pinjaman buku aktif! 📖</strong>
                <div class="list-group list-group-flush bg-transparent">
                    @foreach($pinjamanAktif as $pinjam)
                        <div class="list-group-item bg-transparent border-0 d-flex justify-content-between align-items-center p-1 px-0">
                            <span class="small text-dark">- {{ $pinjam->buku->judul }}</span>
                            <a href="{{ route('anggota.peminjaman.struk', $pinjam->id) }}" class="btn btn-xs btn-outline-dark px-2 py-1 shadow-sm" style="border-radius: 6px; font-size: 12px; font-weight: 500;">
                                <i class="bi bi-download me-1"></i> Unduh Struk
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            @if($totalDenda <= 0)
                <div class="alert alert-success border-0 mb-0 p-3" style="border-radius: 12px;">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-3 fs-4 text-success"></i>
                        <div>
                            <strong class="d-block mb-1">Akun Kamu Bersih! ✨</strong>
                            <span class="text-muted small">Tidak ada denda atau tunggakan pengembalian yang perlu dibayar. Teruslah membaca dan kembalikan buku tepat waktu ya! Semangat belajarnya! 💪🔥</span>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>

    <!-- CAROUSEL BUKU TERPOPULER (100% CLONE DARI DASHBOARD ADMIN / GAMBAR 3) -->
    <div class="row g-3 mt-2">
        <div class="col-12">
            <div class="custom-card position-relative px-4 py-4">
                <h5 class="fw-bold mb-4"><i class="bi bi-fire text-danger me-2"></i>5 Koleksi Buku Terpopuler</h5>
                
                @if($bukuTerpopuler->isEmpty())
                <div class="p-5 text-center bg-light rounded-4 border border-dashed">
                    <i class="bi bi-images text-muted opacity-50 mb-2" style="font-size: 2.5rem;"></i>
                    <p class="text-muted mb-0 small">Belum ada data sirkulasi buku terpopuler saat ini.</p>
                </div>
                @else
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-3">
                    @foreach($bukuTerpopuler as $buku)
                    <div class="col">
                        <div class="book-card-carousel position-relative h-100">
                            <span class="badge-kategori-carousel">
                                {{ $buku->kategori->nama_kategori ?? 'Umum' }}
                            </span>

                            <div class="cover-wrapper-carousel">
                                @if($buku->cover)
                                    <img src="{{ asset('storage/covers/' . $buku->cover) }}" class="book-cover-carousel" alt="Cover Buku">
                                @else
                                    <div class="book-cover-carousel d-flex flex-column align-items-center justify-content-center bg-light text-muted">
                                        <i class="bi bi-book opacity-25 fs-2"></i>
                                        <span class="small opacity-50 fw-bold mt-1" style="font-size: 9px;">NO COVER</span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-grow-1 d-flex flex-column" style="padding: 12px !important;">
                                <h6 class="fw-bold mb-0 text-truncate" style="font-size: 13px;" title="{{ $buku->judul }}">{{ $buku->judul }}</h6>
                                <p class="text-muted mb-1 text-truncate" style="font-size: 11px;">{{ $buku->penulis }}</p>
                                
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="fw-semibold {{ $buku->stok > 0 ? 'text-success' : 'text-danger' }}" style="font-size: 10.5px;">
                                        <i class="bi {{ $buku->stok > 0 ? 'bi-check-circle' : 'bi-x-circle' }} me-1"></i>
                                        {{ $buku->stok > 0 ? 'Tersedia: ' . $buku->stok : 'Habis' }}
                                    </div>
                                </div>

                                <div class="mt-auto">
                                    <div class="text-center fw-bold text-dark" style="background-color: #ffc107; border-radius: 8px; font-size: 11px; padding: 6px 0;">
                                        <i class="bi bi-heart-fill text-danger me-1" style="font-size: 9px;"></i> {{ $buku->total_dipinjam }}x Dipinjam
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection