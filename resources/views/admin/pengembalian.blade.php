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
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary">
                    <tr>
                        <th class="text-center" width="5%">No</th>
                        <th class="text-center">Nama Anggota</th>
                        <th class="text-center">Judul Buku</th>
                        <th class="text-center">Tanggal Pinjam</th>
                        <th class="text-center">Batas Kembali</th>
                        <th class="text-center">Keterlambatan / Denda</th>
                        <th class="text-center" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengembalians as $index => $pinjam)
                    @php
                        $batas_kembali = \Carbon\Carbon::parse($pinjam->tgl_kembali_seharusnya);
                        $hari_ini = \Carbon\Carbon::now();
                        $is_terlambat = $hari_ini->gt($batas_kembali);
                        $selisih = $is_terlambat ? $hari_ini->diffInDays($batas_kembali) : 0;
                    @endphp
                    <tr>
                        <td class="ps-3 fw-bold text-secondary">{{ $pengembalians->firstItem() + $index }}</td>
                        <td>
                            <span class="fw-bold text-dark">{{ $pinjam->user->nama_lengkap ?? 'Tidak Ada' }}</span>
                            <br><small class="text-muted">{{ $pinjam->user->kelas ?? '-' }} - {{ $pinjam->user->jurusan ?? '-' }}</small>
                        </td>
                        <td class="fw-semibold text-dark">{{ $pinjam->buku->judul ?? 'Tidak Ada' }}</td>
                        <td>{{ \Carbon\Carbon::parse($pinjam->tgl_pinjam)->translatedFormat('d M Y') }}</td>
                        <td class="{{ $is_terlambat ? 'text-terlambat' : '' }}">{{ $batas_kembali->translatedFormat('d M Y') }}</td>
                        <td>
                            @if($is_terlambat)
                                <span class="text-danger fw-bold"><i class="bi bi-exclamation-triangle-fill"></i> Telat {{ $selisih }} Hari</span>
                                <br><small class="text-muted fw-bold">Denda: Rp {{ number_format($selisih * 1000, 0, ',', '.') }}</small>
                            @else
                                <span class="badge bg-success px-2 py-1" style="border-radius: 6px;"><i class="bi bi-shield-check"></i> Aman</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-center">
                                <form action="{{ route('admin.pengembalian.proses', $pinjam->id) }}" method="POST" onsubmit="return confirm('Proses pengembalian buku untuk anggota ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-dark btn-sm px-3" style="border-radius: 6px;">
                                        <i class="bi bi-arrow-return-left me-1 text-warning"></i> Kembalikan
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-book fs-1 d-block mb-2 text-secondary"></i>
                            Tidak ada buku yang sedang dipinjam oleh anggota saat ini.
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
@endsection