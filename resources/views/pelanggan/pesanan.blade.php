<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - Rodeo Laundry Malang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- TAMBAHAN: Google Fonts Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Paksa semua teks pakai font ini */
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
    <!-- ========================================= -->

    <script>
        window.pesananData = {
            orders: @json($orders),
        };
    </script>
</head>
<body class="bg-[#FAF8FF] text-[#1A1743] font-sans antialiased"
      x-data="{
        orders: window.pesananData.orders || [],
        get hasOrders() { return this.orders.length > 0; },
        formatRp(n) { return 'Rp ' + n.toLocaleString('id-ID'); },
        statusColor(status) {
            return {
                'pending':    { bg: 'bg-[#EAEDFF]', text: 'text-[#1A1743]', dot: 'bg-[#1A1743]' },
                'processing': { bg: 'bg-[#EAEDFF]', text: 'text-[#1A1743]', dot: 'bg-[#1A1743]' },
                'ready':      { bg: 'bg-[#C2F43B]', text: 'text-[#4E6700]', dot: 'bg-[#4E6700]' },
                'completed':  { bg: 'bg-[#C2F43B]', text: 'text-[#4E6700]', dot: 'bg-[#4E6700]' },
                'cancelled':  { bg: 'bg-red-100',   text: 'text-red-700',   dot: 'bg-red-500' },
            }[status] || { bg: 'bg-gray-100', text: 'text-gray-700', dot: 'bg-gray-500' };
        },
        stepIndex(status) {
            return { 'pending': 0, 'processing': 1, 'ready': 2, 'completed': 3, 'cancelled': -1 }[status] ?? 0;
        },
        async cancelOrder(id) {
            if (!confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')) return;
            const res = await fetch('/orders/' + id + '/cancel', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            window.location.reload();
        }
      }">

    <!-- ================= HEADER ================= -->
    <header class="fixed top-0 w-full h-16 md:h-20 bg-[#FAF8FF]/85 backdrop-blur-md shadow-sm z-50"
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
                <a href="{{ url('/') }}" class="px-4 py-1.5 text-sm font-semibold text-[#47464E] hover:text-[#1A1743] rounded-full transition">Beranda</a>
                <a href="{{ route('pemesanan') }}" class="px-4 py-1.5 text-sm font-semibold text-[#47464E] hover:text-[#1A1743] rounded-full transition">Layanan</a>
                <a href="{{ route('pesanan.index') }}" class="px-4 py-1.5 bg-[#EAEDFF] rounded-full text-sm font-bold text-[#1A1743]">Pesanan</a>
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
                    </div>
                @else
                    <a href="{{ route('login') }}" class="px-4 md:px-5 py-2 bg-[#1A1743] text-white rounded-full text-xs md:text-sm font-bold hover:bg-[#2F2D59] shadow-sm transition">
                        Masuk
                    </a>
                @endauth

                <!-- Hamburger -->
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
                <a href="{{ url('/') }}" class="flex items-center px-4 py-3 rounded-xl text-sm font-semibold text-[#47464E] hover:bg-[#F2F3FF] hover:text-[#1A1743] transition">
                    <i class="fa-solid fa-house w-5 text-center text-[#9895C8] mr-2"></i> Beranda
                </a>
                <a href="{{ route('pemesanan') }}" class="flex items-center px-4 py-3 rounded-xl text-sm font-semibold text-[#47464E] hover:bg-[#F2F3FF] hover:text-[#1A1743] transition">
                    <i class="fa-solid fa-layer-group w-5 text-center text-[#9895C8] mr-2"></i> Layanan
                </a>
                <a href="{{ route('pesanan.index') }}" class="flex items-center px-4 py-3 rounded-xl text-sm font-bold bg-[#EAEDFF] text-[#1A1743]">
                    <i class="fa-solid fa-receipt w-5 text-center text-[#1A1743] mr-2"></i> Pesanan
                </a>
                <a href="https://wa.me/6281234567890" target="_blank" class="flex items-center px-4 py-3 rounded-xl text-sm font-semibold text-[#47464E] hover:bg-[#F2F3FF] hover:text-[#1A1743] transition">
                    <i class="fa-brands fa-whatsapp w-5 text-center text-[#25D366] mr-2"></i> Hubungi WhatsApp
                </a>
            </div>
        </div>
    </header>

    <main class="pt-24 md:pt-28 max-w-[1440px] mx-auto px-4 md:px-6 pb-16">

        <!-- ================= HERO ================= -->
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6 md:mb-8">
            <div>
                <h1 class="text-2xl md:text-4xl lg:text-5xl font-extrabold leading-tight mb-2">Pesanan Saya</h1>
                <p class="text-xs md:text-sm text-[#47464E] max-w-lg">Pantau status cucian Anda secara real-time, mulai dari penjemputan sampai kembali ke lemari.</p>
            </div>
            <a href="{{ route('pemesanan') }}"
               class="inline-flex items-center gap-2 px-5 md:px-6 py-2.5 md:py-3 bg-[#1A1743] text-white font-bold rounded-full text-xs md:text-sm self-start md:self-end
                      transition-all duration-300 ease-out
                      hover:shadow-[0_15px_35px_-8px_rgba(26,23,67,0.5)]
                      hover:-translate-y-1">
                <i class="fa-solid fa-plus text-xs"></i> Pesanan Baru
            </a>
        </div>

        <!-- ================= LIST PESANAN ================= -->
        <div class="space-y-6">

            <template x-if="!hasOrders">
                <div class="text-center py-16 md:py-20 bg-white rounded-[24px] md:rounded-[32px] border border-dashed border-[#E2E7FF] p-6">
                    <div class="w-16 h-16 bg-[#F2F3FF] text-[#9895C8] rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <h3 class="text-base md:text-lg font-bold mb-1">Belum Ada Pesanan</h3>
                    <p class="text-xs md:text-sm text-[#47464E] mb-5">Yuk mulai laundry pertama Anda sekarang!</p>
                    <a href="{{ route('pemesanan') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#BFF138] text-[#131B2E] font-bold rounded-full text-xs md:text-sm
                              transition-all duration-300 hover:shadow-[0_15px_35px_rgba(204,255,70,0.5)]">
                        Mulai Pesan Layanan
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </template>

            <template x-for="order in orders" :key="order.id">
                <div class="bg-white rounded-[24px] md:rounded-[28px] border border-[#E2E7FF]/60 overflow-hidden shadow-sm
                            transition-all duration-300 hover:shadow-[0_15px_40px_-12px_rgba(26,23,67,0.12)]">

                    <!-- Header Kartu -->
                    <div class="px-4 md:px-8 pt-5 md:pt-6 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-[#E2E7FF]/60">
                        <div class="flex items-center gap-3 flex-wrap">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-receipt text-[#1A1743]"></i>
                                <span class="font-bold text-sm md:text-base text-[#1A1743]" x-text="'#' + order.id"></span>
                            </div>
                            <span class="text-[10px] md:text-xs text-[#47464E]" x-text="order.date"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-full text-[10px] md:text-xs font-bold flex items-center gap-1.5"
                                  :class="statusColor(order.status).bg + ' ' + statusColor(order.status).text">
                                <span class="w-1.5 h-1.5 rounded-full" :class="statusColor(order.status).dot"></span>
                                <span x-text="order.statusLabel"></span>
                            </span>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <template x-if="order.status !== 'cancelled'">
                        <div class="px-4 md:px-8 py-5 bg-[#FAF8FF]">
                            <div class="flex items-center justify-between relative">
                                <div class="absolute top-3 left-3 right-3 h-0.5 bg-[#E2E7FF]"></div>
                                <div class="absolute top-3 left-3 h-0.5 bg-[#BFF138] transition-all duration-500"
                                     :style="'width: calc(' + (stepIndex(order.status) / 3) * 100 + '% - ' + (stepIndex(order.status) === 0 ? '0px' : '24px') + ')'"></div>

                                <div class="flex flex-col items-center gap-2 z-10" style="min-width: 60px;">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center bg-[#BFF138] text-[#1A1743] text-[10px]">
                                        <i class="fa-solid fa-clock"></i>
                                    </div>
                                    <span class="text-[10px] md:text-xs font-bold text-[#1A1743]">Diterima</span>
                                </div>

                                <div class="flex flex-col items-center gap-2 z-10" style="min-width: 60px;">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center transition-colors text-[10px]"
                                         :class="stepIndex(order.status) >= 1 ? 'bg-[#BFF138] text-[#1A1743]' : 'bg-white border-2 border-[#E2E7FF] text-transparent'">
                                        <i class="fa-solid fa-gear" :class="stepIndex(order.status) === 1 ? 'fa-spin' : ''"></i>
                                    </div>
                                    <span class="text-[10px] md:text-xs font-bold" :class="stepIndex(order.status) >= 1 ? 'text-[#1A1743]' : 'text-[#9895C8]'">Dicuci</span>
                                </div>

                                <div class="flex flex-col items-center gap-2 z-10" style="min-width: 60px;">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center transition-colors text-[10px]"
                                         :class="stepIndex(order.status) >= 2 ? 'bg-[#BFF138] text-[#1A1743]' : 'bg-white border-2 border-[#E2E7FF] text-transparent'">
                                        <i class="fa-solid fa-truck-fast"></i>
                                    </div>
                                    <span class="text-[10px] md:text-xs font-bold" :class="stepIndex(order.status) >= 2 ? 'text-[#1A1743]' : 'text-[#9895C8]'">Diantar</span>
                                </div>

                                <div class="flex flex-col items-center gap-2 z-10" style="min-width: 60px;">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center transition-colors text-[10px]"
                                         :class="stepIndex(order.status) >= 3 ? 'bg-[#BFF138] text-[#1A1743]' : 'bg-white border-2 border-[#E2E7FF] text-transparent'">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                    <span class="text-[10px] md:text-xs font-bold" :class="stepIndex(order.status) >= 3 ? 'text-[#1A1743]' : 'text-[#9895C8]'">Selesai</span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Info Dibatalkan -->
                    <template x-if="order.status === 'cancelled'">
                        <div class="px-4 md:px-8 py-3 bg-red-50 border-b border-red-100 flex items-center gap-2 text-xs text-red-700 font-semibold">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>Pesanan ini telah dibatalkan.</span>
                        </div>
                    </template>

                    <!-- Body Kartu -->
                    <div class="px-4 md:px-8 py-5 grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-[#47464E] mb-3">Layanan Dipesan</h4>
                            <div class="space-y-2">
                                <template x-for="item in order.items" :key="item.name">
                                    <div class="flex justify-between items-start text-xs md:text-sm">
                                        <div class="flex-1 min-w-0 pr-3">
                                            <p class="font-bold text-[#1A1743] truncate" x-text="item.name"></p>
                                            <p class="text-[10px] md:text-xs text-[#47464E]" x-text="item.qty + ' ' + item.unit"></p>
                                        </div>
                                        <span class="font-semibold text-[#1A1743] shrink-0" x-text="formatRp(item.price)"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-[#47464E] mb-3">Alamat / Lokasi</h4>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div class="flex items-start gap-2 min-w-0 flex-1">
                                    <i class="fa-solid fa-location-dot text-[#1A1743] mt-0.5 shrink-0 text-sm"></i>
                                    <p class="text-xs md:text-sm text-[#47464E]" x-text="order.address"></p>
                                </div>
                                <template x-if="order.address && (order.address.toLowerCase().includes('outlet') || order.address.toLowerCase().includes('rodeo') || order.address.toLowerCase().includes('ambil'))">
                                    <div class="shrink-0 pl-6 sm:pl-0">
                                        <a href="https://maps.app.goo.gl/VNZ24TUTkq7HTJbp9" 
                                           target="_blank" 
                                           rel="noopener noreferrer"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#EAEDFF] hover:bg-[#dfe4ff] text-[#1A1743] rounded-full text-xs font-bold transition shadow-2xs">
                                            <i class="fa-solid fa-diamond-turn-right text-emerald-600 text-xs"></i>
                                            <span>Buka Maps</span>
                                            <i class="fa-solid fa-arrow-up-right-from-square text-[9px] text-[#717082]"></i>
                                        </a>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Kartu -->
                    <div class="px-4 md:px-8 py-4 bg-[#FAF8FF] border-t border-[#E2E7FF]/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <span class="text-[10px] md:text-xs text-[#47464E]">Total Pembayaran</span>
                            <div class="text-lg md:text-xl font-extrabold text-[#1A1743]" x-text="formatRp(order.total)"></div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <template x-if="order.canCancel">
                                <button @click="cancelOrder(order.raw_id)" class="px-3 md:px-4 py-2 bg-white border border-red-200 hover:border-red-400 hover:bg-red-50 text-red-600 rounded-full text-xs font-bold transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-xmark"></i>
                                    Batalkan Pesanan
                                </button>
                            </template>

                            <a href="https://wa.me/6281234567890?text=Halo%20Admin%20Rodeo%20Laundry,%20saya%20ingin%20menanyakan%20pesanan%20nomor%20"
                               target="_blank"
                               class="px-3 md:px-4 py-2 bg-[#EAEDFF] hover:bg-[#dfe4ff] text-[#1A1743] rounded-full text-xs font-bold transition flex items-center gap-1.5">
                                <i class="fa-brands fa-whatsapp text-[#25D366]"></i>
                                Tanya Admin
                            </a>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </main>

    <!-- ================= FOOTER ================= -->
    <footer class="bg-white border-t border-[#E2E7FF]/70 mt-12 md:mt-16">
        <div class="max-w-[1440px] mx-auto px-4 md:px-6 py-12 md:py-16">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 mb-10 md:mb-12">
                <div>
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-9 h-9 md:w-10 md:h-10 bg-[#2F2D59] rounded-xl flex items-center justify-center shadow-lg shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#bff108ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h3"/><path d="M17 6h.01"/><rect width="18" height="20" x="3" y="2" rx="2"/><circle cx="12" cy="13" r="5"/><path d="M12 18a2.5 2.5 0 0 0 0-5 2.5 2.5 0 0 1 0-5"/></svg>
                        </div>
                        <span class="text-lg md:text-xl font-bold tracking-tight text-[#1A1743]">Rodeo Laundry</span>
                    </div>
                    <p class="text-[#47464E] text-xs md:text-sm mb-5 leading-relaxed">
                        Laundry profesional, cepat, bersih, dan wangi di Malang & Batu. Layanan antar-jemput ekspres untuk kenyamanan pakaian higienis Anda.
                    </p>
                </div>

                <div>
                    <h4 class="font-bold text-xs md:text-sm uppercase tracking-wider mb-3 md:mb-4 text-[#1A1743]">Sitemap</h4>
                    <ul class="space-y-2 text-xs md:text-sm text-[#47464E]">
                        <li><a href="{{ url('/') }}" class="hover:text-[#1A1743] transition">Beranda</a></li>
                        <li><a href="{{ route('pemesanan') }}" class="hover:text-[#1A1743] transition">Layanan</a></li>
                        <li><a href="{{ route('pesanan.index') }}" class="hover:text-[#1A1743] transition">Pesanan Saya</a></li>
                        <li><a href="#" class="hover:text-[#1A1743] transition">Pertanyaan Umum (FAQ)</a></li>
                        <li><a href="#" class="hover:text-[#1A1743] transition">Syarat & Ketentuan</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-xs md:text-sm uppercase tracking-wider mb-3 md:mb-4 text-[#1A1743]">Layanan Kami</h4>
                    <ul class="space-y-2 text-xs md:text-sm text-[#47464E]">
                        <li><a href="{{ route('pemesanan') }}" class="hover:text-[#1A1743] transition">Cuci Setrika Kiloan</a></li>
                        <li><a href="{{ route('pemesanan') }}" class="hover:text-[#1A1743] transition">Cuci Gorden</a></li>
                        <li><a href="{{ route('pemesanan') }}" class="hover:text-[#1A1743] transition">Cuci Boneka & Bantal</a></li>
                        <li><a href="{{ route('pemesanan') }}" class="hover:text-[#1A1743] transition">Cuci Sepatu Premium</a></li>
                        <li><a href="{{ route('pemesanan') }}" class="hover:text-[#1A1743] transition">Cuci Tas & Ransel</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-xs md:text-sm uppercase tracking-wider mb-3 md:mb-4 text-[#1A1743]">Kontak & Area</h4>
                    <div class="space-y-2.5 text-xs md:text-sm text-[#47464E]">
                        <p class="flex items-start gap-2.5">
                            <i class="fa-solid fa-location-dot text-[#9895C8] mt-1 shrink-0"></i>
                            <span>Gg. Rodeo, RT.04/RW.10, Sumberejo, Kec. Batu, Kota Batu, Jawa Timur 65318</span>
                        </p>
                        <p class="flex items-center gap-2.5">
                            <i class="fa-regular fa-clock text-[#9895C8] shrink-0"></i>
                            <span>Buka Setiap Hari: 07.00 - 21.00 WIB</span>
                        </p>
                        <p class="flex items-center gap-2.5">
                            <i class="fa-brands fa-whatsapp text-[#25D366] shrink-0"></i>
                            <span>WhatsApp: +62 813-5805-5257</span>
                        </p>
                        <p class="flex items-start gap-2.5 mt-3 pt-3 border-t border-[#E2E7FF]/60">
                            <i class="fa-solid fa-truck-fast text-[#9895C8] mt-1 shrink-0"></i>
                            <span class="font-semibold text-[#1A1743]">Area Penjemputan:</span>
                        </p>
                        <p class="text-xs">Khusus melayani Area Kota Batu dan sekitarnya.</p>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-[#E2E7FF]/60 flex flex-col md:flex-row justify-between items-center gap-3 md:gap-4 text-[10px] md:text-xs text-[#47464E]">
                <p class="text-center md:text-left">© 2025 Rodeo Laundry. Hak Cipta Dilindungi Undang-Undang.</p>
                <div class="flex gap-4 sm:gap-6 flex-wrap justify-center">
                    <span class="flex items-center gap-1.5 font-bold text-[#1A1743]">
                        <i class="fa-solid fa-shield-halved text-[#4E6700]"></i> Garansi Bersih Higienis
                    </span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>