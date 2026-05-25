@extends('layouts.app')

@section('title', 'Transaksi Pengembalian')

@section('content')
<style>
    .btn-action {
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-weight: 600;
        border: none;
        transition: all 0.2s;
    }
    .text-terlambat {
        color: #ef4444 !important;
        font-weight: bold;
    }
    .link-anggota {
        color: #1e293b;
        text-decoration: none;
        font-weight: 600;
    }
    .link-anggota:hover {
        color: #f59e0b;
        text-decoration: underline;
    }
</style>

<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0" style="color: #1e293b;">Manajemen Pengembalian Buku</h4>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="border-radius: 10px;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form action="{{ route('admin.pengembalian') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama anggota atau judul buku..." value="{{ $search }}" style="border-radius: 0 8px 8px 0;">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="tingkat" class="form-select" style="border-radius: 8px;">
                        <option value="">Semua Kelas</option>
                        <option value="X" {{ $filter_tingkat == 'X' ? 'selected' : '' }}>Kelas X</option>
                        <option value="XI" {{ $filter_tingkat == 'XI' ? 'selected' : '' }}>Kelas XI</option>
                        <option value="XII" {{ $filter_tingkat == 'XII' ? 'selected' : '' }}>Kelas XII</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="jurusan" class="form-select" style="border-radius: 8px;">
                        <option value="">Semua Jurusan</option>
                        <option value="RPL" {{ old('jurusan') == 'RPL' ? 'selected' : '' }}>Rekayasa Perangkat Lunak (RPL)</option>
                        <option value="DKV 1" {{ old('jurusan') == 'DKV 1' ? 'selected' : '' }}>Desain Komunikasi Visual 1 (DKV-1)</option>
                        <option value="DKV 2" {{ old('jurusan') == 'DKV 2' ? 'selected' : '' }}>Desain Komunikasi Visual 2 (DKV-2)</option>
                        <option value="AK" {{ old('jurusan') == 'AK' ? 'selected' : '' }}>Akuntansi (AK)</option>
                        <option value="BR" {{ old('jurusan') == 'BR' ? 'selected' : '' }}>Bisnis Retail (BR)</option>
                        <option value="MP" {{ old('jurusan') == 'MP' ? 'selected' : '' }}>Manajemen Perkantoran (MP)</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Cari</button>
                    <a href="{{ route('admin.pengembalian') }}" class="btn btn-secondary w-100" title="Reset Data">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead class="bg-light text-secondary fw-semibold">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="width: 80px;" class="text-center">Profil</th>
                        <th>Nama Anggota</th>
                        <th>Judul Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali</th>
                        <th>Keterlambatan / Denda</th>
                        <th class="text-center" style="width: 150px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- LOOP UTAMA: Hanya merender baris DATA TABEL --}}
                    @forelse($pengembalians as $pinjam)
                    <tr>
                        <td>{{ $loop->iteration + ($pengembalians->currentPage() - 1) * $pengembalians->perPage() }}</td>
                        
                        <td class="text-center">
                            @if($pinjam->user && $pinjam->user->foto_profil)
                                <img src="{{ asset('storage/' . $pinjam->user->foto_profil) }}" alt="Profil" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #e2e8f0;">
                            @else
                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 40px; height: 40px; font-weight: 600;">
                                    {{ strtoupper(substr($pinjam->user->nama_lengkap ?? 'A', 0, 1)) }}
                                </div>
                            @endif
                        </td>

                        <td>
                            <a href="#" class="link-anggota" data-bs-toggle="modal" data-bs-target="#modalDetailAnggotaReturn{{ $pinjam->id }}">
                                {{ $pinjam->user->nama_lengkap ?? 'Nama Tidak Ditemukan' }}
                            </a>
                        </td>

                        <td><span class="fw-semibold text-dark">{{ $pinjam->buku->judul ?? 'Buku Tidak Ditemukan' }}</span></td>

                        <td>{{ $pinjam->tgl_pengajuan ? \Carbon\Carbon::parse($pinjam->tgl_pengajuan)->format('d/m/Y') : '-' }}</td>

                        <td>{{ $pinjam->tgl_kembali_seharusnya ? \Carbon\Carbon::parse($pinjam->tgl_kembali_seharusnya)->format('d/m/Y') : '-' }}</td>

                        <td>
                            <span class="text-success fw-medium">Tepat Waktu</span>
                        </td>

                        <td class="text-center">
                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2" style="border-radius: 6px; font-size: 12px; font-weight: 600;">
                                <i class="bi bi-check-circle-fill me-1"></i> Sudah Dikembalikan
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-book fs-1 d-block mb-2 text-secondary"></i>
                            Belum ada data riwayat pengembalian buku dari anggota.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pengembalians->hasPages())
            <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
                {{ $pengembalians->links() }}
            </div>
        @endif
    </div>
