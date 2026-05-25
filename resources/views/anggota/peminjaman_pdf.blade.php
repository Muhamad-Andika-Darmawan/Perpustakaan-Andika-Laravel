<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .content { margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 8px 0; }
        .footer { margin-top: 50px; text-align: center; font-style: italic; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h2>PERPUSTAKAAN DIGITAL SMKN 40</h2>
        <p>Bukti Peminjaman Buku Resmi</p>
    </div>
    <div class="content">
        <table>
            <tr><td width="30%">ID Transaksi</td><td>: #{{ $peminjaman->id }}</td></tr>
            <tr><td>Nama Anggota</td><td>: {{ $peminjaman->user->nama_lengkap }}</td></tr>
            <tr><td>NISN</td><td>: {{ $peminjaman->user->nisn ?? '-' }}</td></tr>
            <tr><td>Judul Buku</td><td>: <strong>{{ $peminjaman->buku->judul }}</strong></td></tr>
            <tr><td>Tanggal Pinjam</td><td>: {{ \Carbon\Carbon::parse($peminjaman->tgl_pengajuan)->format('d F Y') }}</td></tr>
            <tr><td>Batas Kembali</td><td>: {{ \Carbon\Carbon::parse($peminjaman->tgl_kembali_seharusnya)->format('d F Y') }}</td></tr>
        </table>
    </div>
    <div class="footer">
        <p>Harap kembalikan buku tepat waktu untuk menghindari denda.<br>Terima kasih telah membaca!</p>
    </div>
</body>
</html>