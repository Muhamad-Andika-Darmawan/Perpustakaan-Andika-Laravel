@extends('layouts.app')

@section('title', 'Daftar Denda Anggota')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0" style="color: #1e293b;">Data Denda Perpustakaan</h4>
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
            <form action="{{ route('admin.denda') }}" method="GET" class="row g-2 align-items-center">
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
                    <a href="{{ route('admin.denda') }}" class="btn btn-secondary w-100" title="Reset Data">Reset</a>
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
                        <th class="text-center">Buku Yang Terlibat</th>
                        <th class="text-center">Batas Kembali</th>
                        <th class="text-center">Tanggal Dikembalikan</th>
                        <th class="text-center">Total Denda</th>
                        <th class="text-center" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dendas as $index => $denda)
                    <tr>
                        <td class="ps-3 fw-bold text-secondary">{{ $dendas->firstItem() + $index }}</td>
                        <td>
                            <span class="fw-bold text-dark">{{ $denda->user->nama_lengkap ?? 'Tidak Ada' }}</span>
                            <br><small class="text-muted">@<span>{{ $denda->user->username ?? '-' }}</span></small>
                        </td>
                        <td class="fw-semibold text-secondary small">{{ $denda->buku->judul ?? 'Tidak Ada' }}</td>
                        <td>{{ \Carbon\Carbon::parse($denda->tgl_kembali_seharusnya)->translatedFormat('d M Y') }}</td>
                        <td>
                            @if($denda->tgl_pengembalian)
                                {{ \Carbon\Carbon::parse($denda->tgl_pengembalian)->translatedFormat('d M Y') }}
                            @else
                                <span class="text-danger italic small">Belum Dikembalikan</span>
                            @endif
                        </td>
                        <td class="text-danger fw-bold">Rp {{ number_format($denda->denda, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <form action="{{ route('admin.denda.lunas', $denda->id) }}" method="POST" onsubmit="return confirm('Anggota sudah membayar cash dan denda dianggap lunas?')">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm px-3 fw-bold" style="border-radius: 6px;">
                                    <i class="bi bi-cash-coin"></i> Lunasi
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-wallet2 fs-1 d-block mb-2 text-secondary"></i>
                            Keren! Tidak ada anggota yang memiliki tunggakan denda saat ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($dendas->hasPages())
            <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
                {{ $dendas->links() }}
            </div>
        @endif
    </div>
</div>
@endsection