@extends('layouts.app')

@section('title', 'Laporan Peminjaman')

@section('content')
<style>
    /* Mode Cetak Bersih Total */
    @media print {
        /* 1. Sembunyikan SEMUA elemen navigasi, sidebar, navbar, tombol, form, dan kaki halaman */
        #sidebar-wrapper, .sidebar, #sidebar, .navbar, #navbar, .no-print, .btn, form, 
        .card-header, [class*="btn-scroll-top"], .btn-fab, [id*="print-button"], footer, .footer { 
            display: none !important; 
        }
        
        /* 2. Reset paksa background pembungkus utama agar tidak ada warna abu-abu yang ikut tercetak */
        html, body, #wrapper, #content-wrapper, .content-wrapper, .main-content, .container-fluid, .card, .card-body {
            background: #fff !important;
            background-color: #fff !important;
            color: #000 !important;
            padding: 0 !important;
            margin: 0 !important;
            border: none !important;
            box-shadow: none !important;
            width: 100% !important;
        }

        /* 3. Atur Layout Kop Judul Laporan */
        .print-title {
            display: flex !important;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 25px;
            width: 100% !important;
        }
        
        /* 4. Pastikan Tabel Memenuhi Lebar Kertas */
        table {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        table th, table td {
            border: 1px solid #000 !important;
            padding: 8px !important;
            background-color: transparent !important;
            color: #000 !important;
        }
    }
    
    /* Sembunyikan kop judul saat aplikasi diakses normal lewat browser biasa */
    .print-title { display: none; }
</style>

<div class="container-fluid p-0">
    <div class="print-title">
        <img src="{{ asset('logo-smk.png') }}" alt="Logo SMKN 40" style="width: 85px; height: auto; margin-bottom: 12px;">
        
        <h3 class="fw-bold m-0" style="font-family: 'Times New Roman', Times, serif; letter-spacing: 0.5px;">REKAP LAPORAN PERPUSTAKAAN SMKN 40 JAKARTA</h3>
        <p class="text-muted mb-2" style="font-size: 14px;">Periode: {{ \Carbon\Carbon::parse($tgl_mulai)->translatedFormat('d F Y') }} s.d {{ \Carbon\Carbon::parse($tgl_selesai)->translatedFormat('d F Y') }}</p>
        <hr style="border: 2px solid black; width: 100%; opacity: 1; margin-top: 5px;">
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h4 class="fw-bold m-0" style="color: #1e293b;">Laporan Sirkulasi Peminjaman</h4>
        </div>
        <button type="button" onclick="window.print()" class="btn btn-dark shadow-sm">
            <i class="bi bi-printer"></i> Cetak Laporan
        </button>
    </div>

    <div class="card shadow-sm border-0 mb-4 no-print" style="border-radius: 12px;">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.laporan.peminjaman') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary small">TANGGAL MULAI</label>
                    <input type="date" name="tgl_mulai" class="form-control" value="{{ $tgl_mulai }}" style="border-radius: 8px;">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary small">TANGGAL SELESAI</label>
                    <input type="date" name="tgl_selesai" class="form-control" value="{{ $tgl_selesai }}" style="border-radius: 8px;">
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100">Filter Laporan</button>
                    <a href="{{ route('admin.laporan.peminjaman') }}" class="btn btn-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                    <thead class="table-light" style="background-color: #f8fafc;">
                        <tr>
                            <th class="py-3 px-4 text-center" style="width: 50px; color: #64748b;">NO</th>
                            <th class="py-3 text-center" style="color: #64748b;">ANGGOTA</th>
                            <th class="py-3 text-center" style="color: #64748b;">BUKU</th>
                            <th class="py-3 text-center" style="color: #64748b;">TGL PINJAM</th>
                            <th class="py-3 text-center" style="color: #64748b;">TGL KEMBALI</th>
                            <th class="py-3 text-center" style="color: #64748b;">STATUS</th>
                            <th class="py-3 text-center" style="color: #64748b;">DENDA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporan as $index => $l)
                        <tr style="border-bottom: 1px solid #f1f5f9; vertical-align: middle;">
                            <td class="text-center px-4 text-muted font-monospace">{{ $index + 1 }}</td>
                            <td class="px-4">
                                <div class="fw-semibold text-slate-800">{{ $l->user->nama_lengkap ?? 'Anggota Dihapus' }}</div>
                                <small class="text-muted">NISN: {{ $l->user->nisn ?? '-' }}</small>
                            </td>
                            <td class="px-4 text-truncate" style="max-width: 220px;">
                                <div class="fw-medium text-slate-700" title="{{ $l->buku->judul ?? 'Buku Dihapus' }}">{{ $l->buku->judul ?? 'Buku Dihapus' }}</div>
                                <small class="text-muted">Penulis: {{ $l->buku->penulis ?? '-' }}</small>
                            </td>
                            <td class="px-4 text-muted">
                                {{ \Carbon\Carbon::parse($l->tgl_peminjaman)->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-4 text-muted">
                                {{ \Carbon\Carbon::parse($l->tgl_kembali_seharusnya)->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-4">
                                {{-- LOGIKA BADGE STATUS INTERAKTIF KHUSUS LAPORAN --}}
                                @if($l->status === 'menunggu')
                                    <span class="badge bg-warning px-2 py-1.5" style="border-radius: 6px;">Menunggu Konfirmasi</span>
                                @elseif($l->status === 'ditolak')
                                    <span class="badge bg-danger px-2 py-1.5" style="border-radius: 6px;">Ditolak</span>
                                @elseif($l->status === 'dipinjam')
                                    <span class="badge bg-primary px-2 py-1.5" style="border-radius: 6px;">Sedang Dipinjam</span>
                                @elseif($l->status === 'kembali')
                                    {{-- Jika status kembali tapi dia punya denda historis dan total_denda di DB sudah 0, berarti Lunas --}}
                                    @if(isset($l->denda_historis) && $l->denda_historis > 0 && $l->total_denda == 0)
                                        <span class="badge bg-success px-2 py-1.5" style="border-radius: 6px;"><i class="bi bi-check-circle-fill me-1"></i> Selesai (Denda Lunas)</span>
                                    @elseif($l->total_denda > 0)
                                        <span class="badge bg-danger px-2 py-1.5" style="border-radius: 6px;"><i class="bi bi-exclamation-triangle-fill me-1"></i> Belum Bayar Denda</span>
                                    @else
                                        <span class="badge bg-success px-2 py-1.5" style="border-radius: 6px;">Selesai</span>
                                    @endif
                                @endif
                            </td>
                            <td class="text-end px-4 fw-bold">
                                {{-- LOGIKA MENAMPILKAN NOMINAL DENDA DI LAPORAN --}}
                                @if(isset($l->denda_historis) && $l->denda_historis > 0)
                                    @if($l->total_denda == 0)
                                        {{-- Tampilan kalau sudah dilunasi admin --}}
                                        <span class="text-success" style="font-size: 13px; display: block; fw-normal;">Lunas</span>
                                        <span class="text-muted text-decoration-line-through" style="font-size: 11px; font-weight: normal;">
                                            Rp {{ number_format($l->denda_historis, 0, ',', '.') }}
                                        </span>
                                    @else
                                        {{-- Tampilan kalau denda masih menunggak aktif --}}
                                        <span class="text-danger">
                                            Rp {{ number_format($l->total_denda, 0, ',', '.') }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-folder-x-fill display-6 block mb-2 text-secondary"></i>
                                <div>Tidak ada data transaksi peminjaman pada periode tanggal ini.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light fw-bold" style="border-top: 2px solid #e2e8f0;">
                        <tr>
                            <td colspan="6" class="text-end py-3 px-4" style="color: #1e293b;">Total Denda Terkumpul:</td>
                            <td class="text-end py-3 px-4 text-danger fs-5">Rp {{ number_format($total_denda_semua, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection