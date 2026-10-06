<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rodeo Laundry Malang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FAF8FF] text-[#1A1743] font-sans antialiased">

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
                <a href="{{ url('/') }}" class="px-4 py-1.5 bg-[#EAEDFF] rounded-full text-sm font-bold">Beranda</a>
                <a href="{{ route('pemesanan') }}" class="px-4 py-1.5 text-sm font-semibold text-[#47464E] hover:text-[#1A1743] rounded-full">Layanan</a>
                <a href="{{ route('pesanan') }}" class="px-4 py-1.5 text-sm font-semibold text-[#47464E] hover:text-[#1A1743]">Pesanan</a>
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

    <main class="pt-20">
        <!-- ================= HERO SECTION ================= -->
        <section class="max-w-[1440px] mx-auto px-6 py-16 md:py-24 flex flex-col md:flex-row items-center gap-12">
            <div class="flex-1 flex flex-col gap-4">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight">
                    Cucian Numpuk?<br>
                    Biar Kami yang Urus<span class="text-[#A7D717]">.</span>
                </h1>
                <p class="text-[#47464E] text-lg max-w-lg">
                    Layanan laundry profesional, cepat, bersih, dan wangi. Siap jemput dan antar langsung ke depan pintu Anda di seluruh Malang Raya.
                </p>
                <div class="mt-4">
                    <a href="{{ route('pemesanan') }}" class="inline-flex items-center gap-2 px-10 py-3.5 bg-[#2F2D59] text-white rounded-full font-semibold shadow-[0_12px_28px_-8px_rgba(47,45,89,0.35)] 
                              transition-all duration-300 ease-out
                              hover:shadow-[0_15px_35px_-8px_rgba(47,45,89,0.5)]
                              hover:-translate-y-1">
                        Mulai Sekarang
                        <div class="w-3 h-3 bg-[#BFF138]"></div>
                    </a>
                </div>
            </div>
            <div class="flex-1 w-full relative">
                <img src="https://images.unsplash.com/photo-1545173168-9f1947eebb7f?q=80&w=2071&auto=format&fit=crop" alt="Laundry" class="w-full h-auto rounded-[48px] shadow-2xl object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-[#1A1743]/30 to-transparent rounded-[48px]"></div>
            </div>
        </section>

        <!-- ================= FITUR BAR ================= -->
        <section class="max-w-[1440px] mx-auto px-6 pb-16">
            <div class="bg-white p-6 rounded-[32px] shadow-sm flex flex-wrap justify-between gap-6">
                <div class="flex items-center gap-3 flex-1 min-w-[200px]">
                    <div class="w-10 h-10 bg-[#BFF138] rounded-full flex items-center justify-center"><div class="w-4 h-3 bg-[#131B2E]"></div></div>
                    <div><div class="text-sm font-bold">Gratis Antar Jemput</div><div class="text-xs text-[#47464E]">Seluruh area Kota Malang</div></div>
                </div>
                <div class="flex items-center gap-3 flex-1 min-w-[200px]">
                    <div class="w-10 h-10 bg-[#BFF138] rounded-full flex items-center justify-center"><div class="w-4 h-4 bg-[#131B2E]"></div></div>
                    <div><div class="text-sm font-bold">Cepat & Tepat Waktu</div><div class="text-xs text-[#47464E]">Express 6 Jam & Reguler</div></div>
                </div>
                <div class="flex items-center gap-3 flex-1 min-w-[200px]">
                    <div class="w-10 h-10 bg-[#BFF138] rounded-full flex items-center justify-center"><div class="w-4 h-4 bg-[#131B2E]"></div></div>
                    <div><div class="text-sm font-bold">Bersih & Wangi Tahan Lama</div><div class="text-xs text-[#47464E]">Detergen ramah serat kain</div></div>
                </div>
                <div class="flex items-center gap-3 flex-1 min-w-[200px]">
                    <div class="w-10 h-10 bg-[#BFF138] rounded-full flex items-center justify-center"><div class="w-3.5 h-4 bg-[#131B2E]"></div></div>
                    <div><div class="text-sm font-bold">Terpercaya & Higienis</div><div class="text-xs text-[#47464E]">1 Mesin khusus 1 Pelanggan</div></div>
                </div>
            </div>
        </section>

        <!-- ================= PROMO SECTION ================= -->
        <section class="max-w-[1440px] mx-auto px-6 py-12">
            <div class="relative bg-gradient-to-b from-[#1A1743] to-[#251F7A] rounded-[48px] p-8 md:p-16 overflow-hidden shadow-2xl">
                <div class="absolute w-64 h-64 bg-[#BFF138]/20 blur-3xl rounded-full -top-10 -left-10"></div>
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8">
                    <div class="flex-1">
                        <span class="inline-block px-3 py-1 bg-[#BFF138] text-[#131B2E] text-xs font-bold rounded-full mb-4">PROMO KHUSUS PELANGGAN BARU</span>
                        <h2 class="text-3xl md:text-4xl font-bold text-white leading-tight mb-3">Siap Mencoba Layanan<br>Kami?</h2>
                        <p class="text-[#9895C8] text-sm md:text-base">Dapatkan diskon 20% untuk pesanan pertama Anda. Kurir kami siap menjemput cucian sekarang juga ke lokasi Anda!</p>
                    </div>
                    <div class="flex flex-col gap-4 w-full md:w-auto">
                        <a href="{{ route('pemesanan') }}" class="flex items-center justify-center gap-2 px-8 py-3.5 bg-[#BFF138] text-[#131B2E] font-bold rounded-full 
                                  transition-all duration-300 ease-out
                                  hover:shadow-[0_15px_35px_rgba(204,255,70,0.65)]
                                  hover:-translate-y-1
                                  hover:brightness-110">
                            Pesan Jasa Laundry Sekarang
                        </a>
                        <a href="#" class="flex items-center justify-center gap-2 px-6 py-3.5 bg-white/10 text-white font-semibold rounded-full
                                  transition-all duration-300 ease-out
                                  hover:bg-white/20 
                                  hover:-translate-y-1">
                            Hubungi CS WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= LAYANAN SECTION ================= -->
        <section class="max-w-[1440px] mx-auto px-6 py-16">
            <div class="text-center mb-12">
                <span class="inline-block px-4 py-1 bg-[#C2F43B]/50 text-[#4E6700] text-xs font-extrabold uppercase rounded-full mb-3">Pilihan Layanan</span>
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Layanan Andalan Kami</h2>
                <p class="text-[#47464E] max-w-xl mx-auto">Semua kebutuhan laundry pakaian & perlengkapan Anda dalam satu tempat dengan penanganan standar hotel bintang lima.</p>
            </div>

            <!-- Grid Layanan Dinamis -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($services as $service)
                    <a href="{{ route('pemesanan', ['service' => $service->id]) }}"
                        class="group bg-white p-8 rounded-[32px] border border-[#E2E7FF]/60 
                                flex flex-col justify-between 
                                transition-all duration-300 ease-out
                                hover:border-[#BFF138] 
                                hover:-translate-y-2
                                cursor-pointer">
                        <div>
                            <div class="flex justify-between items-center mb-4">
                                <div class="w-10 h-10 bg-[#1A1743] rounded flex items-center justify-center 
                                            transition-colors duration-300 group-hover:bg-[#BFF138]">
                                    <div class="w-4 h-5 bg-[#C2F43B] transition-colors duration-300 group-hover:bg-[#1A1743]"></div>
                                </div>
                                <div class="px-3 py-1 bg-[#EAEDFF] rounded-full text-sm font-bold">
                                    Rp{{ number_format($service->price, 0, ',', '.') }} 
                                    <span class="text-xs font-normal text-[#47464E]">/ pcs</span>
                                </div>
                            </div>
                            <h3 class="text-lg font-bold mb-2">{{ $service->name }}</h3>
                            <p class="text-sm text-[#47464E] mb-6">{{ $service->description }}</p>
                        </div>
                        <div class="pt-4 border-t border-[#E2E7FF]/60 flex justify-between items-center">
                            <span class="px-2.5 py-0.5 bg-[#F2F3FF] rounded-full text-xs font-semibold text-[#47464E]">24-48 Jam</span>
                            <span class="text-sm font-bold flex items-center gap-1 
                                         transition-transform duration-300 group-hover:translate-x-1">
                                Pilih Layanan <span class="text-xs">→</span>
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-10 bg-white rounded-[32px] border border-[#E2E7FF]/60">
                        <p class="text-[#47464E]">Belum ada layanan yang ditambahkan ke database.</p>
                    </div>
                @endforelse
            </div>

            <!-- Tombol Lihat Semua Layanan -->
            <div class="text-center mt-10">
                <a href="{{ route('pemesanan') }}" class="inline-flex items-center gap-2 px-8 py-3 bg-white text-[#1A1743] font-bold rounded-full border-2 border-[#E2E7FF]
                          transition-all duration-300 ease-out
                          hover:border-[#BFF138] 
                          hover:shadow-[0_15px_35px_-8px_rgba(191,241,56,0.4)]
                          hover:-translate-y-1">
                    Lihat Semua Layanan
                    <span>→</span>
                </a>
            </div>
        </section>

        <!-- ================= CARA KERJA SECTION ================= -->
        <section class="max-w-[1440px] mx-auto px-6 py-16">
            <div class="bg-gradient-to-b from-[#1A1743] to-[#251F7A] rounded-[48px] p-8 md:p-16 relative overflow-hidden shadow-2xl">
                <div class="absolute w-80 h-80 bg-[#BFF138]/10 blur-3xl rounded-full -top-20 right-0"></div>
                <div class="relative z-10 text-center mb-12">
                    <span class="inline-block px-4 py-1 bg-[#BFF138] text-[#131B2E] text-xs font-bold uppercase rounded-full mb-3">Cara Kerja</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Laundry Jadi Lebih Mudah</h2>
                    <p class="text-[#9895C8] max-w-2xl mx-auto">4 langkah praktis tanpa repot keluar rumah. Duduk santai, biar tim profesional kami yang selesaikan.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative z-10">
                    <div class="bg-white/12 backdrop-blur-md p-6 rounded-[32px] border border-white/10">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-4xl font-extrabold text-[#BFF138] opacity-90">01</span>
                            <div class="w-10 h-10 bg-[#BFF138]/20 rounded-full flex items-center justify-center"><div class="w-4 h-4 bg-[#BFF138]"></div></div>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Pilih Layanan</h3>
                        <p class="text-sm text-[#9895C8]">Tentukan jenis cucian dan paket yang Anda inginkan melalui website praktis atau chat WhatsApp customer service kami.</p>
                    </div>
                    <div class="bg-white/12 backdrop-blur-md p-6 rounded-[32px] border border-white/10">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-4xl font-extrabold text-[#BFF138] opacity-90">02</span>
                            <div class="w-10 h-10 bg-[#BFF138]/20 rounded-full flex items-center justify-center"><div class="w-4 h-4 bg-[#BFF138]"></div></div>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Pesan Jadwal</h3>
                        <p class="text-sm text-[#9895C8]">Atur jam penjemputan yang fleksibel sesuai waktu luang Anda, baik di kos, rumah, kantor, atau apartemen.</p>
                    </div>
                    <div class="bg-white/12 backdrop-blur-md p-6 rounded-[32px] border border-white/10">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-4xl font-extrabold text-[#BFF138] opacity-90">03</span>
                            <div class="w-10 h-10 bg-[#BFF138]/20 rounded-full flex items-center justify-center"><div class="w-4 h-4 bg-[#BFF138]"></div></div>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Dijemput Kurir</h3>
                        <p class="text-sm text-[#9895C8]">Kurir ramah kami mengambil cucian langsung di lokasi Anda di seluruh Malang dengan penimbangan digital transparan di tempat.</p>
                    </div>
                    <div class="bg-white/12 backdrop-blur-md p-6 rounded-[32px] border border-white/10">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-4xl font-extrabold text-[#BFF138] opacity-90">04</span>
                            <div class="w-10 h-10 bg-[#BFF138]/20 rounded-full flex items-center justify-center"><div class="w-4 h-4 bg-[#BFF138]"></div></div>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Selesai & Diantar</h3>
                        <p class="text-sm text-[#9895C8]">Pakaian dikembalikan dalam kondisi bersih sempurna, rapi, harum semerbak, siap pakai ke lemari pakaian Anda.</p>
                    </div>
                </div>
            </div>
        </section>
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