@extends('layouts.app')

@section('title', 'Kelola Data Anggota')

@section('content')
<style>
.btn-action{width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:16px;border:none;}
.btn-delete{background:#e63946;color:white;}
.btn-edit{background:#f59e0b;color:white;}
.avatar-table{width:40px;height:40px;border-radius:50%;object-fit:cover;}
.avatar-placeholder{width:40px;height:40px;border-radius:50%;background:#e2e8f0;color:#64748b;display:flex;align-items:center;justify-content:center;font-weight:bold;margin:auto;}
</style>

<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0" style="color: #1e293b;">Data Anggota & Staff</h4>
        </div>
        <button type="button" class="btn btn-warning shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahAnggota">
            <i class="bi bi-plus-lg"></i> Daftarkan Anggota
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 10px;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form action="{{ route('admin.anggota') }}" method="GET" class="row g-2 align-items-center">
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
                    <a href="{{ route('admin.anggota') }}" class="btn btn-secondary w-100" title="Reset Data">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0" style="font-size: 13px;">
                <thead class="table-light text-secondary">
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th class="text-center" style="width: 70px;">Profil</th>
                        <th class="text-center">Nama Lengkap</th>
                        <th  class="text-center">Username & Email</th>
                        <th class="text-center">NISN</th>
                        <th class="text-center">Kelas & Jurusan</th>
                        <th class="text-center">No. Telp</th>
                        <th class="text-center" style="width: 90px;">Hak Akses</th>
                        <th class="text-center" style="width: 110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                    <tr>
                        <td class="text-center text-muted">{{ $users->firstItem() + $index }}</td>
                        <td class="text-center">
                            @if($user->foto_profil)
                                <img src="{{ asset('storage/profil/'.$user->foto_profil) }}" alt="Foto" class="avatar-table shadow-sm">
                            @else
                                <div class="avatar-placeholder">
                                    {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="fw-bold text-dark" style="font-size: 14px;">{{ $user->nama_lengkap }}</span>
                        </td>
                        <td>
                            <span class="text-dark d-block fw-medium">{{ $user->username }}</span>
                            <small class="text-muted" style="font-size: 11px;">{{ $user->email }}</small>
                        </td>
                        <td>
                            <span class="text-dark">{{ $user->nisn ?? '-' }}</span>
                        </td>
                        <td>
                            @if($user->kelas && $user->jurusan)
                                <span class="text-dark">{{ $user->kelas }} - {{ $user->jurusan }}</span>
                            @else
                                <span class="text-muted italic small">Bukan Siswa</span>
                            @endif
                        </td>
                        <td class="text-dark">{{ $user->no_hp ?? '-' }}</td>
                        <td class="text-center">
                            @if($user->role == 'admin')
                                <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 fw-bold" style="font-size: 11px;">Admin</span>
                            @else
                                <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 fw-bold" style="font-size: 11px;">Anggota</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <button class="btn-action btn-edit text-white" 
                                        title="Edit Anggota"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditAnggota"
                                        data-id="{{ $user->id }}"
                                        data-username="{{ $user->username }}"
                                        data-email="{{ $user->email }}"
                                        data-nama="{{ $user->nama_lengkap }}"
                                        data-role="{{ $user->role }}"
                                        data-nisn="{{ $user->nisn }}"
                                        data-kelas="{{ $user->kelas }}"
                                        data-jurusan="{{ $user->jurusan }}"
                                        data-telp="{{ $user->no_hp }}"
                                        data-alamat="{{ $user->alamat }}"
                                        data-foto="{{ $user->foto_profil ? asset('storage/profil/'.$user->foto_profil) : '' }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('admin.anggota.delete', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data pengguna ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete" title="Hapus Anggota"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-people opacity-20 d-block mb-2" style="font-size: 2.5rem;"></i>
                            Tidak ada data anggota atau staff yang terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4 p-3">
            <small class="text-muted">Menampilkan {{ $users->firstItem() ?? 0 }} sampai {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} data</small>
            <div>
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahAnggota" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header border-bottom pt-3 pb-3 px-4 bg-light" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <h5 class="modal-title fw-bold m-0 text-dark">Daftarkan Anggota / Staff Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 12px;"></button>
            </div>
            <form action="{{ route('admin.anggota.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body px-4 py-3">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">Username</label>
                            <input type="text" name="username" class="form-control" placeholder="Masukkan username" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="alamat@email.com" required style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama sesuai KTP/Kartu Pelajar" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">Hak Akses Sistem</label>
                            <select name="role" id="roleInput" class="form-select" required style="border-radius: 8px;">
                                <option value="anggota">Anggota (Siswa)</option>
                                <option value="admin">Admin (Petugas Perpustakaan)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3 text-fields-siswa">
                            <label class="form-label small fw-bold text-secondary">NISN</label>
                            <input type="text" name="nisn" class="form-control" placeholder="NISN" style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="row text-fields-siswa">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">Kelas</label>
                            <select name="kelas" class="form-select" style="border-radius: 8px;">
                                <option value="">Pilih Kelas</option>
                                <option value="X">X (Sepuluh)</option>
                                <option value="XI">XI (Sebelas)</option>
                                <option value="XII">XII (Duabelas)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">Jurusan</label>
                            <select name="jurusan" class="form-select" style="border-radius: 8px;">
                                <option value="">Pilih Jurusan</option>
                                <option value="RPL" {{ old('jurusan') == 'RPL' ? 'selected' : '' }}>Rekayasa Perangkat Lunak (RPL)</option>
                                <option value="DKV 1" {{ old('jurusan') == 'DKV 1' ? 'selected' : '' }}>Desain Komunikasi Visual 1 (DKV-1)</option>
                                <option value="DKV 2" {{ old('jurusan') == 'DKV 2' ? 'selected' : '' }}>Desain Komunikasi Visual 2 (DKV-2)</option>
                                <option value="AK" {{ old('jurusan') == 'AK' ? 'selected' : '' }}>Akuntansi (AK)</option>
                                <option value="BR" {{ old('jurusan') == 'BR' ? 'selected' : '' }}>Bisnis Retail (BR)</option>
                                <option value="MP" {{ old('jurusan') == 'MP' ? 'selected' : '' }}>Manajemen Perkantoran (MP)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label small fw-bold text-secondary">No. Telepon / WhatsApp</label>
                            <input type="text" name="no_hp" id="edit_telp" class="form-control" placeholder="Contoh : 081234567890" style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control" rows="2" placeholder="Tulis alamat rumah lengkap" style="border-radius: 8px;"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label class="form-label small fw-bold text-secondary">Upload Foto Profil</label>
                            <input type="file" name="foto_profil" id="profilInput" class="form-control" accept="image/*" style="border-radius: 8px;">
                        </div>
                        <div class="col-12 mb-2 d-none" id="profilPreviewContainer">
                            <div class="p-2 border rounded-3 bg-light d-inline-block">
                                <img id="profilPreview" src="" alt="Pratinjau" style="max-height: 100px; width: 100px; border-radius: 50%; object-fit: cover;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-3 pt-0 px-4 d-flex gap-2">
                    <button type="submit" class="btn btn-warning">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditAnggota" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header border-bottom pt-3 pb-3 px-4 bg-light" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <h5 class="modal-title">Edit Data Anggota / Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 12px;"></button>
            </div>
            <form id="formEditAnggota" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body px-4 py-3">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">Username</label>
                            <input type="text" name="username" id="edit_username" class="form-control" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control" required style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="edit_nama_lengkap" class="form-control" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">Password Baru <span class="text-muted" style="font-size:11px;">(Biarkan kosong jika tidak diganti)</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Isi hanya jika ingin reset sandi" style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">Hak Akses Sistem</label>
                            <select name="role" id="edit_role" class="form-select" required style="border-radius: 8px;">
                                <option value="anggota">Anggota (Siswa)</option>
                                <option value="admin">Admin (Petugas Perpustakaan)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3 edit-fields-siswa">
                            <label class="form-label small fw-bold text-secondary">NISN</label>
                            <input type="text" name="nisn" id="edit_nisn" class="form-control" placeholder="NISN" style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="row edit-fields-siswa">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">Kelas</label>
                            <select name="kelas" id="edit_kelas" class="form-select" style="border-radius: 8px;">
                                <option value="">Pilih Kelas</option>
                                <option value="X">X (Sepuluh)</option>
                                <option value="XI">XI (Sebelas)</option>
                                <option value="XII">XII (Duabelas)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">Jurusan</label>
                            <select name="jurusan" id="edit_jurusan" class="form-select" style="border-radius: 8px;">
                                <option value="">Pilih Jurusan</option>
                                <option value="RPL" {{ old('jurusan') == 'RPL' ? 'selected' : '' }}>Rekayasa Perangkat Lunak (RPL)</option>
                                <option value="DKV 1" {{ old('jurusan') == 'DKV 1' ? 'selected' : '' }}>Desain Komunikasi Visual 1 (DKV-1)</option>
                                <option value="DKV 2" {{ old('jurusan') == 'DKV 2' ? 'selected' : '' }}>Desain Komunikasi Visual 2 (DKV-2)</option>
                                <option value="AK" {{ old('jurusan') == 'AK' ? 'selected' : '' }}>Akuntansi (AK)</option>
                                <option value="BR" {{ old('jurusan') == 'BR' ? 'selected' : '' }}>Bisnis Retail (BR)</option>
                                <option value="MP" {{ old('jurusan') == 'MP' ? 'selected' : '' }}>Manajemen Perkantoran (MP)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label small fw-bold text-secondary">No. Telepon / WhatsApp</label>
                            <input type="text" name="no_hp" id="edit_telp" class="form-control" placeholder="Contoh : 081234567890" style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Alamat Lengkap</label>
                        <textarea name="alamat" id="edit_alamat" class="form-control" rows="2" placeholder="Tulis alamat rumah lengkap" style="border-radius: 8px;"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label class="form-label small fw-bold text-secondary">Ubah Foto Profil <span class="text-muted" style="font-size:11px;">(Kosongkan jika tidak ingin diubah)</span></label>
                            <input type="file" name="foto_profil" id="editProfilInput" class="form-control" accept="image/*" style="border-radius: 8px;">
                        </div>
                        <div class="col-12 mb-2" id="editProfilPreviewContainer">
                            <div class="p-2 border rounded-3 bg-light d-inline-block">
                                <img id="editProfilPreview" src="" alt="Profil Pengguna" style="max-height: 100px; width: 100px; border-radius: 50%; object-fit: cover;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-3 pt-0 px-4 d-flex gap-2">
                    <button type="submit" class="btn btn-warning">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Live Preview Unggah Profil Modal Tambah
    const profilInput = document.getElementById("profilInput");
    if(profilInput) {
        profilInput.addEventListener("change", function() {
            const file = this.files[0];
            if(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById("profilPreview").src = e.target.result;
                    document.getElementById("profilPreviewContainer").classList.remove("d-none");
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // Logic Menyembunyikan/Menampilkan Field Siswa Berdasarkan Pilihan Role
    const roleInput = document.getElementById("roleInput");
    if(roleInput){
        roleInput.addEventListener("change", function(){
            const fields = document.querySelectorAll(".text-fields-siswa");
            fields.forEach(el => {
                if(this.value === 'admin') {
                    el.classList.add('d-none');
                } else {
                    el.classList.remove('d-none');
                }
            });
        });
    }

    const editRole = document.getElementById("edit_role");
    if(editRole){
        editRole.addEventListener("change", function(){
            const fields = document.querySelectorAll(".edit-fields-siswa");
            fields.forEach(el => {
                if(this.value === 'admin') {
                    el.classList.add('d-none');
                } else {
                    el.classList.remove('d-none');
                }
            });
        });
    }

    // Injeksi Data ke Dalam Modal Pop-Up Edit Anggota
    const modalEditAnggota = document.getElementById('modalEditAnggota');
    if (modalEditAnggota) {
        modalEditAnggota.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            
            const id = button.getAttribute('data-id');
            const username = button.getAttribute('data-username');
            const email = button.getAttribute('data-email');
            const nama = button.getAttribute('data-nama');
            const role = button.getAttribute('data-role');
            const nisn = button.getAttribute('data-nisn');
            const kelas = button.getAttribute('data-kelas');
            const jurusan = button.getAttribute('data-jurusan');
            const telp = button.getAttribute('data-telp');
            const alamat = button.getAttribute('data-alamat');
            const fotoUrl = button.getAttribute('data-foto');

            const form = document.getElementById('formEditAnggota');
            form.action = `/admin/anggota/update/${id}`;

            document.getElementById('edit_username').value = username;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_nama_lengkap').value = nama;
            document.getElementById('edit_role').value = role;
            document.getElementById('edit_nisn').value = nisn;
            document.getElementById('edit_kelas').value = kelas;
            document.getElementById('edit_jurusan').value = jurusan;
            document.getElementById('edit_telp').value = telp;
            document.getElementById('edit_alamat').value = alamat;

            // Sembunyikan field siswa jika yang diedit adalah akun Admin
            const fields = document.querySelectorAll(".edit-fields-siswa");
            fields.forEach(el => {
                if(role === 'admin') {
                    el.classList.add('d-none');
                } else {
                    el.classList.remove('d-none');
                }
            });

            const editProfilPreview = document.getElementById('editProfilPreview');
            const editProfilPreviewContainer = document.getElementById('editProfilPreviewContainer');
            if (fotoUrl) {
                editProfilPreview.src = fotoUrl;
                editProfilPreviewContainer.classList.remove('d-none');
            } else {
                editProfilPreviewContainer.classList.add('d-none');
            }
        });
    }

    // Live Preview Unggah Profil Modal Edit
    const editProfilInput = document.getElementById('editProfilInput');
    if (editProfilInput) {
        editProfilInput.addEventListener('change', function() {
            const file = this.files[0];
            if(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('editProfilPreview').src = e.target.result;
                    document.getElementById('editProfilPreviewContainer').classList.remove('d-none');
                }
                reader.readAsDataURL(file);
            }
        });
    }
</script>
@endsection