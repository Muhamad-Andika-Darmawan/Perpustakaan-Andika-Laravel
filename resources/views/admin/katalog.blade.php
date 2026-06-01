@extends('layouts.app')

@section('title', 'Katalog Buku Admin')

@section('content')

<style>
.btn-action{width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:16px;border:none;}
.btn-delete{background:#e63946;color:white;}
.btn-edit{background:#f59e0b;color:white;}
.cover-preview-table{width:150px;border-radius:10px;display:block;margin:auto;}
</style>
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0" style="color: #1e293b;">Katalog Buku</h4>
        </div>
        <button type="button" class="btn btn-warning px-3" data-bs-toggle="modal" data-bs-target="#modalKelolaKategori">
            <i class="bi bi-tags-fill me-1"></i> Kelola Kategori
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3 text-dark">Tambah Buku Baru</h5>
            <hr class="opacity-10 mb-4">
            
            <form action="{{ route('admin.katalog.storeBuku') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-secondary">Judul Buku</label>
                        <input type="text" name="judul" class="form-control" placeholder="Masukkan judul lengkap" required style="border-radius: 8px;">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-secondary">Kategori Buku</label>
                        <select name="kategori_id" class="form-select" required style="border-radius: 8px;">
                            <option value="">Pilih Kategori</option>
                            @foreach($kategoris as $kat)
                                <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-secondary">Penulis</label>
                        <input type="text" name="penulis" class="form-control" placeholder="Nama penulis" required style="border-radius: 8px;">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-secondary">Penerbit</label>
                        <input type="text" name="penerbit" class="form-control" placeholder="Nama penerbit" required style="border-radius: 8px;">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-secondary">Tahun Terbit</label>
                        <input type="number" name="tahun_terbit" class="form-control" placeholder="Tahun Terbit" required style="border-radius: 8px;">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-secondary">Jumlah Stok</label>
                        <input type="number" name="stok" class="form-control" placeholder="Jumlah Stok" required style="border-radius: 8px;">
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label small fw-bold text-secondary">Deskripsi / Sinopsis Buku</label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Tulis ringkasan sinopsis pendek buku..." style="border-radius: 8px;"></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label small fw-bold text-secondary">Upload Cover Buku</label>
                        <input type="file" name="cover" id="coverInput" class="form-control" accept="image/*" style="border-radius: 8px;">
                    </div>
                    
                    <div class="col-12 d-none mb-3" id="previewContainer">
                        <div class="p-2 border rounded-3 bg-light d-inline-block">
                            <img id="coverPreview" src="" alt="Preview" style="max-height: 120px; border-radius: 6px; object-fit: cover;">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <button type="button" id="btnHapusCover" class="btn btn-secondary">Hapus Cover</button>
                        <button type="submit" class="btn btn-warning">Simpan Buku</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form action="{{ route('admin.katalog') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari berdasarkan judul, penulis, atau penerbit..." value="{{ $search }}" style="border-radius: 0 8px 8px 0;">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="kategori" class="form-select" style="border-radius: 8px;">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->id }}" {{ $kategori_filter == $kat->id ? 'selected' : '' }}>{{ $kat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Cari Buku</button>
                    <a href="{{ route('admin.katalog') }}" class="btn btn-secondary w-100">Reset</a>
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
                        <th class="text-center" style="width: 60px;">Cover</th>
                        <th class="text-center">Judul Buku</th>
                        <th class="text-center">Deskripsi / Sinopsis</th>
                        <th class="text-center">Kategori</th>
                        <th class="text-center">Penulis</th>
                        <th class="text-center">Penerbit & Tahun Terbit</th>
                        <th class="text-center" style="width: 80px;">Stok</th>
                        <th class="text-center" style="width: 110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bukus as $index => $item)
                    <tr>
                        <td class="text-center text-muted">{{ $bukus->firstItem() + $index }}</td>
                        <td class="text-center">
                            @if($item->cover)
                                <img src="{{ asset('storage/covers/'.$item->cover) }}" alt="Cover" class="cover-preview-table">
                            @else
                                <div class="bg-light text-muted d-flex align-items-center justify-content-center rounded mx-auto" style="width: 38px; height: 50px;">
                                    <i class="bi bi-book" style="font-size: 16px;"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="text-dark" style="font-size: 16px;">{{ $item->judul }}</span>
                        </td>
                        <td>
                            <span class="text-dark" font-size: 12px;" title="{{ $item->deskripsi }}">
                                {{ $item->deskripsi ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1" style="font-size: 11px;">
                                {{ $item->kategori->nama_kategori ?? 'Umum' }}
                            </span>
                        </td>
                        <td class="text-dark">{{ $item->penulis }}</td>
                        <td>
                            <span class="text-dark">{{ $item->penerbit }}</span>
                            <small class="text-muted d-block" style="font-size: 11px;">Tahun: {{ $item->tahun_terbit }}</small>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $item->stok > 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} px-2 py-1 fw-bold">
                                {{ $item->stok }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <button type="button" class="btn-action btn-edit" data-bs-toggle="modal" data-bs-target="#modalEditBuku" 
                                    data-id="{{ $item->id }}" 
                                    data-judul="{{ $item->judul }}" 
                                    data-kategori="{{ $item->kategori_id }}"
                                    data-penulis="{{ $item->penulis }}" 
                                    data-penerbit="{{ $item->penerbit }}" 
                                    data-tahun="{{ $item->tahun_terbit }}" 
                                    data-stok="{{ $item->stok }}" 
                                    data-deskripsi="{{ $item->deskripsi }}"
                                    data-cover="{{ $item->cover ? asset('storage/covers/'.$item->cover) : '' }}">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <form action="{{ route('admin.buku.delete', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete" style="background:#e63946; color:white; width:36px; height:36px; display:flex; align-items:center; justify-content:center; border-radius:8px; border:none;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox opacity-20 d-block mb-2" style="font-size: 2.5rem;"></i>
                            Tidak ada koleksi buku yang terdaftar atau cocok dengan pencarian.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4 p-3">
            <small class="text-muted">Menampilkan {{ $bukus->firstItem() ?? 0 }} sampai {{ $bukus->lastItem() ?? 0 }} dari {{ $bukus->total() }} data</small>
            <div>
                {{ $bukus->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditBuku" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header border-bottom pt-3 pb-3 px-4 d-flex justify-content-between align-items-center bg-light" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <h5 class="modal-title fw-bold m-0 text-dark">Edit Buku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 12px;"></button>
            </div>
            
            <form id="formEditBuku" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body px-4 py-3">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">Judul Buku</label>
                            <input type="text" name="judul" id="edit_judul" class="form-control" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">Kategori Buku</label>
                            <select name="kategori_id" id="edit_kategori_id" class="form-select" required style="border-radius: 8px;">
                                <option value="">Pilih Kategori</option>
                                @foreach($kategoris as $kat)
                                    <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">Penulis</label>
                            <input type="text" name="penulis" id="edit_penulis" class="form-control" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">Penerbit</label>
                            <input type="text" name="penerbit" id="edit_penerbit" class="form-control" required style="border-radius: 8px;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">Tahun Terbit</label>
                            <input type="number" name="tahun_terbit" id="edit_tahun_terbit" class="form-control" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">Jumlah Stok</label>
                            <input type="number" name="stok" id="edit_stok" class="form-control" required style="border-radius: 8px;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label small fw-bold text-secondary">Deskripsi / Sinopsis Buku</label>
                            <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="3" style="border-radius: 8px;"></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-2">
                            <label class="form-label small fw-bold text-secondary">Ubah Cover Buku</label>
                            <input type="file" name="cover" id="editCoverInput" class="form-control" accept="image/*" style="border-radius: 8px;">
                        </div>
                        
                        <div class="col-12 mb-2" id="editPreviewContainer">
                            <div class="p-2 border rounded-3 bg-light d-inline-block">
                                <img id="editCoverPreview" src="" alt="Cover Lama" style="max-height: 120px; border-radius: 6px; object-fit: cover;">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer border-top-0 pb-3 pt-0 px-4 d-flex gap-2">
                    <button type="submit" class="btn btn-warning">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalKelolaKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header border-bottom pt-3 pb-3 px-4 d-flex justify-content-between align-items-center bg-light" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <h5 class="modal-title fw-bold m-0 text-dark">Kelola Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <form action="{{ route('admin.katalog.storeKategori') }}" method="POST" class="mb-4">
                    @csrf
                    <label class="fw-bold mb-2">Tambah Kategori Baru</label>
                    <div class="d-flex gap-2 mb-4">
                        <input type="text" name="nama_kategori" class="form-control" placeholder="Nama kategori baru..." required>
                        <button type="submit" class="btn btn-warning px-4">Tambah</button>
                    </div>
                </form>

                <label class="fw-bold mb-2">Daftar Kategori Aktif</label>
                <div class="border rounded" style="max-height: 200px; overflow-y: auto; border-radius: 6px;">
                    @foreach($kategoris as $k)
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom last-border-none" style="font-size: 13.5px;">
                        <span class="text-dark">{{ $k->nama_kategori }}</span>
                        
                        <form action="{{ route('admin.katalog.deleteKategori', $k->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kategori ini? Semua buku dengan kategori ini akan kehilangan relasinya.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action btn-delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .last-border-none:last-child {
        border-bottom: none !important;
    }
</style>

<script>
    const coverInput = document.getElementById("coverInput");
    const preview = document.getElementById("coverPreview");
    const previewContainer = document.getElementById("previewContainer");
    const btnHapusCover = document.getElementById("btnHapusCover");
    
    if(coverInput) {
        coverInput.addEventListener("change", function() {
            const file = this.files[0];
            if(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.classList.remove("d-none");
                    btnHapusCover.classList.remove("d-none"); 
                }
                reader.readAsDataURL(file);
            }
        });
    }

    if(btnHapusCover) {
        btnHapusCover.addEventListener("click", function() {
            coverInput.value = "";                     
            preview.src = "";                          
            previewContainer.classList.add("d-none");  
            this.classList.add("d-none");             
        });
    }

    /* ==================== JAVASCRIPT LOGIC UNTUK INJEKSI DATA MODAL EDIT BUKU ==================== */
    const modalEditBuku = document.getElementById('modalEditBuku');
    if (modalEditBuku) {
        modalEditBuku.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            
            // Ambil seluruh data atribut dari tombol edit tabel
            const id = button.getAttribute('data-id');
            const judul = button.getAttribute('data-judul');
            const kategoriId = button.getAttribute('data-kategori');
            const penulis = button.getAttribute('data-penulis');
            const penerbit = button.getAttribute('data-penerbit');
            const tahun = button.getAttribute('data-tahun');
            const stok = button.getAttribute('data-stok');
            const deskripsi = button.getAttribute('data-deskripsi');
            const coverUrl = button.getAttribute('data-cover');

            // Inject action form route dengan ID buku target dinamis
            const form = document.getElementById('formEditBuku');
            form.action = `/admin/katalog/update/${id}`; // Sesuaikan dengan route name web.php milikmu

            // Isi nilai ke field input modal
            document.getElementById('edit_judul').value = judul;
            document.getElementById('edit_kategori_id').value = kategoriId;
            document.getElementById('edit_penulis').value = penulis;
            document.getElementById('edit_penerbit').value = penerbit;
            document.getElementById('edit_tahun_terbit').value = tahun;
            document.getElementById('edit_stok').value = stok;
            document.getElementById('edit_deskripsi').value = deskripsi;

            // Handle Preview Cover di Modal Edit
            const editCoverPreview = document.getElementById('editCoverPreview');
            const editPreviewContainer = document.getElementById('editPreviewContainer');
            if (coverUrl) {
                editCoverPreview.src = coverUrl;
                editPreviewContainer.classList.remove('d-none');
            } else {
                editPreviewContainer.classList.add('d-none');
            }
        });
    }

    // Live Preview saat user memilih file cover baru di Modal Edit
    const editCoverInput = document.getElementById('editCoverInput');
    if (editCoverInput) {
        editCoverInput.addEventListener('change', function() {
            const file = this.files[0];
            if(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('editCoverPreview').src = e.target.result;
                    document.getElementById('editPreviewContainer').classList.remove('d-none');
                }
                reader.readAsDataURL(file);
            }
        });
    }
</script>
@endsection