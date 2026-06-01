@extends('layouts.app')

@section('title', 'Daftar Denda Anggota')

@section('content')
<style>
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
                        <option value="X" {{ request('tingkat') == 'X' ? 'selected' : '' }}>Kelas X</option>
                        <option value="XI" {{ request('tingkat') == 'XI' ? 'selected' : '' }}>Kelas XI</option>
                        <option value="XII" {{ request('tingkat') == 'XII' ? 'selected' : '' }}>Kelas XII</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="jurusan" class="form-select" style="border-radius: 8px;">
                        <option value="">Semua Jurusan</option>
                        <option value="RPL" {{ request('jurusan') == 'RPL' ? 'selected' : '' }}>Rekayasa Perangkat Lunak (RPL)</option>
                        <option value="DKV 1" {{ request('jurusan') == 'DKV 1' ? 'selected' : '' }}>Desain Komunikasi Visual 1 (DKV-1)</option>
                        <option value="DKV 2" {{ request('jurusan') == 'DKV 2' ? 'selected' : '' }}>Desain Komunikasi Visual 2 (DKV-2)</option>
                        <option value="AK" {{ request('jurusan') == 'AK' ? 'selected' : '' }}>Akuntansi (AK)</option>
                        <option value="BR" {{ request('jurusan') == 'BR' ? 'selected' : '' }}>Bisnis Retail (BR)</option>
                        <option value="MP" {{ request('jurusan') == 'MP' ? 'selected' : '' }}>Manajemen Perkantoran (MP)</option>
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
                <thead class="table-light text-secondary fw-semibold">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="width: 80px;" class="text-center">Profil</th>
                        <th>Nama Anggota</th>
                        <th>Buku Yang Terlibat</th>
                        <th>Batas Kembali</th>
                        <th>Tanggal Dikembalikan</th>
                        <th>Total Denda</th>
                        <th class="text-center" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dendas as $index => $denda)
                    <tr>
                        <td class="ps-3 fw-bold text-secondary">{{ $dendas->firstItem() + $index }}</td>
                        
                        <!-- Kolom Foto Profil seperti halaman Pengembalian -->
                        <td class="text-center">
                            @if($denda->user && $denda->user->foto_profil)
                                <img src="{{ asset('storage/profil/' . $denda->user->foto_profil) }}" alt="Profil" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #e2e8f0;">
                            @else
                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 40px; height: 40px; font-weight: 600;">
                                    {{ strtoupper(substr($denda->user->nama_lengkap ?? 'A', 0, 1)) }}
                                </div>
                            @endif
                        </td>

                        <!-- Link Nama Anggota Interaktif memicu Modal -->
                        <td>
                            <a href="#" class="link-anggota" data-bs-toggle="modal" data-bs-target="#modalDetailAnggotaDenda{{ $denda->id }}">
                                {{ $denda->user->nama_lengkap ?? 'Nama Tidak Ditemukan' }}
                            </a>
                            <br><small class="text-muted">@<span>{{ $denda->user->username ?? '-' }}</span></small>
                        </td>

                        <td class="fw-semibold text-secondary small">{{ $denda->buku->judul ?? 'Tidak Ada' }}</td>
                        <td>{{ \Carbon\Carbon::parse($denda->tgl_kembali_seharusnya)->translatedFormat('d/m/Y') }}</td>
                        <td>
                            @if($denda->tgl_pengembalian)
                                {{ \Carbon\Carbon::parse($denda->tgl_pengembalian)->translatedFormat('d/m/Y') }}
                            @else
                                <span class="text-danger italic small">Belum Dikembalikan</span>
                            @endif
                        </td>
                        <td class="text-danger fw-bold">Rp {{ number_format($denda->total_denda, 0, ',', '.') }}</td>
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
                        <td colspan="8" class="text-center text-muted py-5">
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

<!-- Render Modal Info Anggota secara Dinamis -->
@foreach($dendas as $denda)
<div class="modal fade" id="modalDetailAnggotaDenda{{ $denda->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-body p-4 text-center">
                <div class="position-relative mb-3">
                    @if($denda->user && $denda->user->foto_profil)
                        <img src="{{ asset('storage/profil/' . $denda->user->foto_profil) }}" alt="Foto Profil" class="rounded-circle shadow-sm" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #fff;">
                    @else
                        <div class="bg-dark text-white rounded-circle shadow-sm d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px; font-size: 36px; font-weight: bold; border: 3px solid #fff;">
                            {{ strtoupper(substr($denda->user->nama_lengkap ?? 'A', 0, 1)) }}
                        </div>
                    @endif
                    <span class="badge position-absolute bottom-0 start-50 translate-middle-x px-3 py-1 bg-dark text-warning border border-2 border-white small fw-bold" style="border-radius: 20px; font-size: 11px;">
                        {{ strtoupper($denda->user->role ?? 'ANGGOTA') }}
                    </span>
                </div>

                <h5 class="fw-bold text-dark mb-1">{{ $denda->user->nama_lengkap ?? 'Nama Tidak Ditemukan' }}</h5>
                <p class="text-warning small fw-semibold mb-4">@​{{ $denda->user->username ?? 'username' }}</p>

                <div class="card bg-light border-0 p-3 mb-4 text-start" style="border-radius: 12px;">
                    <table class="table table-borderless table-sm m-0 small text-secondary">
                        <tr>
                            <td class="fw-medium" style="width: 130px;">NISN</td>
                            <td style="width: 15px;">:</td>
                            <td class="text-dark fw-semibold">{{ $denda->user->nisn ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Tingkat Kelas</td>
                            <td>:</td>
                            <td class="text-dark fw-semibold">{{ $denda->user->kelas ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Jurusan</td>
                            <td>:</td>
                            <td class="text-dark fw-semibold">{{ $denda->user->jurusan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">No. Telepon</td>
                            <td>:</td>
                            <td class="text-dark fw-semibold">{{ $denda->user->no_hp ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Buku Dipinjam</td>
                            <td>:</td>
                            <td class="text-dark fw-semibold">
                                {{ \App\Models\Peminjaman::where('user_id', $denda->user_id)->where('status', 'dipinjam')->count() }} Buku Aktif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Total Denda</td>
                            <td>:</td>
                            <td>
                                @php
                                    $totalDendaUser = \App\Models\Peminjaman::where('user_id', $denda->user_id)->sum('total_denda');
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
                        {{ $denda->user->about_me ?? 'Halo! Saya adalah salah satu anggota aktif di Perpustakaan Digital.' }}
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