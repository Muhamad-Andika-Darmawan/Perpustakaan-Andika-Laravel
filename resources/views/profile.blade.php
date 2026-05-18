@extends('layouts.app')

@section('title', 'Pengaturan Profil')

@section('content')
<style>
    .profile-avatar-lg {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }
    .avatar-placeholder-lg {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        font-weight: bold;
        border: 4px solid #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        margin: auto;
    }
    .nav-pills .nav-link.active {
        background-color: #f59e0b !important;
        color: white !important;
    }
    .nav-pills .nav-link {
        color: #64748b;
        font-weight: 500;
    }
</style>

<div class="container-fluid p-0">
    <div class="mb-4">
        <h4 class="fw-bold m-0" style="color: #1e293b;">Pengaturan Profil</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 10px;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> Periksa kembali isian form Anda!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm text-center p-4" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="mb-3">
                        @if($user->foto_profil)
                            <img src="{{ asset('storage/profil/' . $user->foto_profil) }}" class="profile-avatar-lg" alt="Foto Profil">
                        @else
                            <div class="avatar-placeholder-lg">
                                {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <h5 class="fw-bold m-0 text-dark">{{ $user->nama_lengkap }}</h5>
                    <p class="text-muted small mb-2">@_{{ $user->username }}</p>
                    <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-warning text-dark' }} px-3 py-2 uppercase shadow-sm" style="border-radius: 8px;">
                        <i class="bi {{ $user->role === 'admin' ? 'bi-shield-lock-fill' : 'bi-person-fill' }} me-1"></i>
                        {{ strtoupper($user->role) }}
                    </span>

                    <hr class="my-4 text-secondary opacity-25">

                    <p class="text-start fw-bold mb-1 text-dark" style="font-size: 14px;">About Me</p>
                    <p class="text-start text-muted small italic-text">
                        {{ $user->about_me ?? 'Belum ada deskripsi singkat mengenai diri Anda. Tulis sesuatu di tab edit profil!' }}
                    </p>

                    <hr class="my-4 text-secondary opacity-25">

                    <div class="d-grid">
                        <form action="{{ route('profile.delete') }}" method="POST" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin menghapus akun ini secara permanen? Tindakan ini tidak dapat dibatalkan.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100 py-2 fw-semibold" style="border-radius: 10px;">
                                <i class="bi bi-trash3-fill me-1"></i> Hapus Akun Saya
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-2" style="border-radius: 15px;">
                <div class="card-header bg-transparent border-0 pt-3 px-3">
                    <ul class="nav nav-pills card-header-pills" id="profileTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="edit-profile-tab" data-bs-toggle="tab" data-bs-target="#edit-profile" type="button" role="tab"><i class="bi bi-person-lines-fill me-1"></i> Ubah Data Diri</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="change-password-tab" data-bs-toggle="tab" data-bs-target="#change-password" type="button" role="tab"><i class="bi bi-key-fill me-1"></i> Keamanan Password</button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-3">
                    <div class="tab-content" id="profileTabContent">
                        
                        <div class="tab-pane fade show active" id="edit-profile" role="tabpanel">
                            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-dark">Nama Lengkap</label>
                                        <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" required style="border-radius: 8px;">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-dark">Username</label>
                                        <input type="text" name="username" class="form-control" value="{{ old('username', $user->username) }}" required style="border-radius: 8px;">
                                    </div>

                                    @if($user->role === 'anggota')
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-dark">NISN</label>
                                        <input type="text" name="nisn" class="form-control" value="{{ old('nisn', $user->nisn) }}" style="border-radius: 8px;">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-dark">Kelas</label>
                                        <input type="text" name="kelas" class="form-control" value="{{ old('kelas', $user->kelas) }}" style="border-radius: 8px;">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-dark">Jurusan</label>
                                        <input type="text" name="jurusan" class="form-control" value="{{ old('jurusan', $user->jurusan) }}" style="border-radius: 8px;">
                                    </div>
                                    @endif

                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold text-dark">No. Telepon</label>
                                        <input type="text" name="no_telp" class="form-control" placeholder="Contoh : 081234567890"  value="{{ old('no_telp', $user->no_telp) }}" style="border-radius: 8px;">
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold text-dark">About Me (Deskripsi Singkat)</label>
                                        <textarea name="about_me" class="form-control" rows="3" style="border-radius: 8px;" placeholder="Ceritakan sedikit tentang dirimu... (misal: Hobi membaca buku fiksi)">{{ old('about_me', $user->about_me) }}</textarea>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold text-dark">Ganti Foto Profil</label>
                                        <input type="file" name="foto_profil" id="inputFotoProfil" class="form-control" accept="image/*" style="border-radius: 8px;">
                                        <div class="form-text text-muted" style="font-size: 11px;">Format berkas: JPG, JPEG, PNG. Maksimal ukuran: 2MB.</div>
                                        
                                        <div class="mt-3 d-none" id="framePreviewFoto">
                                            <p class="small text-muted mb-1">Pratinjau Foto Baru:</p>
                                            <img src="" id="previewFotoProfil" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 text-end">
                                    <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="change-password" role="tabpanel">
                            <form action="{{ route('profile.password') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold text-dark">Password Saat Ini (Lama)</label>
                                        <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required style="border-radius: 8px;">
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-dark">Password Baru</label>
                                        <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" required style="border-radius: 8px;">
                                        @error('new_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-dark">Konfirmasi Password Baru</label>
                                        <input type="password" name="new_password_confirmation" class="form-control" required style="border-radius: 8px;">
                                    </div>
                                </div>

                                <div class="mt-4 text-end">
                                    <button type="submit" class="btn btn-warning">Perbarui Password</button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Logika Live Preview Unggah Foto Profil Baru
    const inputFotoProfil = document.getElementById('inputFotoProfil');
    const framePreviewFoto = document.getElementById('framePreviewFoto');
    const previewFotoProfil = document.getElementById('previewFotoProfil');

    if (inputFotoProfil) {
        inputFotoProfil.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewFotoProfil.src = e.target.result;
                    framePreviewFoto.classList.remove('d-none');
                }
                reader.readAsDataURL(file);
            }
        });
    }
</script>
@endsection