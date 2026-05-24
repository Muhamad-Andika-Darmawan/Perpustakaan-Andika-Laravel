@extends('layouts.app')

@section('title', 'Data Anggota & Staff')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0" style="color: #0b1b35;">Daftar Anggota & Staff</h4>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form action="{{ route('anggota.data_anggota') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari Nama Lengkap atau NISN..." value="{{ $search }}" style="border-radius: 0 8px 8px 0;">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="kelas" class="form-select" style="border-radius: 8px;">
                        <option value="">Semua Kelas</option>
                        <option value="X" {{ $filter_kelas == 'X' ? 'selected' : '' }}>Kelas X</option>
                        <option value="XI" {{ $filter_kelas == 'XI' ? 'selected' : '' }}>Kelas XI</option>
                        <option value="XII" {{ $filter_kelas == 'XII' ? 'selected' : '' }}>Kelas XII</option>
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
                    <a href="{{ route('anggota.data_anggota') }}" class="btn btn-secondary w-100" title="Reset Data">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm mb-4" style="border-radius: 15px; border: none; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light" style="background-color: #f8fafc;">
                    <tr>
                        <th class="px-4 py-3 text-muted" style="width: 60px;">No</th>
                        <th class="py-3 text-muted text-center" style="width: 80px;">Profil</th>
                        <th class="py-3 text-muted">Nama Lengkap</th>
                        <th class="py-3 text-muted">Username</th>
                        <th class="py-3 text-muted">NISN</th>
                        <th class="py-3 text-muted">Kelas & Jurusan</th>
                        <th class="py-3 text-muted">Hak Akses</th>
                        <th class="px-4 py-3 text-muted text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                        <tr>
                            <td class="px-4 py-3 fw-bold text-muted">
                                {{ $users->firstItem() + $index }}
                            </td>
                            
                            <td class="py-3 text-center">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#detailUserModal{{ $user->id }}">
                                    @if($user->foto_profil)
                                        <img src="{{ asset('storage/profil/' . $user->foto_profil) }}" class="rounded-circle shadow-sm border" style="width: 38px; height: 38px; object-fit: cover;" alt="Profil">
                                    @else
                                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold text-uppercase mx-auto shadow-sm" style="width: 38px; height: 38px; font-size: 14px;">
                                            {{ substr($user->nama_lengkap, 0, 1) }}
                                        </div>
                                    @endif
                                </a>
                            </td>

                            <td class="py-3">
                                <a href="#" class="fw-bold text-decoration-none text-dark" data-bs-toggle="modal" data-bs-target="#detailUserModal{{ $user->id }}">
                                    {{ $user->nama_lengkap }}
                                </a>
                            </td>

                            <td class="py-3 text-secondary">@​{{ $user->username }}</td>

                            <td class="py-3 text-secondary font-monospace">
                                @if($user->role === 'admin')
                                    <span class="text-muted fw-bold">-</span>
                                @else
                                    {{ $user->nisn }}
                                @endif
                            </td>

                            <td class="py-3">
                                @if($user->role === 'admin')
                                    <span class="text-muted small italic">Bukan Siswa</span>
                                @else
                                    <span class="badge bg-light text-dark border px-2.5 py-1.5" style="font-size: 12px; border-radius: 6px;">
                                        {{ $user->kelas }} - {{ $user->jurusan }}
                                    </span>
                                @endif
                            </td>

                            <td class="py-3">
                                @if($user->role === 'admin')
                                    <span class="badge bg-danger px-2.5 py-1.5" style="font-size: 11px; border-radius: 6px;">
                                        <i class="bi bi-shield-lock-fill"></i> Admin
                                    </span>
                                @else
                                    <span class="badge bg-success px-2.5 py-1.5" style="font-size: 11px; border-radius: 6px;">
                                        <i class="bi bi-person-fill"></i> Anggota
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-center">
                                <button type="button" class="btn btn-outline-primary btn-sm" style="border-radius: 8px; font-size: 12px;" data-bs-toggle="modal" data-bs-target="#detailUserModal{{ $user->id }}">
                                    <i class="bi bi-eye"></i> Detail
                                </button>
                            </td>
                        </tr>

                        <div class="modal fade" id="detailUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content" style="border-radius: 20px; border: none;">
                                    <div class="modal-body p-4 text-center">
                                        <div class="mb-3 d-flex justify-content-center">
                                            @if($user->foto_profil)
                                                <img src="{{ asset('storage/profil/' . $user->foto_profil) }}" class="rounded-circle shadow-sm border" style="width: 100px; height: 100px; object-fit: cover;" alt="Profil">
                                            @else
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold text-uppercase shadow-sm" style="width: 100px; height: 100px; font-size: 36px;">
                                                    {{ substr($user->nama_lengkap, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>

                                        <h5 class="fw-bold text-dark mb-1">{{ $user->nama_lengkap }}</h5>
                                        <p class="text-warning small mb-3">@​{{ $user->username }}</p>

                                        <div class="mb-3">
                                            @if($user->role === 'admin')
                                                <span class="badge bg-light text-danger border px-3 py-1.5" style="font-size: 12px;">
                                                    Staff Administrator Pusat
                                                </span>
                                            @else
                                                <span class="badge bg-light text-dark border px-3 py-1.5" style="font-size: 12px;">
                                                    Siswa: {{ $user->kelas }} - {{ $user->jurusan }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="bg-light p-2.5 rounded-3 mb-2 text-start d-flex align-items-center gap-2">
                                            <i class="bi bi-telephone-fill text-primary ms-1"></i>
                                            <div>
                                                <small class="text-muted d-block" style="font-size: 10px; line-height: 1;">No. Telepon</small>
                                                <strong class="text-dark small">{{ $user->no_hp ?? 'Tidak ada nomor telepon' }}</strong>
                                            </div>
                                        </div>

                                        <div class="bg-light p-3 rounded-3 mb-4 text-start">
                                            <h6 class="fw-bold text-dark small mb-1"><i class="bi bi-chat-quote text-muted"></i> About Me:</h6>
                                            <p class="text-muted small mb-0" style="text-align: justify; line-height: 1.4;">
                                                @if($user->role === 'admin')
                                                    {{ $user->about_me ?? 'Halo! Saya adalah Administrator sistem yang bertugas mengelola sirkulasi buku dan manajemen anggota Perpustakaan Digital.' }}
                                                @else
                                                    {{ $user->about_me ?? 'Halo! Saya adalah salah satu anggota aktif di Perpustakaan Digital.' }}
                                                @endif
                                            </p>
                                        </div>

                                        <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal" style="border-radius: 12px; font-weight: bold; padding: 10px;">
                                            TUTUP PROFILE
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-people display-3 opacity-25 d-block mb-2"></i>
                                Pengguna yang kamu cari tidak ditemukan...
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection