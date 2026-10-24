<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rodeo Laundry Malang — Bersih, Higienis & Antar Jemput Tepat Waktu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#FAF8FF] text-[#1A1743] font-sans antialiased selection:bg-[#BFF108] selection:text-[#1A1743]">

    <!-- ================= HEADER ================= -->
    <header class="fixed top-0 w-full h-16 md:h-20 bg-[#FAF8FF]/85 backdrop-blur-md shadow-sm z-50 border-b border-[#E2E7FF]/60"
            x-data="{ mobileOpen: false }">
        <div class="max-w-[1440px] mx-auto h-full px-4 md:px-6 flex items-center justify-between">

            <!-- Logo -->
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <div class="w-9 h-9 md:w-10 md:h-10 bg-[#2F2D59] rounded-xl flex items-center justify-center shadow-lg shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#bff108ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h3"/><path d="M17 6h.01"/><rect width="18" height="20" x="3" y="2" rx="2"/><circle cx="12" cy="13" r="5"/><path d="M12 18a2.5 2.5 0 0 0 0-5 2.5 2.5 0 0 1 0-5"/></svg>
                </div>
                <div>
                    <div class="text-base md:text-lg font-semibold leading-none">
                        Rodeo Laundry<span class="text-[#bff108ff]">.</span>
                    </div>
                    <div class="text-[9px] md:text-[10px] font-bold uppercase tracking-wider text-[#47464E] mt-1">Malang</div>
                </div>
            </a>

            <!-- Nav Desktop -->
            <nav class="hidden md:flex items-center gap-1 bg-[#F2F3FF] p-1.5 rounded-full">
                <a href="{{ url('/') }}" class="px-4 py-1.5 bg-[#EAEDFF] rounded-full text-sm font-bold text-[#1A1743]">Beranda</a>
                <a href="{{ route('pemesanan') }}" class="px-4 py-1.5 text-sm font-semibold text-[#47464E] hover:text-[#1A1743] rounded-full transition">Layanan</a>
                <a href="{{ route('pesanan.index') }}" class="px-4 py-1.5 text-sm font-semibold text-[#47464E] hover:text-[#1A1743] rounded-full transition">Pesanan</a>
            </nav>

            <!-- Right Side -->
            <div class="flex items-center gap-2 md:gap-3">
                <a href="https://wa.me/6281234567890" target="_blank" class="hidden sm:flex items-center gap-2 px-3 md:px-4 py-2 bg-[#EAEDFF] hover:bg-[#dfe4ff] rounded-full text-xs md:text-sm font-semibold text-[#131B2E] transition">
                    <i class="fa-brands fa-whatsapp text-sm md:text-base text-[#25D366]"></i> WhatsApp
                </a>

                @auth
                    <div class="flex items-center gap-2">
                        <a href="{{ route('profile.edit') }}" class="w-8 h-8 md:w-9 md:h-9 bg-[#1A1743] hover:bg-[#2F2D59] text-white rounded-full flex items-center justify-center transition shadow-sm shrink-0" title="{{ Auth::user()->username }}">
                            <i class="fa-regular fa-user text-xs md:text-sm"></i>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-xs text-[#47464E] hover:text-[#1A1743] font-medium hidden sm:inline px-2 py-1" title="Keluar">
                                Keluar
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login', ['ref' => 'navbar']) }}" class="px-4 md:px-5 py-2 bg-[#1A1743] text-white rounded-full text-xs md:text-sm font-bold hover:bg-[#2F2D59] shadow-sm transition">
                        Masuk
                    </a>  
                @endauth

                <!-- Hamburger Mobile -->
                <button @click="mobileOpen = !mobileOpen"
                        class="md:hidden w-9 h-9 flex items-center justify-center rounded-full bg-[#F2F3FF] hover:bg-[#EAEDFF] transition"
                        aria-label="Toggle menu">
                    <svg x-show="!mobileOpen" class="w-5 h-5 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileOpen" class="w-5 h-5 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Dropdown Mobile -->
        <div x-show="mobileOpen" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            @click.outside="mobileOpen = false"
            class="md:hidden absolute top-full left-0 right-0 bg-white shadow-xl border-t border-[#E2E7FF] z-40">
            <div class="px-4 py-3 space-y-1">
                <a href="{{ url('/') }}" class="flex items-center px-4 py-3 rounded-xl text-sm font-bold bg-[#EAEDFF] text-[#1A1743]">
                    <i class="fa-solid fa-house w-5 text-center text-[#1A1743] mr-2"></i> Beranda
                </a>
                <a href="{{ route('pemesanan') }}" class="flex items-center px-4 py-3 rounded-xl text-sm font-semibold text-[#47464E] hover:bg-[#F2F3FF] hover:text-[#1A1743] transition">
                    <i class="fa-solid fa-layer-group w-5 text-center text-[#9895C8] mr-2"></i> Layanan
                </a>
                <a href="{{ route('pesanan.index') }}" class="flex items-center px-4 py-3 rounded-xl text-sm font-semibold text-[#47464E] hover:bg-[#F2F3FF] hover:text-[#1A1743] transition">
                    <i class="fa-solid fa-receipt w-5 text-center text-[#9895C8] mr-2"></i> Pesanan
                </a>
            </div>
        </div>
    </header>

    <main class="pt-20 md:pt-24">

        <!-- ================= HERO SECTION ================= -->
        <section class="max-w-[1440px] mx-auto px-4 md:px-6 py-10 md:py-16 lg:py-20">
            <div class="flex flex-col lg:flex-row items-center gap-8 lg:gap-14">
                <div class="flex-1 flex flex-col gap-4 w-full">
                    <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold leading-[1.15] tracking-tight">
                        Cucian Numpuk?<br>
                        Biar Kami yang Urus<span class="text-[#bff108ff]">.</span>
                    </h1>
                    <p class="text-[#47464E] text-sm sm:text-base md:text-lg max-w-lg leading-relaxed">
                        Layanan laundry profesional, cepat, bersih, dan wangi. Siap jemput dan antar langsung ke depan pintu Anda di seluruh Malang Raya.
                    </p>
                    <div class="mt-2 md:mt-4">
                        <a href="{{ route('pemesanan') }}" class="inline-flex items-center gap-2.5 px-7 md:px-9 py-3 md:py-3.5 bg-[#2F2D59] hover:bg-[#1A1743] text-white rounded-full font-semibold text-sm md:text-base shadow-[0_12px_28px_-8px_rgba(47,45,89,0.35)] transition-all duration-300 ease-out hover:shadow-[0_15px_35px_-8px_rgba(47,45,89,0.5)] hover:-translate-y-1">
                            <span>Mulai Sekarang</span>
                            <i class="fa-solid fa-arrow-right text-[#BFF108] text-sm"></i>
                        </a>
                    </div>
                </div>

                <div class="flex-1 w-full relative">
                    <div class="relative overflow-hidden rounded-[28px] md:rounded-[44px] shadow-2xl border border-white">
                        <img src="{{ asset('images/hero-laundry.jpg') }}" 
                             alt="Interior Laundry Room Rodeo Laundry" 
                             class="w-full h-auto max-h-[460px] object-cover rounded-[28px] md:rounded-[44px] transition-transform duration-700 hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#1A1743]/15 via-transparent to-transparent pointer-events-none rounded-[28px] md:rounded-[44px]"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= FITUR BAR ================= -->
        <section class="max-w-[1440px] mx-auto px-4 md:px-6 pb-12 md:pb-16">
            <div class="bg-white p-5 md:p-6 rounded-[24px] md:rounded-[32px] shadow-sm border border-[#E2E7FF]/60 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                
                <!-- Fitur 1 -->
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 bg-[#BFF108] rounded-full flex items-center justify-center shrink-0 shadow-sm text-[#131B2E]">
                        <i class="fa-solid fa-truck-fast text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-bold text-[#1A1743]">Gratis Antar Jemput</div>
                        <div class="text-xs text-[#47464E]">Seluruh area Kota Malang</div>
                    </div>
                </div>

                <!-- Fitur 2 -->
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 bg-[#BFF108] rounded-full flex items-center justify-center shrink-0 shadow-sm text-[#131B2E]">
                        <i class="fa-regular fa-clock text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-bold text-[#1A1743]">Cepat & Tepat Waktu</div>
                        <div class="text-xs text-[#47464E]">Express 6 Jam & Reguler</div>
                    </div>
                </div>

                <!-- Fitur 3 -->
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 bg-[#BFF108] rounded-full flex items-center justify-center shrink-0 shadow-sm text-[#131B2E]">
                        <i class="fa-solid fa-wand-magic-sparkles text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-bold text-[#1A1743]">Bersih & Wangi Tahan Lama</div>
                        <div class="text-xs text-[#47464E]">Detergen ramah serat kain</div>
                    </div>
                </div>

                <!-- Fitur 4 -->
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 bg-[#BFF108] rounded-full flex items-center justify-center shrink-0 shadow-sm text-[#131B2E]">
                        <i class="fa-solid fa-shield-halved text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-bold text-[#1A1743]">Terpercaya & Higienis</div>
                        <div class="text-xs text-[#47464E]">1 Mesin khusus 1 Pelanggan</div>
                    </div>
                </div>

            </div>
        </section>

        <!-- ================= PROMO SECTION ================= -->
        <section class="max-w-[1440px] mx-auto px-4 md:px-6 py-6 md:py-10">
            <div class="relative bg-gradient-to-r from-[#1A1743] via-[#221E5C] to-[#2B237C] rounded-[24px] md:rounded-[40px] p-6 sm:p-10 md:p-14 overflow-hidden shadow-2xl">
                <div class="absolute w-72 h-72 bg-[#BFF108]/15 blur-3xl rounded-full -top-10 -left-10 pointer-events-none"></div>
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 md:gap-8">
                    <div class="flex-1">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#BFF108] text-[#131B2E] text-[10px] md:text-xs font-extrabold uppercase rounded-full mb-3 md:mb-4 shadow-sm">
                            <i class="fa-solid fa-tag text-xs"></i>
                            PROMO KHUSUS PELANGGAN BARU
                        </span>
                        <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-white leading-tight mb-2 md:mb-3">
                            Siap Mencoba Layanan Kami?
                        </h2>
                        <p class="text-[#B9B6E5] text-xs sm:text-sm md:text-base max-w-xl leading-relaxed">
                            Dapatkan diskon 20% untuk pesanan pertama Anda. Kurir kami siap menjemput cucian sekarang juga ke lokasi Anda!
                        </p>
                    </div>
                    <div class="flex flex-col gap-3 w-full md:w-auto md:min-w-[280px]">
                        <a href="{{ route('pemesanan') }}" class="flex items-center justify-center gap-2 px-6 md:px-8 py-3.5 bg-[#BFF108] text-[#131B2E] font-bold rounded-full text-sm md:text-base transition-all duration-300 ease-out hover:shadow-[0_15px_35px_rgba(191,241,8,0.45)] hover:-translate-y-1 hover:brightness-105">
                            <i class="fa-solid fa-cart-shopping text-sm"></i>
                            <span>Pesan Jasa Laundry Sekarang</span>
                        </a>
                        <a href="https://wa.me/6281234567890" target="_blank" class="flex items-center justify-center gap-2 px-6 md:px-8 py-3.5 bg-white/10 hover:bg-white/20 text-white border border-white/20 font-bold rounded-full text-sm md:text-base backdrop-blur-md transition-all duration-300 ease-out hover:-translate-y-1">
                            <i class="fa-brands fa-whatsapp text-base text-[#25D366]"></i>
                            <span>Hubungi CS WhatsApp</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= LAYANAN SECTION ================= -->
        <section class="max-w-[1440px] mx-auto px-4 md:px-6 py-12 md:py-16">
            <div class="text-center mb-8 md:mb-12">
                <span class="inline-block px-4 py-1 bg-[#BFF108] text-[#131B2E] text-[10px] md:text-xs font-extrabold uppercase rounded-full mb-3">Pilihan Layanan</span>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold mb-3">Layanan Andalan Kami</h2>
                <p class="text-xs sm:text-sm md:text-base text-[#47464E] max-w-xl mx-auto">Semua kebutuhan laundry pakaian & perlengkapan Anda dalam satu tempat dengan penanganan standar hotel bintang lima.</p>
            </div>

            <!-- Grid Layanan Dinamis (Figma Consistent) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 md:gap-6">
                @forelse($services as $service)
                    @php
                        $lname = strtolower($service->name);
                        $isShoe = str_contains($lname, 'sepatu');
                        $isKiloan = str_contains($lname, 'kiloan');
                        $isPremium = str_contains($lname, 'jas') || str_contains($lname, 'premium') || str_contains($lname, 'formal');
                        $isBoneka = str_contains($lname, 'boneka') || str_contains($lname, 'bantal');
                        $isTas = str_contains($lname, 'tas') || str_contains($lname, 'ransel');
                        $isBed = str_contains($lname, 'bed') || str_contains($lname, 'sprei') || str_contains($lname, 'selimut');

                        $badgeLabel = match(true) {
                            $isKiloan => '24–48 Jam',
                            $isShoe => 'Treatment Khusus',
                            $isPremium => 'Garansi Bahan',
                            $isBoneka => 'Hypoallergenic',
                            $isTas => 'Deep Cleaning',
                            $isBed => 'Hygienic Dry',
                            default => 'Layanan Kilat',
                        };
                    @endphp

                    <a href="{{ route('pemesanan', ['service' => $service->id]) }}"
                        class="group bg-white p-6 md:p-8 rounded-[24px] md:rounded-[32px] 
                               {{ $isShoe ? 'border-2 border-[#BFF108] shadow-md' : 'border border-[#E2E7FF]/70' }} 
                               flex flex-col justify-between 
                               transition-all duration-300 ease-out
                               hover:border-[#BFF108] hover:shadow-xl hover:-translate-y-1.5
                               cursor-pointer">
                        <div>
                            <div class="flex justify-between items-center mb-4 gap-2">
                                <div class="w-11 h-11 bg-[#1A1743] text-[#BFF108] group-hover:bg-[#BFF108] group-hover:text-[#131B2E] rounded-xl flex items-center justify-center shrink-0 transition-colors duration-300">
                                    @if($isShoe)
                                        <i class="fa-solid fa-shoe-prints text-base"></i>
                                    @elseif($isKiloan)
                                        <i class="fa-solid fa-weight-hanging text-base"></i>
                                    @elseif($isPremium)
                                        <i class="fa-solid fa-shirt text-base"></i>
                                    @elseif($isBoneka)
                                        <i class="fa-solid fa-mattress-pillow text-base"></i>
                                    @elseif($isTas)
                                        <i class="fa-solid fa-bag-shopping text-base"></i>
                                    @elseif($isBed)
                                        <i class="fa-solid fa-rug text-base"></i>
                                    @else
                                        <i class="fa-solid fa-jug-detergent text-base"></i>
                                    @endif
                                </div>
                                <div class="px-3 py-1 bg-[#EAEDFF] text-[#1A1743] rounded-full text-xs md:text-sm font-bold shrink-0">
                                    Rp{{ number_format($service->price, 0, ',', '.') }} 
                                    <span class="text-[10px] md:text-xs font-normal opacity-80">/ {{ $service->unit }}</span>
                                </div>
                            </div>
                            <h3 class="text-base md:text-lg font-bold mb-2 text-[#1A1743]">{{ $service->name }}</h3>
                            <p class="text-xs md:text-sm text-[#47464E] leading-relaxed mb-4 md:mb-6 line-clamp-2">{{ $service->description }}</p>
                        </div>
                        <div class="pt-3.5 md:pt-4 border-t border-[#E2E7FF]/70 flex justify-between items-center">
                            <span class="px-2.5 py-1 bg-[#F2F3FF] text-[#1A1743] rounded-full text-[11px] font-bold">
                                {{ $badgeLabel }}
                            </span>
                            <span class="text-xs md:text-sm font-bold flex items-center gap-1.5 transition-transform duration-300 group-hover:translate-x-1 text-[#1A1743]">
                                <span>Pilih Layanan</span>
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-12 bg-white rounded-[24px] md:rounded-[32px] border border-[#E2E7FF]/60">
                        <p class="text-[#47464E] text-sm">Belum ada layanan yang tersedia.</p>
                    </div>
                @endforelse
            </div>

            <!-- Tombol Lihat Semua -->
            <div class="text-center mt-10 md:mt-12">
                <a href="{{ route('pemesanan') }}" class="inline-flex items-center gap-2 px-8 py-3 bg-white hover:bg-[#F2F3FF] text-[#1A1743] font-bold rounded-full border-2 border-[#E2E7FF] text-sm md:text-base transition-all duration-300 ease-out hover:border-[#BFF108] hover:shadow-[0_12px_28px_-8px_rgba(191,241,8,0.4)] hover:-translate-y-1">
                    <span>Lihat Semua Layanan</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </section>

        <!-- ================= CARA KERJA SECTION ================= -->
        <section class="max-w-[1440px] mx-auto px-4 md:px-6 py-10 md:py-16">
            <div class="bg-gradient-to-b from-[#1A1743] via-[#221E5C] to-[#2B237C] rounded-[24px] md:rounded-[48px] p-6 sm:p-10 md:p-16 relative overflow-hidden shadow-2xl">
                <div class="absolute w-80 h-80 bg-[#BFF108]/10 blur-3xl rounded-full -top-20 right-0 pointer-events-none"></div>
                <div class="relative z-10 text-center mb-8 md:mb-12">
                    <span class="inline-block px-4 py-1 bg-[#BFF108] text-[#131B2E] text-[10px] md:text-xs font-bold uppercase rounded-full mb-3">Cara Kerja</span>
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-white mb-3">Laundry Jadi Lebih Mudah</h2>
                    <p class="text-xs sm:text-sm md:text-base text-[#9895C8] max-w-2xl mx-auto">4 langkah praktis tanpa repot keluar rumah. Duduk santai, biar tim profesional kami yang selesaikan.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 relative z-10">
                    
                    <!-- Step 01 -->
                    <div class="bg-white/10 backdrop-blur-md p-5 md:p-6 rounded-[24px] md:rounded-[32px] border border-white/15 hover:bg-white/15 transition-all">
                        <div class="flex justify-between items-center mb-3 md:mb-4">
                            <span class="text-3xl md:text-4xl font-extrabold text-[#BFF108]">01</span>
                            <div class="w-10 h-10 bg-[#BFF108]/20 text-[#BFF108] rounded-full flex items-center justify-center">
                                <i class="fa-solid fa-mobile-screen-button text-base"></i>
                            </div>
                        </div>
                        <h3 class="text-base md:text-lg font-bold text-white mb-2">Pilih Layanan</h3>
                        <p class="text-xs md:text-sm text-[#B9B6E5] leading-relaxed">Tentukan jenis cucian dan paket yang Anda inginkan melalui website praktis atau chat WhatsApp customer service kami.</p>
                    </div>

                    <!-- Step 02 -->
                    <div class="bg-white/10 backdrop-blur-md p-5 md:p-6 rounded-[24px] md:rounded-[32px] border border-white/15 hover:bg-white/15 transition-all">
                        <div class="flex justify-between items-center mb-3 md:mb-4">
                            <span class="text-3xl md:text-4xl font-extrabold text-[#BFF108]">02</span>
                            <div class="w-10 h-10 bg-[#BFF108]/20 text-[#BFF108] rounded-full flex items-center justify-center">
                                <i class="fa-regular fa-calendar-check text-base"></i>
                            </div>
                        </div>
                        <h3 class="text-base md:text-lg font-bold text-white mb-2">Pesan Jadwal</h3>
                        <p class="text-xs md:text-sm text-[#B9B6E5] leading-relaxed">Atur jam penjemputan yang fleksibel sesuai waktu luang Anda, baik di kos, rumah, kantor, atau apartemen.</p>
                    </div>

                    <!-- Step 03 -->
                    <div class="bg-white/10 backdrop-blur-md p-5 md:p-6 rounded-[24px] md:rounded-[32px] border border-white/15 hover:bg-white/15 transition-all">
                        <div class="flex justify-between items-center mb-3 md:mb-4">
                            <span class="text-3xl md:text-4xl font-extrabold text-[#BFF108]">03</span>
                            <div class="w-10 h-10 bg-[#BFF108]/20 text-[#BFF108] rounded-full flex items-center justify-center">
                                <i class="fa-solid fa-truck text-base"></i>
                            </div>
                        </div>
                        <h3 class="text-base md:text-lg font-bold text-white mb-2">Dijemput Kurir</h3>
                        <p class="text-xs md:text-sm text-[#B9B6E5] leading-relaxed">Kurir ramah kami mengambil cucian langsung di lokasi Anda di seluruh Malang dengan penimbangan digital transparan di tempat.</p>
                    </div>

                    <!-- Step 04 -->
                    <div class="bg-white/10 backdrop-blur-md p-5 md:p-6 rounded-[24px] md:rounded-[32px] border border-white/15 hover:bg-white/15 transition-all">
                        <div class="flex justify-between items-center mb-3 md:mb-4">
                            <span class="text-3xl md:text-4xl font-extrabold text-[#BFF108]">04</span>
                            <div class="w-10 h-10 bg-[#BFF108]/20 text-[#BFF108] rounded-full flex items-center justify-center">
                                <i class="fa-solid fa-circle-check text-base"></i>
                            </div>
                        </div>
                        <h3 class="text-base md:text-lg font-bold text-white mb-2">Selesai & Diantar</h3>
                        <p class="text-xs md:text-sm text-[#B9B6E5] leading-relaxed">Pakaian dikembalikan dalam kondisi bersih sempurna, rapi, harum semerbak, siap pakai ke lemari pakaian Anda.</p>
                    </div>

                </div>
            </div>
        </section>
    </main>

    <!-- ================= FOOTER ================= -->
    <footer class="bg-white border-t border-[#E2E7FF]/70 mt-12 md:mt-16">
        <div class="max-w-[1440px] mx-auto px-4 md:px-6 py-12 md:py-16">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 md:gap-10 mb-10 md:mb-12">
                <!-- Col 1: Brand Info -->
                <div>
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-9 h-9 md:w-10 md:h-10 bg-[#2F2D59] rounded-xl flex items-center justify-center shadow-lg shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#bff108ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h3"/><path d="M17 6h.01"/><rect width="18" height="20" x="3" y="2" rx="2"/><circle cx="12" cy="13" r="5"/><path d="M12 18a2.5 2.5 0 0 0 0-5 2.5 2.5 0 0 1 0-5"/></svg>
                        </div>
                        <span class="text-lg md:text-xl font-bold tracking-tight text-[#1A1743]">Rodeo Laundry</span>
                    </div>
                    <p class="text-[#47464E] text-xs md:text-sm mb-4 leading-relaxed">
                        Laundry profesional, cepat, bersih, dan wangi di Malang. Layanan antar-jemput ekspres untuk kenyamanan pakaian higienis Anda setiap hari.
                    </p>
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-[#EAEDFF] text-[#1A1743] rounded-full text-xs font-semibold">
                        <span class="w-2 h-2 rounded-full bg-[#BFF108]"></span>
                        <span>Outlet Soekarno Hatta Malang</span>
                    </div>
                </div>

                <!-- Col 2: Layanan Kami -->
                <div>
                    <h4 class="font-bold text-xs md:text-sm uppercase tracking-wider mb-3 md:mb-4 text-[#1A1743]">Layanan Kami</h4>
                    <ul class="space-y-2.5 text-xs md:text-sm text-[#47464E]">
                        <li><a href="{{ route('pemesanan') }}" class="hover:text-[#1A1743] transition">Kiloan Premium</a></li>
                        <li><a href="{{ route('pemesanan') }}" class="hover:text-[#1A1743] transition">Dry Cleaning Satuan</a></li>
                        <li><a href="{{ route('pemesanan') }}" class="hover:text-[#1A1743] transition">Cuci Sepatu & Tas</a></li>
                        <li><a href="{{ route('pemesanan') }}" class="hover:text-[#1A1743] transition">Bed Cover & Karpet</a></li>
                        <li><a href="{{ route('pemesanan') }}" class="hover:text-[#1A1743] transition">Express 3 Jam</a></li>
                    </ul>
                </div>

                <!-- Col 3: Area & Bantuan -->
                <div>
                    <h4 class="font-bold text-xs md:text-sm uppercase tracking-wider mb-3 md:mb-4 text-[#1A1743]">Area & Bantuan</h4>
                    <ul class="space-y-2.5 text-xs md:text-sm text-[#47464E]">
                        <li>Lowokwaru & Soekarno Hatta</li>
                        <li>Klojen & Sekitar Kampus UB/UM</li>
                        <li>Blimbing & Sukun</li>
                        <li>Pertanyaan Umum (FAQ)</li>
                        <li>Syarat & Ketentuan Garansi</li>
                    </ul>
                </div>

                <!-- Col 4: Operasional & Kontak -->
                <div>
                    <h4 class="font-bold text-xs md:text-sm uppercase tracking-wider mb-3 md:mb-4 text-[#1A1743]">Operasional & Kontak</h4>
                    <div class="space-y-3 text-xs md:text-sm text-[#47464E]">
                        <p class="flex items-start gap-2.5">
                            <i class="fa-solid fa-location-dot text-[#9895C8] mt-1 shrink-0"></i>
                            <span>Jl. Soekarno Hatta No. 45, Mojolangu, Kec. Lowokwaru, Kota Malang, Jawa Timur 65142</span>
                        </p>
                        <p class="flex items-center gap-2.5">
                            <i class="fa-regular fa-clock text-[#9895C8] shrink-0"></i>
                            <span>Buka Setiap Hari: 07.00 - 21.00 WIB</span>
                        </p>
                        <p class="flex items-center gap-2.5">
                            <i class="fa-brands fa-whatsapp text-[#25D366] shrink-0"></i>
                            <span>WhatsApp: 0812-3456-7890</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-[#E2E7FF]/60 flex flex-col md:flex-row justify-between items-center gap-3 md:gap-4 text-[10px] md:text-xs text-[#47464E]">
                <p class="text-center md:text-left">© 2025 Rodeo Laundry Malang. Hak Cipta Dilindungi Undang-Undang.</p>
                <div class="flex gap-4 sm:gap-6 flex-wrap justify-center">
                    <span class="flex items-center gap-1.5 font-bold text-[#1A1743]">
                        <i class="fa-solid fa-shield-halved text-[#4E6700]"></i> Garansi Bersih Higienis
                    </span>
                    <span class="flex items-center gap-1.5 font-bold text-[#1A1743]">
                        <i class="fa-solid fa-bolt text-[#4E6700]"></i> 1 Mesin 1 Pelanggan
                    </span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>