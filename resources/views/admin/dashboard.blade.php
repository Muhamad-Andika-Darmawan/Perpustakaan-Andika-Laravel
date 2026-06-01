@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<style>
        /* SINKRONISASI STYLE ASLI DENGAN PENYESUAIAN UKURAN LEBIH KECIL */
        .book-card-carousel {
            border: none; 
            border-radius: 14px; /* Disesuaikan dari 20px */
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
            font-size: 10px; /* Diperkecil */
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
                <h2 class="fw-bold mb-0">{{ $bukuDipinjamAktif }}</h2>
                <div class="text-muted small">Buku Dipinjam (Aktif)</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="custom-card">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-exclamation-triangle"></i></div>
                <h2 class="fw-bold mb-0">{{ $terlambatKembali }}</h2>
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
                    <a href="{{ route('admin.peminjaman') }}" class="btn-view-all">Lihat Semua</a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
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
                            @forelse($peminjamanTerbaru as $terbaru)
                            <tr>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $terbaru->user->nama_lengkap ?? 'Tidak Diketahui' }}</span>
                                </td>
                                <td><span class="text-secondary small fw-medium">{{ $terbaru->buku->judul ?? 'Buku Dihapus' }}</span></td>
                                <td>{{ $terbaru->tgl_pinjam ? \Carbon\Carbon::parse($terbaru->tgl_pinjam)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $terbaru->tgl_kembali_seharusnya ? \Carbon\Carbon::parse($terbaru->tgl_kembali_seharusnya)->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @if($terbaru->status == 'menunggu')
                                        <span class="badge bg-warning text-dark px-2 py-1 small">Menunggu</span>
                                    @elseif($terbaru->status == 'dipinjam')
                                        <span class="badge bg-primary px-2 py-1 small">Dipinjam</span>
                                    @elseif($terbaru->status == 'kembali')
                                        <span class="badge bg-success px-2 py-1 small">Kembali</span>
                                    @else
                                        <span class="badge bg-danger px-2 py-1 small">Ditolak</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada transaksi peminjaman di dalam sistem perpustakaan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="custom-card">
                <h5 class="fw-bold mb-4">Aksi Cepat</h5>
                
                <a href="{{ route('admin.katalog') }}" class="text-decoration-none text-dark">
                    <div class="quick-item d-flex align-items-center mb-3 p-2 rounded" style="transition: background 0.2s;">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning mb-0 me-3" style="width:40px; height:40px; display:flex; align-items:center; justify-content:center; border-radius:8px;">
                            <i class="bi bi-book-half" style="font-size: 16px;"></i>
                        </div>
                        <span class="fw-bold">Tambah Buku Baru</span>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </div>
                </a>

                <a href="{{ route('admin.anggota') }}" class="text-decoration-none text-dark">
                    <div class="quick-item d-flex align-items-center p-2 rounded" style="transition: background 0.2s;">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary mb-0 me-3" style="width:40px; height:40px; display:flex; align-items:center; justify-content:center; border-radius:8px;">
                            <i class="bi bi-person-plus"></i>
                        </div>
                        <span class="fw-bold">Daftarkan Anggota</span>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- SINKRONISASI CAROUSEL KOLEKSI BUKU TERPOPULER -->
    <div class="row g-3">
        <div class="col-12">
            <div class="custom-card position-relative px-4 py-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-fire text-danger me-2"></i>Koleksi Buku Terpopuler</h5>
                
                @if($bukuTerpopuler->isEmpty())
                <div class="p-5 text-center bg-light rounded-4 border border-dashed">
                    <i class="bi bi-images text-muted opacity-50 mb-2" style="font-size: 2.5rem;"></i>
                    <p class="text-muted mb-0 small">Belum ada data buku terpopuler. Data akan muncul setelah ada buku yang dipinjam.</p>
                </div>
                @else
                <!-- Bootstrap Carousel Component dengan padding samping agar tombol panah aman di luar kartu -->
                <div id="carouselBukuTerpopuler" class="carousel slide px-4" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <!-- chunk(5) agar dalam 1 slide memuat 5 kartu kecil yang rapi -->
                        @foreach($bukuTerpopuler->chunk(5) as $key => $chunkedBuku)
                        <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                            <div class="row justify-content-start g-3">
                                @foreach($chunkedBuku as $buku)
                                <div class="col-custom-5 mb-2">
                                    <div class="book-card-carousel position-relative">
                                        <!-- Badge Kategori Ukuran Compact -->
                                        <span class="badge-kategori-carousel">
                                            {{ $buku->kategori->nama_kategori ?? 'Umum' }}
                                        </span>

                                        <!-- Wrapper Cover Rasio 4:5 -->
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

                                        <!-- Detail Teks Diperkecil & Dibuat Lebih Rapat -->
                                        <div class="p-2.5 p-3 flex-grow-1 d-flex flex-column" style="padding: 12px !important;">
                                            <h6 class="fw-bold mb-0 text-truncate" style="font-size: 13px;" title="{{ $buku->judul }}">{{ $buku->judul }}</h6>
                                            <p class="text-muted mb-1 text-truncate" style="font-size: 11px;">{{ $buku->penulis }}</p>
                                            
                                            <!-- Info Ketersediaan Stok Mini -->
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div class="fw-semibold {{ $buku->stok > 0 ? 'text-success' : 'text-danger' }}" style="font-size: 10.5px;">
                                                    <i class="bi {{ $buku->stok > 0 ? 'bi-check-circle' : 'bi-x-circle' }} me-1"></i>
                                                    {{ $buku->stok > 0 ? 'Tersedia: ' . $buku->stok : 'Habis' }}
                                                </div>
                                            </div>

                                            <!-- Tombol Status Mini -->
                                            <div class="mt-auto">
                                                <div class="text-center fw-bold" style="background-color: #ffc107; border-radius: 8px; font-size: 11px; padding: 6px 0;">
                                                    <i class="bi bi-heart-fill text-danger me-1" style="font-size: 9px;"></i> {{ $buku->total_dipinjam }}x Dipinjam
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Navigator Slider di Sisi Luar Kiri & Kanan Card -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselBukuTerpopuler" data-bs-slide="prev" style="width: 35px; left: 0;">
                        <span class="bi bi-arrow-left-circle-fill text-secondary" style="font-size: 1.8rem;" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselBukuTerpopuler" data-bs-slide="next" style="width: 35px; right: 0;">
                        <span class="bi bi-arrow-right-circle-fill text-secondary" style="font-size: 1.8rem;" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection