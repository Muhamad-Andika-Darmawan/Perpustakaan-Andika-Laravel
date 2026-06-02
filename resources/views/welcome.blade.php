<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Digital SMKN 40 Jakarta</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        html {
            scroll-behavior: smooth;
        }
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-[#0b1b35] selection:text-white">

    <nav class="fixed top-0 left-0 right-0 z-50 bg-[#0b1b35]/95 backdrop-blur-md border-b border-amber-400/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-1">
                    <img src="{{ asset('logo-smk.png') }}" alt="Logo" class="me-3 h-9 w-auto object-contain">
                    <span class="font-bold text-lg tracking-tight text-white">
                        Perpustakaan 40
                    </span>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('login') }}" class="text-sm font-medium text-slate-200 hover:text-[#ffc107] transition-colors">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 h-9 text-sm font-medium text-[#0b1b35] bg-[#ffc107] hover:bg-[#ffca2c] active:scale-95 rounded-xl shadow-sm transition-all fw-bold">
                        Daftar Anggota
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <header class="relative pt-32 pb-20 md:pt-40 md:pb-28 overflow-hidden bg-[#0b1b35] text-white">
        <div class="absolute top-0 right-0 -translate-y-12 translate-x-12 w-96 h-96 bg-[#ffc107]/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 translate-y-12 -translate-x-12 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative text-center">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold text-[#ffc107] bg-[#ffc107]/10 border border-[#ffc107]/30 mb-6">
                <span class="w-1.5 h-1.5 bg-[#ffc107] rounded-full animate-pulse"></span>
                Digital Literacy Platform SMKN 40
            </span>
            
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight max-w-3xl mx-auto leading-[1.15]">
                Eksplorasi Pengetahuan Tanpa Batas di <span class="text-[#ffc107]">Perpustakaan SMKN 40</span>
            </h1>
            
            <p class="mt-6 text-base sm:text-lg text-slate-300 max-w-2xl mx-auto leading-relaxed">
                Akses katalog buku online, ajukan peminjaman mandiri secara digital, pantau riwayat pinjaman, dan kumpulkan poin membaca dengan mudah dari perangkatmu.
            </p>

            <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 h-12 text-base font-semibold text-[#0b1b35] bg-[#ffc107] hover:bg-[#ffca2c] rounded-2xl shadow-lg shadow-yellow-500/10 hover:shadow-yellow-500/20 transition-all transform hover:-translate-y-0.5">
                    Mulai Membaca Sekarang <i class="fas fa-arrow-right ml-2 text-sm"></i>
                </a>
                <a href="#fitur" class="inline-flex items-center justify-center px-6 h-12 text-base font-medium text-white hover:text-[#ffc107] bg-white/5 hover:bg-white/10 border border-white/20 rounded-2xl transition-all">
                    Lihat Fitur Utama
                </a>
            </div>
        </div>
    </header>

    <section id="fitur" class="py-20 bg-white border-y border-slate-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-2xl sm:text-3xl font-bold text-[#0b1b35] tracking-tight">
                    Mengapa Membaca Melalui Platform Kami?
                </h2>
                <p class="mt-3 text-slate-500">
                    Sistem dirancang untuk memberikan kemudahan manajemen literasi sekolah secara transparan, modern, dan serba otomatis.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-6 rounded-2xl border border-slate-100 hover:border-amber-100 bg-slate-50/50 hover:bg-white hover:shadow-xl hover:shadow-slate-100 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-[#0b1b35]/5 flex items-center justify-center text-[#0b1b35] group-hover:bg-[#0b1b35] group-hover:text-white transition-all shadow-sm">
                        <i class="fas fa-search text-lg"></i>
                    </div>
                    <h3 class="mt-5 text-lg font-bold text-[#0b1b35]">Pencarian Cepat & Filter</h3>
                    <p class="mt-2 text-sm text-slate-500 leading-relaxed">
                        Cari koleksi buku perpustakaan berdasarkan judul, penulis, penerbit, hingga kategori khusus lewat menu pencarian responsif.
                    </p>
                </div>

                <div class="p-6 rounded-2xl border border-slate-100 hover:border-amber-100 bg-slate-50/50 hover:bg-white hover:shadow-xl hover:shadow-slate-100 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-[#ffc107] group-hover:bg-[#ffc107] group-hover:text-[#0b1b35] transition-all shadow-sm">
                        <i class="fas fa-qrcode text-lg"></i>
                    </div>
                    <h3 class="mt-5 text-lg font-bold text-[#0b1b35]">Pengajuan Pinjam Mandiri</h3>
                    <p class="mt-2 text-sm text-slate-500 leading-relaxed">
                        Tidak perlu mengantre lama, cukup pilih buku pilihanmu di katalog digital dan ajukan peminjaman langsung dari akun siswa.
                    </p>
                </div>

                <div class="p-6 rounded-2xl border border-slate-100 hover:border-amber-100 bg-slate-50/50 hover:bg-white hover:shadow-xl hover:shadow-slate-100 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-[#0b1b35]/5 flex items-center justify-center text-[#0b1b35] group-hover:bg-[#0b1b35] group-hover:text-white transition-all shadow-sm">
                        <i class="fas fa-receipt text-lg"></i>
                    </div>
                    <h3 class="mt-5 text-lg font-bold text-[#0b1b35]">Unduh Struk & Pantau Denda</h3>
                    <p class="mt-2 text-sm text-slate-500 leading-relaxed">
                        Unduh bukti struk peminjaman berformat PDF otomatis, pantau tanggal pengembalian buku, serta transparansi akumulasi denda.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-[#0b1b35] text-slate-400 py-12 border-t border-amber-400/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6 border-b border-slate-800 pb-8">
            <div class="flex items-center gap-1">
                <img src="{{ asset('logo-smk.png') }}" alt="Logo" class="me-3 h-8 w-auto object-contain">
                <span class="font-bold text-white tracking-tight">Perpustakaan <span class="text-[#ffc107]">SMKN 40</span></span>
            </div>
            <p class="text-sm text-center md:text-right text-slate-300">
                &copy; {{ date('Y') }} Muhamad Andika Darmawan. All rights reserved. Hubungi admin perpustakaan untuk aktivasi akun fisik.
            </p>
        </div>
    </footer>

</body>
</html>