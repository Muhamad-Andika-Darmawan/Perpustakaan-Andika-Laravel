@extends('layouts.app')

@section('title', 'Dashboard Anggota')

@section('content')
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

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="custom-card">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-book"></i></div>
                <h2 class="fw-bold mb-0">0</h2>
                <div class="text-muted small">Buku Sedang Dipinjam</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="custom-card">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-exclamation-triangle"></i></div>
                <h2 class="fw-bold mb-0">Rp 0</h2>
                <div class="text-muted small">Total Denda Kamu</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="custom-card">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-check-circle"></i></div>
                <h2 class="fw-bold mb-0">0</h2>
                <div class="text-muted small">Buku Telah Dibaca</div>
            </div>
        </div>
    </div>

    <div class="custom-card mb-4">
        <h5 class="fw-bold mb-3">Informasi Penting</h5>

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
    </div>

    <div class="row g-3">
        <div class="col-12">
            <div class="custom-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-fire text-danger me-2"></i>Rekomendasi Buku Terpopuler</h5>
                <div class="p-5 text-center bg-light rounded-4 border border-dashed">
                    <i class="bi bi-images text-muted opacity-50 mb-2" style="font-size: 2.5rem;"></i>
                    <p class="text-muted mb-0 small">Carousel koleksi buku terpopuler untuk anggota akan muncul di sini.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection