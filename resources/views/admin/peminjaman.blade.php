@extends('layouts.app')

@section('title', 'Transaksi Peminjaman')

@section('content')
<style>
    .btn-action {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 16px;
        border: none;
        transition: all 0.2s;
    }
    .btn-success-custom {
        background: #10b981;
        color: white;
    }
    .btn-success-custom:hover {
        background: #059669;
        color: white;
    }
    .btn-danger-custom {
        background: #ef4444;
        color: white;
    }
    .btn-danger-custom:hover {
        background: #dc2626;
        color: white;
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
            <h4 class="fw-bold m-0" style="color: #1e293b;">Persetujuan & Data Peminjaman Buku</h4>
        </div>
    </div>

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

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form action="{{ route('admin.peminjaman') }}" method="GET" class="row g-2 align-items-center">
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
                    <a href="{{ route('admin.peminjaman') }}" class="btn btn-secondary w-100" title="Reset Data">Reset</a>
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
                        <th class="text-center" style="width: 180px;">Status / Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($peminjamans as $pinjam)
                    <tr>
                        <td>{{ $loop->iteration + ($peminjamans->currentPage() - 1) * $peminjamans->perPage() }}</td>
                        
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
                            <a href="#" class="link-anggota" data-bs-toggle="modal" data-bs-target="#modalDetailAnggota{{ $pinjam->id }}">
                                {{ $pinjam->user->nama_lengkap ?? 'Nama Tidak Ditemukan' }}
                            </a>
                        </td>

                        <td>
                            <span class="fw-semibold text-dark">{{ $pinjam->buku->judul ?? 'Buku Tidak Ditemukan' }}</span>
                        </td>

                        <td>
                            {{ $pinjam->status == 'dipinjam' && $pinjam->tgl_pengajuan ? \Carbon\Carbon::parse($pinjam->tgl_pengajuan)->format('d/m/Y') : '-' }}
                        </td>

                        <td>
                            {{ $pinjam->status == 'dipinjam' && $pinjam->tgl_kembali_seharusnya ? \Carbon\Carbon::parse($pinjam->tgl_kembali_seharusnya)->format('d/m/Y') : '-' }}
                        </td>

                        <td class="text-center">
                            @if($pinjam->status == 'menunggu')
                                <div class="d-flex justify-content-center gap-2">
                                    <form action="{{ route('admin.peminjaman.acc', $pinjam->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success-custom btn-action" title="ACC Peminjaman">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.peminjaman.tolak', $pinjam->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menolak pengajuan ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger-custom btn-action" title="Tolak Peminjaman">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            @elseif($pinjam->status == 'dipinjam')
                                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2" style="border-radius: 6px; font-size: 12px; font-weight: 600;">
                                    <i class="bi bi-hourglass-split me-1"></i> Sedang Dipinjam
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                            Belum ada data pengajuan atau buku aktif yang dipinjam.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($peminjamans->hasPages())
            <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
                {{ $peminjamans->links() }}
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="modalDetailAnggota" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px;">
            <div class="modal-header bg-dark text-white" style="border-radius: 12px 12px 0 0;">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-card-heading me-2"></i>Detail Anggota & Staff</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <img id="user_avatar" src="" class="rounded-circle border" style="width: 90px; height: 90px; object-fit: cover; display:none;">
                    <div id="user_avatar_placeholder" class="rounded-circle bg-secondary text-white mx-auto d-flex align-items-center justify-content-center fw-bold fs-3" style="width: 90px; height: 90px;"></div>
                </div>
                <h5 class="fw-bold text-dark m-0" id="user_nama">-</h5>
                <p class="text-muted small mb-3">@<span id="user_username">-</span></p>
                
                <hr>
                
                <div class="row text-start small border rounded p-3 bg-light m-0">
                    <div class="col-6 mb-2"><strong>NISN:</strong> <span id="user_nisn" class="d-block text-secondary">-</span></div>
                    <div class="col-6 mb-2"><strong>No. Telepon:</strong> <span id="user_telp" class="d-block text-secondary">-</span></div>
                    <div class="col-6 mb-2"><strong>Kelas:</strong> <span id="user_kelas" class="d-block text-secondary">-</span></div>
                    <div class="col-6 mb-2"><strong>Jurusan:</strong> <span id="user_jurusan" class="d-block text-secondary">-</span></div>
                </div>

                <div class="mt-3 border rounded p-3" style="background: #fff8e1; border-color: #ffe082 !important;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-dark fw-bold small"><i class="bi bi-book-fill me-1 text-warning"></i> Buku Sedang Dipinjam:</span>
                        <span class="badge bg-dark fs-6" id="user_total_pinjam">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.btn-detail-user').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            
            fetch(`/admin/peminjaman/detail-anggota/${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('user_nama').innerText = data.nama_lengkap;
                    document.getElementById('user_username').innerText = data.username;
                    document.getElementById('user_nisn').innerText = data.nisn;
                    document.getElementById('user_kelas').innerText = data.kelas;
                    document.getElementById('user_jurusan').innerText = data.jurusan;
                    document.getElementById('user_telp').innerText = data.no_hp;
                    document.getElementById('user_total_pinjam').innerText = data.total_dipinjam;

                    const avatarImg = document.getElementById('user_avatar');
                    const avatarPlaceholder = document.getElementById('user_avatar_placeholder');

                    if (data.foto_profil) {
                        avatarImg.src = data.foto_profil;
                        avatarImg.style.display = 'block';
                        avatarPlaceholder.style.display = 'none';
                    } else {
                        avatarImg.style.display = 'none';
                        avatarPlaceholder.style.display = 'flex';
                        avatarPlaceholder.innerText = data.nama_lengkap.charAt(0).toUpperCase();
                    }

                    const modal = new bootstrap.Modal(document.getElementById('modalDetailAnggota'));
                    modal.show();
                })
                .catch(err => console.error("Gagal memuat data:", err));
        });
    });
</script>

@foreach($peminjamans as $pinjam)
<div class="modal fade" id="modalDetailAnggota{{ $pinjam->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow" style="border-radius: 20px;">
                                <div class="modal-body p-4 text-center">
                                    
                                    <div class="position-relative mb-3">
                                        @if($pinjam->user && $pinjam->user->foto_profil)
                                            <img src="{{ asset('storage/public/profil/' . $pinjam->user->foto_profil) }}" alt="Foto Profil" class="rounded-circle shadow-sm" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #fff;">
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
                                                        // Menghitung akumulasi denda yang pernah atau sedang dimiliki user ini
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