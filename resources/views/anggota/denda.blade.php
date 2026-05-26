@extends('layouts.app')

@section('title', 'Tagihan Denda Saya')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0" style="color: #0b1b35;">Tagihan Denda Kamu</h4>
            <p class="text-muted small m-0">Silakan pantau dan lunasi denda keterlambatan atau pengembalian tanpa struk kamu ke petugas perpustakaan.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 13.5px;">
                    <thead style="background-color: #0b1b35; color: white;">
                        <tr>
                            <th class="py-3 px-4" width="5%">No</th>
                            <th class="py-3">ID Pinjam</th>
                            <th class="py-3">Judul Buku</th>
                            <th class="py-3">Batas Kembali</th>
                            <th class="py-3">Status Buku</th>
                            <th class="py-3 text-end px-4" width="20%">Jumlah Denda</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daftarDenda as $index => $denda)
                        <tr>
                            <td class="px-4 fw-medium text-muted">{{ $index + 1 }}</td>
                            <td class="fw-bold text-secondary">#{{ $denda->id }}</td>
                            <td class="fw-bold" style="color: #0b1b35;">{{ $denda->buku->judul }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($denda->tgl_kembali_seharusnya)->format('d F Y') }}
                            </td>
                            <td>
                                @if($denda->status == 'dipinjam')
                                    <span class="badge bg-warning text-dark px-2 py-1.5" style="border-radius: 6px;">Masih Dipinjam</span>
                                @else
                                    <span class="badge bg-secondary px-2 py-1.5" style="border-radius: 6px;">Sudah Kembali</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-primary px-4">
                                Rp {{ number_format($denda->total_denda, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-check-circle-fill text-success display-4 opacity-25 d-block mb-3"></i>
                                <p class="m-0 fw-medium fs-6">Yeay! Kamu tidak memiliki tagihan denda aktif.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                    @if($adaDendaAktif)
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="5" class="text-end fw-bold py-3 px-4">TOTAL YANG HARUS DIBAYAR :</td>
                            <td class="text-end fw-bold text-danger py-3 px-4" style="font-size: 16px;">
                                Rp {{ number_format($grandTotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    @if($adaDendaAktif)
    <div class="alert alert-danger border-0 mt-4 p-3 d-flex align-items-center gap-3 shadow-sm" style="border-radius: 10px; background-color: #fef2f2; color: #991b1b;">
        <i class="bi bi-exclamation-triangle-fill fs-4 flex-shrink-0" style="color: #dc2626;"></i>
        <div>
            <h6 class="fw-bold m-0 mb-1" style="font-size: 14px;">Penting untuk Diperhatikan!</h6>
            <p class="m-0 small text-secondary" style="line-height: 1.5; color: #7f1d1d !important;">
                Silakan datangi meja meja pelayanan admin perpustakaan di SMKN 40 untuk melakukan pembayaran secara tunai (*cash*). Pastikan akun kamu kembali bersih dari tunggakan agar dapat melakukan peminjaman buku berikutnya.
            </p>
        </div>
    </div>
    @endif
</div>
@endsection