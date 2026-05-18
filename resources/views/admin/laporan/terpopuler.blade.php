@extends('layouts.app')

@section('title', 'Buku Terpopuler')

@section('content')
<div class="container-fluid p-0">
    <div class="mb-4">
        <h4 class="fw-bold m-0" style="color: #1e293b;">Buku Terpopuler</h4>
        <small class="text-muted">Daftar buku koleksi perpustakaan yang paling sering dipinjam oleh anggota</small>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                    <thead class="table-light" style="background-color: #f8fafc;">
                        <tr>
                            <th class="py-3 px-4 text-center" style="width: 70px; color: #64748b;">RANK</th>
                            <th class="py-3 text-center" style="width: 100px; color: #64748b;">COVER</th>
                            <th class="py-3" style="color: #64748b;">INFORMASI BUKU</th>
                            <th class="py-3 text-center" style="color: #64748b;">KATEGORI</th>
                            <th class="py-3 text-center" style="color: #64748b;">TOTAL DIPINJAM</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($buku_populer as $index => $b)
                        <tr>
                            <td class="text-center px-4">
                                @if($index == 0)
                                    <span class="badge bg-warning text-dark fs-6 p-2 shadow-sm" style="border-radius: 50%; width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-trophy-fill"></i></span>
                                @elseif($index == 1)
                                    <span class="badge bg-secondary text-white fs-6 p-2" style="border-radius: 50%; width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                                @elseif($index == 2)
                                    <span class="badge bg-danger text-white fs-6 p-2" style="border-radius: 50%; width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;">3</span>
                                @else
                                    <span class="fw-bold text-secondary fs-6">{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td class="text-center py-2">
                                @if($b->cover)
                                    <img src="{{ asset('storage/covers/' . $b->cover) }}" alt="Cover" class="img-thumbnail" style="width: 60px; height: 80px; object-fit: cover; border-radius: 6px;">
                                @else
                                    <div class="bg-light border text-muted d-flex align-items-center justify-content-center mx-auto" style="width: 60px; height: 80px; border-radius: 6px; font-size: 11px;">No Cover</div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold fs-6 mb-1" style="color: #1e293b;">{{ $b->judul }}</div>
                                <div class="text-muted small"><i class="bi bi-person me-1"></i>Penulis: {{ $b->penulis }}</div>
                                <div class="text-muted small"><i class="bi bi-building me-1"></i>Penerbit: {{ $b->penerbit }} ({{ $b->tahun_terbit }})</div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border px-2 py-1.5" style="border-radius: 6px;">{{ $b->kategori->nama_kategori }}</span>
                            </td>
                            <td class="text-center">
                                <div class="badge bg-warning text-dark px-3 py-2 fs-6 fw-bold shadow-sm" style="border-radius: 8px;">
                                    <i class="bi bi-heart-fill text-danger me-1.5"></i>{{ $b->total_dipinjam }} Kali
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-bookmark-x-fill display-6 block mb-2 text-secondary"></i>
                                <div>Belum ada histori peminjaman buku tercatat saat ini.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection