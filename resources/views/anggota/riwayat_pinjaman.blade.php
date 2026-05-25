@extends('layouts.app')

@section('title', 'Riwayat & Pinjaman Buku')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="border-radius: 10px;">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert" style="border-radius: 10px;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0" style="color: #0b1b35;">Aktivitas Peminjaman Saya</h4>
            <p class="text-muted small m-0">Pantau status pengajuan, buku aktif, dan riwayat transaksi kamu.</p>
        </div>
        <span class="badge bg-dark px-3 py-2" style="border-radius: 8px;">
            <i class="bi bi-clock-history me-1"></i> Mode Anggota
        </span>
    </div>

    <div class="card border-0 shadow-sm mb-4 d-inline-block" style="border-radius: 15px;">
        <div class="card-body p-2">
            <ul class="nav nav-pills d-flex align-items-center gap-2" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="{{ route('anggota.riwayat_pinjaman', ['tab' => 'menunggu']) }}" 
                       class="nav-link py-2 px-3 d-flex align-items-center gap-2 {{ $tabaktif == 'menunggu' ? 'active bg-warning text-dark fw-bold' : 'text-muted' }}" 
                       style="border-radius: 10px; transition: all 0.3s; font-size: 13px;">
                        <i class="bi bi-hourglass-split fs-6"></i> Menunggu ACC
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('anggota.riwayat_pinjaman', ['tab' => 'dipinjam']) }}" 
                       class="nav-link py-2 px-3 d-flex align-items-center gap-2 {{ $tabaktif == 'dipinjam' ? 'active bg-primary text-white fw-bold' : 'text-muted' }}" 
                       style="border-radius: 10px; transition: all 0.3s; font-size: 13px;">
                        <i class="bi bi-book fs-6"></i> Sedang Dipinjam
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('anggota.riwayat_pinjaman', ['tab' => 'kembali']) }}" 
                       class="nav-link py-2 px-3 d-flex align-items-center gap-2 {{ $tabaktif == 'kembali' ? 'active bg-success text-white fw-bold' : 'text-muted' }}" 
                       style="border-radius: 10px; transition: all 0.3s; font-size: 13px;">
                        <i class="bi bi-check2-circle fs-6"></i> Riwayat Selesai
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="table-light text-secondary fw-semibold small" style="background-color: #f8fafc;">
                    <tr>
                        <th style="width: 80px;">Cover</th>
                        <th class="text-start">Informasi Buku</th>
                        <th>Tanggal Pinjam</th>
                        <th>Batas Kembali</th>
                        @if($tabaktif == 'dipinjam' || $tabaktif == 'kembali')
                            <th>Status / Denda</th>
                        @endif
                        @if($tabaktif == 'menunggu' || $tabaktif == 'dipinjam')
                            <th style="width: 140px;">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayats as $data)
                        @php
                            // Perhitungan denda keterlambatan berjalan
                            $hariIni = \Carbon\Carbon::now()->startOfDay();
                            $batasKembali = \Carbon\Carbon::parse($data->tanggal_pengembalian)->startOfDay();
                            $terlambat = $hariIni->diffInDays($batasKembali, false) < 0;
                            $selisihHari = abs($hariIni->diffInDays($batasKembali, false));
                        @endphp
                        <tr>
                            <td>
                                @if($data->buku->cover)
                                    <img src="{{ asset('storage/covers/' . $data->buku->cover) }}" alt="Cover" class="img-fluid rounded" style="width: 50px; height: 65px; object-fit: cover; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 65px; color: #cbd5e1;">
                                        <i class="bi bi-book" style="font-size: 20px;"></i>
                                    </div>
                                @endif
                            </td>

                            <td class="text-start">
                                <h6 class="fw-bold m-0 mb-1" style="color: #0b1b35; font-size: 14px;">{{ $data->buku->judul }}</h6>
                                <span class="text-muted small d-block"><i class="bi bi-person me-1"></i>Penulis: {{ $data->buku->penulis }}</span>
                            </td>

                            <td class="text-muted small">
                                {{ $data->tanggal_peminjaman ? \Carbon\Carbon::parse($data->tanggal_peminjaman)->translatedFormat('d/m/Y') : '-' }}
                            </td>

                            <td class="small fw-semibold {{ $tabaktif == 'dipinjam' && $terlambat ? 'text-danger' : 'text-muted' }}">
                                {{ \Carbon\Carbon::parse($data->tanggal_pengembalian)->translatedFormat('d/m/Y') }}
                                @if($tabaktif == 'dipinjam' && $terlambat)
                                    <span class="d-block text-danger badge bg-danger-subtle mt-1 small" style="font-size: 10px;">Telat {{ $selisihHari }} Hari</span>
                                @endif
                            </td>

                            @if($tabaktif == 'dipinjam')
                                <td>
                                    @if($terlambat)
                                        <span class="text-danger fw-bold d-block small">Rp {{ number_format($selisihHari * 1000, 0, ',', '.') }}</span>
                                        <small class="text-muted" style="font-size: 11px;">(Denda berjalan Rp 1.000/hari)</small>
                                    @else
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 11px;">Aman</span>
                                    @endif
                                </td>
                            @elseif($tabaktif == 'kembali')
                                <td>
                                    @if($data->status == 'ditolak')
                                        <span class="badge bg-danger text-white px-2 py-1" style="font-size: 11px;"><i class="bi bi-x-circle me-1"></i> Ditolak Admin</span>
                                    @else
                                        @if($data->denda > 0)
                                            <span class="text-danger fw-bold d-block small">Rp {{ number_format($data->denda, 0, ',', '.') }}</span>
                                            <span class="badge bg-danger text-white" style="font-size: 10px;">Sudah Dikembalikan</span>
                                        @else
                                            <span class="badge bg-success text-white px-2 py-1" style="font-size: 11px;"><i class="bi bi-check-all me-1"></i> Selesai</span>
                                        @endif
                                    @endif
                                </td>
                            @endif

                            @if($tabaktif == 'menunggu')
                                <td>
                                    <form action="{{ route('anggota.pinjam.batal', $data->id) }}" method="POST" onsubmit="return confirm('Apakah kamu yakin ingin membatalkan pengajuan peminjaman buku ini?')">
                                        @csrf
                                        @method('DELETE') <button type="submit" class="btn btn-sm btn-danger px-3 shadow-sm" style="border-radius: 8px; font-weight: 500;">
                                            <i class="bi bi-x-circle me-1"></i> Batal
                                        </button>
                                    </form>
                                </td>
                            @elseif($tabaktif == 'dipinjam')
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('anggota.peminjaman.struk', $riwayat->id) }}" class="btn btn-sm btn-outline-dark px-3 shadow-sm" style="border-radius: 8px; font-weight: 500;">
                                            <i class="bi bi-download me-1"></i> Unduh Struk
                                        </a>

                                        <span class="badge bg-light text-dark border d-flex align-items-center px-3" style="border-radius: 8px; font-weight: 500;">
                                            <i class="bi bi-info-circle me-1 text-primary"></i> Kembalikan ke Admin
                                        </span>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="100%" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox display-4 opacity-25 d-block mb-3"></i>
                                <p class="m-0 fw-medium">Tidak ada data transaksi peminjaman di tab ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $riwayats->links() }}
    </div>
</div>
@endsection