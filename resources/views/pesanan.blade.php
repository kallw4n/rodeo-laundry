<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - Rodeo Laundry Malang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
</head>
<body class="bg-[#FAF8FF] text-[#1A1743] font-sans antialiased"
      x-data="{ }">

    <!-- ================= HEADER ================= -->
    <header class="fixed top-0 w-full h-20 bg-[#FAF8FF]/85 backdrop-blur-md shadow-sm z-50">
        <div class="max-w-[1440px] mx-auto h-full px-6 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <div class="w-10 h-10 bg-[#2F2D59] flex items-center justify-center shadow-lg">
                    <div class="w-4 h-5 bg-[#BFF138]"></div>
                </div>
                <div>
                    <div class="flex items-center text-lg font-semibold leading-none">
                        Rodeo<span class="text-[#A7D717]">.</span>
                    </div>
                    <div class="text-[10px] font-bold uppercase tracking-wider text-[#47464E] mt-1">Laundry Malang</div>
                </div>
            </a>

            <nav class="hidden md:flex items-center gap-1 bg-[#F2F3FF] p-1.5 rounded-full">
                <a href="{{ url('/') }}" class="px-4 py-1.5 text-sm font-semibold text-[#47464E] hover:text-[#1A1743] rounded-full">Beranda</a>
                <a href="{{ route('pemesanan') }}" class="px-4 py-1.5 text-sm font-semibold text-[#47464E] hover:text-[#1A1743] rounded-full">Layanan</a>
                <a href="{{ route('pesanan') }}" class="px-4 py-1.5 bg-[#EAEDFF] rounded-full text-sm font-bold">Pesanan</a>
            </nav>

            <div class="flex items-center gap-3">
                <a href="#" class="flex items-center gap-2 px-4 py-2 bg-[#EAEDFF] rounded-full text-sm font-semibold text-[#131B2E]">
                    <div class="w-4 h-4 bg-[#4E6700]"></div> WhatsApp
                </a>
                <div class="w-8 h-8 bg-[#1A1743] rounded-full flex items-center justify-center">
                    <div class="w-3 h-3 bg-white"></div>
                </div>
            </div>
        </div>
    </header>

    <main class="pt-28 max-w-[1440px] mx-auto px-6 pb-16 space-y-6">

        <!-- ================= HERO CARD: STATUS PESANAN ================= -->
        <div class="relative bg-gradient-to-br from-[#1A1743] to-[#2F2D59] rounded-[32px] p-6 md:p-10 overflow-hidden shadow-2xl text-white">
            <!-- Dekorasi blur -->
            <div class="absolute w-80 h-80 bg-[#BFF138]/10 blur-3xl rounded-full -top-20 -right-20"></div>

            <div class="relative z-10">
                <!-- Header: Order ID + Status -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/10 rounded-full border border-white/10 w-fit">
                        <span class="text-[#BFF138] font-bold text-xs">#</span>
                        <span class="text-xs font-semibold text-[#9895C8]">ORDER ID:</span>
                        <span class="text-xs font-bold">#RDL-2024-001</span>
                        <span class="w-1 h-1 bg-[#9895C8] rounded-full"></span>
                        <span class="text-xs text-[#9895C8]">DIBUAT HARI INI, 09:45 WIB</span>
                    </div>
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-[#BFF138] text-[#131B2E] rounded-full w-fit">
                        <span class="w-2 h-2 bg-[#131B2E] rounded-full animate-pulse"></span>
                        <span class="text-xs font-extrabold uppercase tracking-wide">Sedang Dicuci</span>
                    </div>
                </div>

                <!-- Target Penyelesaian -->
                <div class="mb-6">
                    <p class="text-xs font-bold text-[#BFF138] uppercase tracking-wider mb-2">Target Penyelesaian Garment</p>
                    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#BFF138]/20 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#BFF138]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h2 class="text-3xl md:text-4xl font-extrabold">Hari ini, 17:00 WIB</h2>
                        </div>
                        <div class="px-4 py-2 bg-white/10 rounded-full border border-white/10 w-fit">
                            <span class="text-xs text-[#9895C8]">Sisa Waktu: </span>
                            <span class="text-xs font-bold text-[#BFF138]">~ 5 Jam 30 Menit</span>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#BFF138]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-sm font-semibold text-white">Tahap 3 dari 6 • Proses Pencucian Utama &amp; Ozon</span>
                        </div>
                        <span class="text-sm font-bold text-[#BFF138]">60% Selesai</span>
                    </div>
                    <div class="w-full h-2 bg-white/10 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-[#BFF138] to-[#A7D717] rounded-full transition-all duration-1000" style="width: 60%"></div>
                    </div>
                </div>

                <!-- Info Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="flex items-center gap-3 px-4 py-3 bg-white/10 rounded-2xl border border-white/10">
                        <div class="w-8 h-8 bg-[#BFF138]/20 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-[#BFF138]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold text-[#9895C8] uppercase tracking-wider">Hub Operasional</p>
                            <p class="text-sm font-bold text-white truncate">Soekarno-Hatta Malang</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 px-4 py-3 bg-white/10 rounded-2xl border border-white/10">
                        <div class="w-8 h-8 bg-[#BFF138]/20 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-[#BFF138]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold text-[#9895C8] uppercase tracking-wider">Jenis Layanan</p>
                            <p class="text-sm font-bold text-white truncate">Regular Express 8 Jam</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 px-4 py-3 bg-white/10 rounded-2xl border border-white/10">
                        <div class="w-8 h-8 bg-[#BFF138]/20 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-[#BFF138]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold text-[#9895C8] uppercase tracking-wider">Moda Transportasi</p>
                            <p class="text-sm font-bold text-white truncate">Jemput &amp; Antar Kurir</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= CARD: PROGRES CUCIAN (TIMELINE) ================= -->
        <div class="bg-white rounded-[32px] p-6 md:p-8 shadow-sm border border-[#E2E7FF]/60">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8 pb-6 border-b border-[#E2E7FF]/60">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-[#EAEDFF] rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold">Progres Cucian</h2>
                        <p class="text-sm text-[#47464E]">Dilengkapi sensor telemetri temperatur dan pemantauan RFID</p>
                    </div>
                </div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-[#C2F43B]/30 rounded-full w-fit">
                    <span class="w-2 h-2 bg-[#4E6700] rounded-full animate-pulse"></span>
                    <span class="text-xs font-bold text-[#4E6700]">RFID Feed: Terkoneksi</span>
                </div>
            </div>

            <!-- Timeline -->
            <div class="relative pl-8 space-y-8">

                <!-- Garis vertikal -->
                <div class="absolute left-[11px] top-3 bottom-3 w-px bg-[#E2E7FF]"></div>
                <div class="absolute left-[11px] top-3 w-px bg-[#BFF138]" style="height: 40%;"></div>

                <!-- Step 1: Dijemput Kurir (DONE) -->
                <div class="relative">
                    <div class="absolute -left-8 top-1 w-6 h-6 bg-[#BFF138] rounded-full flex items-center justify-center border-4 border-white shadow">
                        <svg class="w-3 h-3 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-2">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <h3 class="font-bold text-[#1A1743]">Dijemput Kurir</h3>
                                <span class="px-2 py-0.5 bg-[#EAEDFF] rounded-full text-[10px] font-bold text-[#1A1743]">Tuntas</span>
                            </div>
                            <p class="text-sm text-[#47464E]">Pakaian kotor telah diserahkan di Kost Griya Suhat oleh pelanggan. Armada Driver Rodeo #02.</p>
                        </div>
                        <span class="text-xs font-semibold text-[#47464E] shrink-0">10:00 WIB</span>
                    </div>
                </div>

                <!-- Step 2: Diterima Outlet Hub (DONE) -->
                <div class="relative">
                    <div class="absolute -left-8 top-1 w-6 h-6 bg-[#BFF138] rounded-full flex items-center justify-center border-4 border-white shadow">
                        <svg class="w-3 h-3 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-2">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <h3 class="font-bold text-[#1A1743]">Diterima Outlet Hub Soekarno-Hatta</h3>
                                <span class="px-2 py-0.5 bg-[#EAEDFF] rounded-full text-[10px] font-bold text-[#1A1743]">Tuntas</span>
                            </div>
                            <p class="text-sm text-[#47464E]">Penimbangan, tagging RFID barcode, dan disinfeksi awal selesai (2.0 Kg, 1 Pasang Sepatu).</p>
                        </div>
                        <span class="text-xs font-semibold text-[#47464E] shrink-0">10:30 WIB</span>
                    </div>
                </div>

                <!-- Step 3: Sedang Dicuci (ACTIVE) -->
                <div class="relative">
                    <div class="absolute -left-8 top-1 w-6 h-6 bg-[#1A1743] rounded-full flex items-center justify-center border-4 border-white shadow-lg">
                        <span class="w-2 h-2 bg-[#BFF138] rounded-full animate-pulse"></span>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-2">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <h3 class="font-bold text-[#1A1743]">Sedang Dicuci &amp; Sterilisasi Ozone</h3>
                                <span class="px-2 py-0.5 bg-[#1A1743] text-[#BFF138] rounded-full text-[10px] font-bold uppercase tracking-wide">Aktif Sekarang</span>
                            </div>
                            <p class="text-sm text-[#47464E]">Sedang diproses dalam mesin cuci komersial dengan formulasi deterjen hypo-allergenic ramah serat kain.</p>
                        </div>
                        <span class="text-xs font-semibold text-[#1A1743] shrink-0">11:00 WIB</span>
                    </div>
                </div>

                <!-- Step 4: Disetrika (PENDING) -->
                <div class="relative opacity-60">
                    <div class="absolute -left-8 top-1 w-6 h-6 bg-[#F2F3FF] rounded-full flex items-center justify-center border-4 border-white shadow">
                        <span class="w-2 h-2 bg-[#E2E7FF] rounded-full"></span>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-2">
                        <div class="flex-1">
                            <h3 class="font-bold text-[#1A1743] mb-1">Disetrika Uap Presisi &amp; Dilipat Higienis</h3>
                            <p class="text-sm text-[#47464E]">Treatment pewangi Magnolia &amp; lipatan garment rapi standar boutique.</p>
                        </div>
                        <span class="text-xs font-semibold text-[#47464E] shrink-0">Est. 14:30 WIB</span>
                    </div>
                </div>

                <!-- Step 5: Siap Diantar (PENDING) -->
                <div class="relative opacity-60">
                    <div class="absolute -left-8 top-1 w-6 h-6 bg-[#F2F3FF] rounded-full flex items-center justify-center border-4 border-white shadow">
                        <span class="w-2 h-2 bg-[#E2E7FF] rounded-full"></span>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-2">
                        <div class="flex-1">
                            <h3 class="font-bold text-[#1A1743] mb-1">Siap Diantar Kurir Rodeo</h3>
                            <p class="text-sm text-[#47464E]">Pakaian dimasukkan ke tas kedap udara higienis armada motor steril UV-C.</p>
                        </div>
                        <span class="text-xs font-semibold text-[#47464E] shrink-0">Est. 16:00 WIB</span>
                    </div>
                </div>

                <!-- Step 6: Pesanan Selesai (PENDING) -->
                <div class="relative opacity-60">
                    <div class="absolute -left-8 top-1 w-6 h-6 bg-[#F2F3FF] rounded-full flex items-center justify-center border-4 border-white shadow">
                        <span class="w-2 h-2 bg-[#E2E7FF] rounded-full"></span>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-2">
                        <div class="flex-1">
                            <h3 class="font-bold text-[#1A1743] mb-1">Pesanan Selesai / Diterima</h3>
                            <p class="text-sm text-[#47464E]">Konfirmasi penerimaan dengan PIN serah terima digital kepada kurir.</p>
                        </div>
                        <span class="text-xs font-semibold text-[#47464E] shrink-0">Est. 17:00 WIB</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= CARD: ALAMAT & KURIR ================= -->
        <div class="bg-white rounded-[32px] p-6 md:p-8 shadow-sm border border-[#E2E7FF]/60">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-[#EAEDFF] rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold">Alamat Penjemputan &amp; Pengantaran</h2>
                        <p class="text-sm text-[#47464E]">Titik serah terima paket kurir Rodeo</p>
                    </div>
                </div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-[#C2F43B]/30 rounded-full w-fit">
                    <svg class="w-3.5 h-3.5 text-[#4E6700]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    <span class="text-xs font-bold text-[#4E6700]">Terverifikasi GPS</span>
                </div>
            </div>

            <!-- Kotak Alamat -->
            <div class="p-5 rounded-2xl bg-[#FAF8FF] border border-[#E2E7FF]/60 mb-5">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-8 h-8 bg-[#BFF138] rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-[#1A1743] mb-1">Kost Griya Suhat Residence</h3>
                        <p class="text-sm text-[#47464E] mb-3">Jl. Soekarno Hatta No. 45, Kecamatan Lowokwaru, Kota Malang, Jawa Timur 65141</p>
                        <div class="flex items-start gap-2 text-xs text-[#47464E]">
                            <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span><span class="font-bold">Catatan:</span> Pagar hitam depan ruko samping minimarket, titip di resepsionis jika sedang keluar.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Courier Info -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pt-4 border-t border-[#E2E7FF]/60">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-[#EAEDFF] rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-sm text-[#47464E]">
                        Dijemput oleh <span class="font-bold text-[#1A1743]">Budi Santoso</span> (Armada Driver #02)
                    </p>
                </div>
                <a href="#" class="inline-flex items-center gap-2 text-sm font-bold text-[#1A1743] hover:text-[#4E6700] transition">
                    Buka Petunjuk Arah
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>
        </div>

    </main>

    <!-- ================= FOOTER ================= -->
    <footer class="bg-white border-t border-gray-100 mt-16">
        <div class="max-w-[1440px] mx-auto px-6 py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-9 h-9 bg-[#2F2D59] flex items-center justify-center"><div class="w-3.5 h-5 bg-[#BFF138]"></div></div>
                        <span class="text-xl font-bold">Rodeo Laundry</span>
                    </div>
                    <p class="text-[#47464E] text-sm mb-4 max-w-sm">Laundry profesional, cepat, bersih, dan wangi di Malang. Layanan antar-jemput ekspres untuk kenyamanan pakaian higienis Anda setiap hari.</p>
                    <span class="inline-flex items-center gap-2 px-3 py-1 bg-[#C2F43B] rounded-full text-xs font-bold text-[#151F00]">
                        <div class="w-2 h-2 bg-[#4E6700] rounded-full"></div> Outlet Soekarno Hatta Malang
                    </span>
                </div>
                <div>
                    <h4 class="font-bold text-sm uppercase tracking-wider mb-4">Layanan Kami</h4>
                    <ul class="space-y-2 text-sm text-[#47464E]">
                        <li>Kiloan Premium</li>
                        <li>Dry Cleaning Satuan</li>
                        <li>Cuci Sepatu & Tas</li>
                        <li>Bed Cover & Karpet</li>
                        <li>Express 3 Jam</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-sm uppercase tracking-wider mb-4">Area & Bantuan</h4>
                    <ul class="space-y-2 text-sm text-[#47464E]">
                        <li>Lowokwaru & Soekarno Hatta</li>
                        <li>Klojen & Sekitar Kampus UB/UM</li>
                        <li>Blimbing & Sukun</li>
                        <li>Pertanyaan Umum (FAQ)</li>
                        <li>Syarat & Ketentuan Garansi</li>
                    </ul>
                </div>
            </div>
            <div class="mb-12">
                <h4 class="font-bold text-sm uppercase tracking-wider mb-4">Operasional & Kontak</h4>
                <div class="space-y-2 text-sm text-[#47464E]">
                    <p>Jl. Soekarno Hatta No. 45, Mojolangu, Kec. Lowokwaru, Kota Malang, Jawa Timur 65142</p>
                    <p>Buka Setiap Hari: 07.00 - 21.00 WIB</p>
                    <p>WhatsApp: 0812-3456-7890</p>
                </div>
            </div>
            <div class="pt-6 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-[#47464E]">
                <p>© 2025 Rodeo Laundry Malang. Hak Cipta Dilindungi Undang-Undang.</p>
                <div class="flex gap-4">
                    <span class="flex items-center gap-1 font-bold"><div class="w-3 h-3 bg-[#4E6700]"></div> Garansi Bersih Higienis</span>
                    <span class="flex items-center gap-1 font-bold"><div class="w-3 h-3 bg-[#4E6700]"></div> 1 Mesin 1 Pelanggan</span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>