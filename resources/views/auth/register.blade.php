<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - Perpustakaan Digital SMKN 40 Jakarta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8fafc; min-height: 100vh; display: flex; align-items: center; padding: 40px 0; }
        .register-card { border: none; border-radius: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; background: white; }
        .btn-register { background: #f59e0b; color: white; border: none; border-radius: 12px; padding: 12px; font-weight: bold; width: 100%; transition: 0.3s; }
        .btn-register:hover { background: #d97706; color: white; }
        .form-control, .form-select { border-radius: 10px; padding: 10px 12px; border: 1px solid #e2e8f0; font-size: 0.95rem; }
        .form-control:focus, .form-select:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15); }
        .logo-placeholder { width: 64px; height: 64px; background: #e2e8f0; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; color: #64748b; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <div class="register-card p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="mb-2">
                        <img src="{{ asset('logo-smk.png') }}" alt="Logo" class="img-fluid" style="width: 64px; height: auto;" onerror="this.style.display='none'; document.getElementById('alt-logo').style.display='inline-flex';">
                        <div id="alt-logo" class="logo-placeholder" style="display: none;">
                            <i class="bi bi-mortarboard-fill" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold m-0">Register</h3>
                    <p class="text-muted small">Perpustakaan Digital SMKN 40 Jakarta</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger p-2 small rounded-3 mb-4">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('register.proses') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 pe-md-4 border-end">
                            <p class="fw-bold text-muted small mb-3"><i class="bi bi-key me-1"></i> Akses Akun</p>
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama Lengkap" value="{{ old('nama_lengkap') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Username</label>
                                <input type="text" name="username" class="form-control" placeholder="Username" value="{{ old('username') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="alamat@gmail.com" value="{{ old('email') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                            </div>
                        </div>

                        <div class="col-md-6 ps-md-4 mt-4 mt-md-0">
                            <p class="fw-bold text-muted small mb-3"><i class="bi bi-person-vcard me-1"></i> Data Siswa</p>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">NISN</label>
                                <input type="text" name="nisn" class="form-control" placeholder="NISN" value="{{ old('nisn') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Kelas</label>
                                <select name="kelas" class="form-select" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    <option value="X" {{ old('kelas') == 'X' ? 'selected' : '' }}>Kelas X</option>
                                    <option value="XI" {{ old('kelas') == 'XI' ? 'selected' : '' }}>Kelas XI</option>
                                    <option value="XII" {{ old('kelas') == 'XII' ? 'selected' : '' }}>Kelas XII</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Jurusan</label>
                                <select name="jurusan" class="form-select" required>
                                    <option value="">-- Pilih Jurusan --</option>
                                    <option value="RPL" {{ old('jurusan') == 'RPL' ? 'selected' : '' }}>Rekayasa Perangkat Lunak (RPL)</option>
                                    <option value="TKJ" {{ old('jurusan') == 'DKV 1' ? 'selected' : '' }}>Desain Komunikasi Visual 1 (DKV-1)</option>
                                    <option value="DKV" {{ old('jurusan') == 'DKV 2' ? 'selected' : '' }}>Desain Komunikasi Visual 2 (DKV-2)</option>
                                    <option value="DKV" {{ old('jurusan') == 'AK' ? 'selected' : '' }}>Akuntansi (AK)</option>
                                    <option value="DKV" {{ old('jurusan') == 'BR' ? 'selected' : '' }}>Bisnis Retail (BR)</option>
                                    <option value="DKV" {{ old('jurusan') == 'MP' ? 'selected' : '' }}>Manajemen Perkantoran (MP)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">No. Handphone</label>
                                <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx" value="{{ old('no_hp') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Alamat Rumah</label>
                                <textarea name="alamat" class="form-control" rows="1" placeholder="Alamat rumah lengkap" required>{{ old('alamat') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 text-muted">

                    <button type="submit" class="btn-register">DAFTAR SEBAGAI ANGGOTA</button>
                </form>

                <div class="text-center mt-3">
                    <p class="text-muted small mb-0">Sudah punya akun? <a href="{{ route('login') }}" class="text-warning fw-bold text-decoration-none">Login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>