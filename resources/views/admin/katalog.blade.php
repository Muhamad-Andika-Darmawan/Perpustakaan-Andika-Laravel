@extends('layouts.app')

@section('title', 'Katalog Buku')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0">Katalog Buku</h4>
            <small class="text-muted">Kelola koleksi dan kategori buku perpustakaan</small>
        </div>
        <button class="btn btn-warning text-white fw-bold px-4 py-2" style="border-radius: 12px;" data-bs-toggle="modal" data-bs-target="#modalTambahBuku">
            <i class="bi bi-plus-lg me-2"></i> Tambah Buku Baru
        </button>
    </div>

    <div class="custom-card mb-4">
        <form action="{{ route('katalog') }}" method="GET" class="row g-3">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari berdasarkan judul, penulis, penerbit..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="kategori" class="form-select">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $kategori_filter == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-semibold" style="border-radius: 10px;">Filter</button>
                <a href="{{ route('katalog') }}" class="btn btn-outline-secondary w-100 fw-semibold" style="border-radius: 10px;">Reset</a>
            </div>
        </form>
    </div>

    <div class="custom-card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 60px;">No</th>
                        <th>Cover</th>
                        <th>Judul Buku</th>
                        <th>Penulis</th>
                        <th>Penerbit</th>
                        <th>Tahun</th>
                        <th>Stok</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($buku as $index => $item)
                    <tr>
                        <td class="text-center text-muted">{{ $buku->firstItem() + $index }}</td>
                        <td>
                            <img src="{{ asset('storage/covers/'.$item->cover) }}" alt="Cover" class="rounded" style="width: 45px; height: 60px; object-fit: cover;">
                        </td>
                        <td><strong class="text-dark">{{ $item->judul }}</strong></td>
                        <td>{{ $item->penulis }}</td>
                        <td>{{ $item->penerbit }}</td>
                        <td>{{ $item->tahun_terbit }}</td>
                        <td><span class="badge bg-light-green px-2.5 py-1.5">{{ $item->stok }} Eks</span></td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-sm btn-outline-primary" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#modalEditBuku{{ $item->id }}"><i class="bi bi-pencil-square"></i></button>
                                <button class="btn btn-sm btn-outline-danger" style="border-radius: 8px;"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-book-half opacity-25 d-block mb-2" style="font-size: 2.5rem;"></i>
                            Belum ada data koleksi buku yang terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted small">
                Menampilkan {{ $buku->firstItem() ?? 0 }} sampai {{ $buku->lastItem() ?? 0 }} dari {{ $buku->total() }} buku
            </div>
            <div>
                {{ $buku->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahBuku" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow border-0" style="border-radius: 20px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-journal-plus text-warning me-2"></i> Tambah Koleksi Buku</h5>
                <button type="button" class="btn-close" data-bs-close="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('katalog.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body px-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Judul Buku</label>
                            <input type="text" name="judul" class="form-control" placeholder="Masukkan judul lengkap" required style="border-radius: 10px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Kategori Buku</label>
                            <select name="kategori_id" class="form-select" required style="border-radius: 10px;">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Penulis / Pengarang</label>
                            <input type="text" name="penulis" class="form-control" placeholder="Nama penulis" required style="border-radius: 10px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Penerbit</label>
                            <input type="text" name="penerbit" class="form-control" placeholder="Nama perusahaan penerbit" required style="border-radius: 10px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tahun Terbit</label>
                            <input type="number" name="tahun_terbit" class="form-control" placeholder="Contoh: 2024" required style="border-radius: 10px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Jumlah Stok</label>
                            <input type="number" name="stok" class="form-control" placeholder="Jumlah eks" required style="border-radius: 10px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Upload Cover Buku</label>
                            <input type="file" name="cover" class="form-control" accept="image/*" style="border-radius: 10px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                    <button type="submit" class="btn btn-warning text-white fw-bold px-4" style="border-radius: 10px;">Simpan Buku</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection