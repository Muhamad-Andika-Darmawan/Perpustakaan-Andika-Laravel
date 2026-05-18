@extends('layouts.app')

@section('title', 'Buku Terpopuler')

@section('content')
<style>
    /* Mengadopsi Style Asli PHP Native dengan Sentuhan Modern */
    .custom-card {
        background: #ffffff;
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        padding: 20px;
        height: 100%;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .custom-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
    }

    .rank-badge {
        width: 50px;
        height: 50px;
        background: #1e293b;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: bold;
        flex-shrink: 0;
    }

    /* Variasi warna peringkat 1, 2, dan 3 agar lebih estetik */
    .rank-1 { background: linear-gradient(45deg, #f59e0b, #d97706); }
    .rank-2 { background: linear-gradient(45deg, #94a3b8, #475569); }
    .rank-3 { background: linear-gradient(45deg, #b45309, #78350f); }
</style>

<div class="container-fluid p-0">
    <div class="mb-4">
        <h4 class="fw-bold m-0" style="color: #1e293b;">10 Buku Paling Sering Dipinjam</h4>
    </div>

    <div class="row">
        @forelse($buku_populer as $index => $b)
            @php $rank = $index + 1; @endphp
            <div class="col-md-6 mb-4">
                <div class="card custom-card">
                    <div class="d-flex align-items-center gap-4">
                        
                        <div class="rank-badge {{ $rank == 1 ? 'rank-1' : ($rank == 2 ? 'rank-2' : ($rank == 3 ? 'rank-3' : '')) }}">
                            {{ $rank }}
                        </div>

                        @if($b->cover)
                            <img src="{{ asset('storage/covers/' . $b->cover) }}" 
                                 style="width: 70px; height: 100px; object-fit: cover; border-radius: 8px;" 
                                 class="shadow-sm" 
                                 alt="Cover {{ $b->judul }}">
                        @else
                            <div class="shadow-sm d-flex flex-column align-items-center justify-content-center bg-secondary text-white text-center" 
                                 style="width: 70px; height: 100px; border-radius: 8px;">
                                <i class="bi bi-book" style="font-size: 20px;"></i>
                                <span style="font-size: 9px; display: block;">No Cover</span>
                            </div>
                        @endif

                        <div class="flex-grow-1 min-width-0">
                            <h6 class="mb-1 fw-bold text-dark text-truncate" title="{{ $b->judul }}">
                                {{ $b->judul }}
                            </h6>
                            <p class="text-muted small mb-2 text-truncate">Penulis: {{ $b->penulis }}</p>
                            
                            <span class="badge bg-primary px-3 py-1.5" style="border-radius: 6px; font-size: 12px;">
                                <i class="bi bi-fire me-1"></i> Dipinjam {{ $b->total_dipinjam ?? 0 }} Kali
                            </span>
                        </div>

                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="bi bi-graph-down display-4 text-secondary mb-3"></i>
                <div>Belum ada data sikit sirkulasi peminjaman buku saat ini.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection