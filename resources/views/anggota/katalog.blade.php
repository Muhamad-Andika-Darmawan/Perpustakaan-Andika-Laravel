@extends('layouts.app')

@section('title', 'Katalog Buku')

@section('content')
<style>
    /* STYLE ASLI 100% AMBIL DARI KATALOG NATIVE KAMU */
    .book-card {
        border: none; 
        border-radius: 20px; 
        background: white; 
        transition: all 0.3s ease;
        height: 100%; 
        display: flex; 
        flex-direction: column; 
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .book-card:hover { 
        transform: translateY(-10px); 
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); 
    }
    
    /* Trik Aspek Rasio Murni 4:5 Pilihanmu */
    .cover-wrapper { 
        position: relative; 
        width: 100%; 
        padding-top: 125%; /* Rasio 4:5 */ 
        overflow: hidden; 
        background-color: #f8f9fa;
    }
    .book-cover { 
        position: absolute; 
        top: 0; 
        left: 0; 
        width: 100%; 
        height: 100%; 
        object-fit: cover; 
        border-bottom: 1px solid #f1f5f9; 
    }
    
    .badge-kategori { 
        position: absolute; 
        top: 15px; 
        left: 15px; 
        background: rgba(11, 27, 53, 0.85); 
        backdrop-filter: blur(4px); 
        color: white; 
        border-radius: 8px; 
        font-size: 11px; 
        padding: 5px 12px; 
        z-index: 5; 
    }
    .stok-status { 
        font-size: 12px; 
        font-weight: 600; 
    }
    
    /* Tombol Ikon Detail Melayang di Pojok Cover */
    .btn-detail-icon { 
        position: absolute; 
        top: 15px; 
        right: 15px; 
        width: 35px; 
        height: 35px; 
        background: rgba(255,255,255,0.8); 
        backdrop-filter: blur(4px); 
        color: #0b1b35; 
        border-radius: 10px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        border: none; 
        z-index: 5; 
        transition: 0.2s; 
    }
    .btn-detail-icon:hover { 
        background: #0b1b35; 
        color: white; 
    }
</style>

<div class="container-fluid p-0">
    <div class="mb-4">
        <h4 class="fw-bold m-0">Katalog Buku</h4>
        <p class="text-muted small mb-0">Temukan buku favoritmu di Perpustakaan 40</p>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-body p-3">
            <form action="{{ route('anggota.katalog') }}" method="GET" class="row g-3">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" style="border-radius: 10px;" placeholder="Cari judul, penulis, atau penerbit..." value="{{ $search }}">
                </div>
                <div class="col-md-3">
                    <select name="kategori" class="form-select" style="border-radius: 10px;">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->id }}" {{ $filterKategori == $kat->id ? 'selected' : '' }}>{{ $kat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100" style="border-radius: 10px;">Cari Buku</button>
                    <a href="{{ route('anggota.katalog') }}" class="btn btn-secondary w-100" style="border-radius: 10px;">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        @forelse($bukus as $buku)
            <div class="col-md-3 mb-4">
                <div class="book-card position-relative">
                    <span class="badge-kategori">{{ $buku->kategori->nama_kategori ?? 'Umum' }}</span>
                    
                    <button class="btn-detail-icon shadow-sm" data-bs-toggle="modal" data-bs-target="#detailModal{{ $buku->id }}">
                        <i class="bi bi-eye"></i>
                    </button>

                    <div class="cover-wrapper">
                        @if($buku->cover)
                            <img src="{{ asset('storage/covers/'.$buku->cover) }}" class="book-cover" alt="Cover Buku">
                        @else
                            <div class="book-cover d-flex flex-column align-items-center justify-content-center bg-light text-muted">
                                <i class="bi bi-book opacity-25 fs-1"></i>
                                <span class="small opacity-50 fw-bold mt-1" style="font-size: 10px;">NO COVER</span>
                            </div>
                        @endif
                    </div>

                    <div class="p-3 flex-grow-1 d-flex flex-column">
                        <h6 class="fw-bold mb-1 text-truncate" title="{{ $buku->judul }}">{{ $buku->judul }}</h6>
                        <p class="text-muted mb-2 text-truncate" style="font-size: 12px;">{{ $buku->penulis }} ({{ $buku->tahun_terbit }})</p>
                        
                        <div class="d-flex justify-content-between align-items-center mt-2 mb-3">
                            <div class="stok-status {{ $buku->stok > 0 ? 'text-success' : 'text-danger' }}">
                                <i class="bi {{ $buku->stok > 0 ? 'bi-check-circle' : 'bi-x-circle' }} me-1"></i>
                                {{ $buku->stok > 0 ? 'Tersedia: ' . $buku->stok : 'Stok Habis' }}
                            </div>
                        </div>

                        <div class="mt-auto">
                            @if($buku->stok > 0)
                                <form action="{{ route('anggota.pinjam.proses', $buku->id) }}" method="POST" onsubmit="return confirm('Apakah kamu yakin ingin mengajukan peminjaman buku ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold" style="background-color: #0b1b35; border: none; border-radius: 12px; padding: 8px;">
                                        <i class="bi bi-journal-plus me-1"></i> Pinjam Sekarang
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-secondary w-100 disabled" style="border-radius: 12px; padding: 10px;">
                                    STOK HABIS
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="detailModal{{ $buku->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border-radius: 20px; border: none;">
                        <div class="modal-body p-4">
                            <div class="row">
                                <div class="col-5">
                                    @if($buku->cover)
                                        <img src="{{ asset('storage/covers/'.$buku->cover) }}" class="img-fluid rounded-4 shadow-sm" style="aspect-ratio: 4/5; object-fit: cover;" alt="Cover">
                                    @else
                                        <div class="w-100 d-flex align-items-center justify-content-center bg-light rounded-4 border" style="aspect-ratio: 4/5;">
                                            <i class="bi bi-book text-muted opacity-50 fs-1"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-7">
                                    <h5 class="fw-bold text-dark">{{ $buku->judul }}</h5>
                                    <table class="table table-borderless table-sm small mb-3 w-auto" style="margin-top: 15px;">
                                        <tr>
                                            <td class="text-muted text-start text-nowrap" style="width: 75px; padding: 3px 0 !important; border: none !important;">Penulis</td>
                                            <td class="text-start" style="padding: 3px 0 !important; font-size: 12px; !important; border: none !important;">: {{ $buku->penulis }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted text-start text-nowrap" style="padding: 3px 0 !important; border: none !important;">Penerbit</td>
                                            <td class="text-start" style="padding: 3px 0 !important; font-size: 12px; !important; border: none !important;">: {{ $buku->penerbit }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted text-start text-nowrap" style="padding: 3px 0 !important; border: none !important;">Tahun</td>
                                            <td class="text-start" style="padding: 3px 0 !important; font-size: 12px; !important; border: none !important;">: {{ $buku->tahun_terbit }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted text-start text-nowrap align-middle" style="padding: 3px 0 !important; border: none !important;">Kategori</td>
                                            <td class="text-start align-middle" style="padding: 3px 0 !important; border: none !important;">: <span class="badge bg-secondary" style="font-size: 12px; padding: 3px 6px;">{{ $buku->kategori->nama_kategori ?? 'Umum' }}</span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <hr>
                            <h6 class="fw-bold text-dark">Sinopsis / Deskripsi:</h6>
                            <p class="text-muted small text-justify" style="max-height: 120px; overflow-y: auto;">
                                {{ $buku->deskripsi ?? 'Tidak ada deskripsi untuk buku ini.' }}
                            </p>
                            <div class="mt-4 row g-2">
                                <div class="col-8">
                                    @if($buku->stok > 0)
                                        <form action="{{ route('anggota.pinjam.proses', $buku->id) }}" method="POST" onsubmit="return confirm('Apakah kamu yakin ingin mengajukan peminjaman buku ini?')">
                                            @csrf
                                            <button type="submit" class="btn btn-warning w-100 shadow-sm" style="border-radius:12px; font-weight: bold; color: #0b1b35;">
                                                PINJAM SEKARANG
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                <div class="col-4">
                                    <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal" style="border-radius:12px;">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-book-half display-1 text-muted opacity-25"></i>
                <p class="mt-3 text-muted">Yah, buku yang kamu cari tidak ditemukan...</p>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $bukus->links() }}
    </div>
</div>
@endsection