</div>

@foreach($pengembalians as $pinjam)
<div class="modal fade" id="modalDetailAnggotaReturn{{ $pinjam->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-body p-4 text-center">
                <div class="position-relative mb-3">
                    @if($pinjam->user && $pinjam->user->foto_profil)
                        <img src="{{ asset('storage/' . $pinjam->user->foto_profil) }}" alt="Foto Profil" class="rounded-circle shadow-sm" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #fff;">
                    @else
                        <div class="bg-dark text-white rounded-circle shadow-sm d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px; font-size: 36px; font-weight: bold; border: 3px solid #fff;">
                            {{ strtoupper(substr($pinjam->user->nama_lengkap ?? 'A', 0, 1)) }}
                        </div>
                    @endif
                    <span class="badge position-absolute bottom-0 start-50 translate-middle-x px-3 py-1 bg-dark text-warning border border-2 border-white small fw-bold" style="border-radius: 20px; font-size: 11px;">
                        {{ strtoupper($pinjam->user->role ?? 'ANGGOTA') }}
                    </span>
                </div>

                <h5 class="fw-bold text-dark mb-1">{{ $pinjam->user->nama_lengkap ?? 'Nama Tidak Ditemukan' }}</h5>
                <p class="text-warning small fw-semibold mb-4">@​{{ $pinjam->user->username ?? 'username' }}</p>

                <div class="card bg-light border-0 p-3 mb-4 text-start" style="border-radius: 12px;">
                    <table class="table table-borderless table-sm m-0 small text-secondary">
                        <tr>
                            <td class="fw-medium" style="width: 130px;">NISN</td>
                            <td style="width: 15px;">:</td>
                            <td class="text-dark fw-semibold">{{ $pinjam->user->nisn ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Tingkat Kelas</td>
                            <td>:</td>
                            <td class="text-dark fw-semibold">{{ $pinjam->user->kelas ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Jurusan</td>
                            <td>:</td>
                            <td class="text-dark fw-semibold">{{ $pinjam->user->jurusan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">No. Telepon</td>
                            <td>:</td>
                            <td class="text-dark fw-semibold">{{ $pinjam->user->no_hp ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Buku Dipinjam</td>
                            <td>:</td>
                            <td class="text-dark fw-semibold">
                                {{ \App\Models\Peminjaman::where('user_id', $pinjam->user_id)->where('status', 'dipinjam')->count() }} Buku Aktif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Total Denda</td>
                            <td>:</td>
                            <td>
                                @php
                                    $totalDendaUser = \App\Models\Peminjaman::where('user_id', $pinjam->user_id)->sum('total_denda');
                                @endphp
                                @if($totalDendaUser > 0)
                                    <span class="text-danger fw-bold">Rp {{ number_format($totalDendaUser, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-success fw-semibold">Rp 0 (Bersih ✨)</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="mb-4 text-start px-1">
                    <h6 class="fw-bold text-dark small mb-1">Tentang Pengguna :</h6>
                    <p class="text-muted small m-0 italic" style="line-height: 1.5;">
                        {{ $pinjam->user->about_me ?? 'Halo! Saya adalah salah satu anggota aktif di Perpustakaan Digital.' }}
                    </p>
                </div>

                <button type="button" class="btn btn-secondary w-100 py-2 fw-bold" data-bs-dismiss="modal" style="border-radius: 12px; font-size: 13px;">
                    TUTUP PROFIL
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection