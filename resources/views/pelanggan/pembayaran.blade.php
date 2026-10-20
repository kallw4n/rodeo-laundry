<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Rodeo Laundry Malang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script>
    window.pembayaranData = {
        services: @json($services),
        kiloanService: @json($kiloanService),
    };
    </script>
</head>
<body class="bg-[#FAF8FF] text-[#1A1743] font-sans antialiased"
      x-data="{
        ...window.pembayaranData,

        kiloan: $persist(0).as('cart_kiloan'),
        serviceQty: $persist({}).as('cart_service_qty'),
        notes: $persist('').as('cart_notes'),
        courierNotes: $persist('').as('cart_courier_notes'),
        pickupMethod: $persist('courier').as('cart_pickup'),
        cartAddress: $persist('').as('cart_address'),

        summaryOpen: true,
        paymentMethod: 'qris',
        submitting: false,
        error: '',

        async checkout() {
            this.submitting = true;
            this.error = '';
            const items = this.selectedServices.map(s => ({ id: s.id, qty: this.serviceQty[s.id] }));
            if (this.kiloan > 0 && this.kiloanService) items.unshift({ id: this.kiloanService.id, qty: this.kiloan });
            const res = await fetch('{{ route('pesan.store') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    items,
                    payment_method: this.paymentMethod,
                    pickup: this.pickupMethod,
                    address: this.cartAddress,
                    notes: [this.notes, this.courierNotes].filter(Boolean).join(' / '),
                }),
            });
            if (!res.ok) { this.error = 'Gagal membuat pesanan, silakan coba lagi.'; this.submitting = false; return; }
            ['cart_kiloan', 'cart_service_qty', 'cart_notes', 'cart_courier_notes', 'cart_pickup', 'cart_address'].forEach(k => localStorage.removeItem('_x_' + k));
            window.location = (await res.json()).redirect;
        },

        get hargaKiloan() { return this.kiloanService?.price ?? 0; },
        get kiloanName() { return this.kiloanService?.name ?? 'Cuci Kiloan'; },
        getService(id) { return this.services.find(s => s.id === id); },

        getServiceIcon(name = '') {
            const n = (name || '').toLowerCase();
            if (n.includes('kiloan') || n.includes('kilo')) return 'fa-solid fa-weight-hanging';
            if (n.includes('sepatu')) return 'fa-solid fa-shoe-prints';
            if (n.includes('setrika') && !n.includes('cuci')) return 'fa-solid fa-wand-magic-sparkles';
            if (n.includes('gorden')) return 'fa-solid fa-border-none';
            if (n.includes('bed') || n.includes('bantal') || n.includes('boneka')) return 'fa-solid fa-mattress-pillow';
            if (n.includes('tas') || n.includes('ransel')) return 'fa-solid fa-bag-shopping';
            if (n.includes('jas') || n.includes('kering')) return 'fa-solid fa-shirt';
            return 'fa-solid fa-jug-detergent';
        },

        get selectedServices() {
            return this.services.filter(s => (this.serviceQty[s.id] || 0) > 0);
        },
        get totalBayar() {
            let t = this.kiloan * this.hargaKiloan;
            this.services.forEach(s => t += s.price * (this.serviceQty[s.id] || 0));
            return t;
        },
        get totalItems() {
            let count = this.kiloan > 0 ? 1 : 0;
            this.services.forEach(s => { count += (this.serviceQty[s.id] || 0); });
            return count;
        },
        get hasAnySelected() { return this.kiloan > 0 || this.selectedServices.length > 0; },
        formatRp(n) { return 'Rp ' + n.toLocaleString('id-ID'); }
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
                <a href="{{ url('/') }}" class="px-4 py-1.5 text-sm font-semibold text-[#47464E] hover:text-[#1A1743] rounded-full">Beranda</a>
                <a href="{{ route('pemesanan') }}" class="px-4 py-1.5 bg-[#EAEDFF] rounded-full text-sm font-bold text-[#1A1743]">Layanan</a>
                <a href="{{ route('pesanan.index') }}" class="px-4 py-1.5 text-sm font-semibold text-[#47464E] hover:text-[#1A1743]">Pesanan</a>
            </nav>

            <!-- Right Side -->
            <div class="flex items-center gap-2 md:gap-3">
                <a href="https://wa.me/6281234567890" target="_blank" class="hidden sm:flex items-center gap-2 px-3 md:px-4 py-2 bg-[#EAEDFF] hover:bg-[#dfe4ff] rounded-full text-xs md:text-sm font-semibold text-[#131B2E] transition">
                    <i class="fa-brands fa-whatsapp text-sm md:text-base text-[#25D366]"></i> WhatsApp
                </a>

                <div class="w-8 h-8 md:w-9 md:h-9 bg-[#1A1743] rounded-full flex items-center justify-center text-white shadow-sm">
                    <i class="fa-regular fa-user text-xs md:text-sm"></i>
                </div>

                <!-- Hamburger Button (Mobile only) -->
                <button @click="mobileOpen = !mobileOpen"
                        class="md:hidden w-9 h-9 flex items-center justify-center rounded-full bg-[#F2F3FF] hover:bg-[#EAEDFF] transition">
                    <svg x-show="!mobileOpen" class="w-5 h-5 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileOpen" class="w-5 h-5 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Dropdown Menu Mobile -->
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
                <a href="{{ route('pemesanan') }}" class="flex items-center px-4 py-3 rounded-xl text-sm font-bold bg-[#EAEDFF] text-[#1A1743]">
                    <i class="fa-solid fa-layer-group w-5 text-center text-[#1A1743] mr-2"></i> Layanan
                </a>
                <a href="{{ route('pesanan.index') }}" class="flex items-center px-4 py-3 rounded-xl text-sm font-semibold text-[#47464E] hover:bg-[#F2F3FF] hover:text-[#1A1743] transition">
                    <i class="fa-solid fa-receipt w-5 text-center text-[#9895C8] mr-2"></i> Pesanan
                </a>
                <a href="https://wa.me/6281234567890" target="_blank" class="flex items-center px-4 py-3 rounded-xl text-sm font-semibold text-[#47464E] hover:bg-[#F2F3FF] hover:text-[#1A1743] transition">
                    <i class="fa-brands fa-whatsapp w-5 text-center text-[#25D366] mr-2"></i> Hubungi WhatsApp
                </a>
            </div>
        </div>
    </header>

    <!-- ================= MAIN ================= -->
    <main class="pt-24 md:pt-28 max-w-[1440px] mx-auto px-4 md:px-6 pb-16">

        <!-- ================= HERO + STEPPER ================= -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 md:gap-6 mb-6 md:mb-8">
            <div>
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-[#C2F43B]/50 text-[#4E6700] text-[10px] md:text-xs font-bold uppercase rounded-full mb-3">
                    <i class="fa-solid fa-bolt text-[#4E6700] text-[10px]"></i> Layanan On-Demand Malang
                </span>
                <h1 class="text-2xl md:text-4xl lg:text-5xl font-extrabold leading-tight mb-2 md:mb-3">Pembayaran</h1>
                <p class="text-xs md:text-base text-[#47464E] max-w-lg">Selesaikan pembayaran pesanan Anda dengan metode instan &amp; aman. Pakaian siap dijemput dan diproses higienis.</p>
            </div>

            <!-- Stepper -->
            <div class="flex items-center gap-1.5 md:gap-3 bg-white p-1.5 md:p-2 rounded-full shadow-sm border border-[#E2E7FF]/60 self-start md:self-auto">
                <a href="{{ route('pemesanan') }}" class="flex items-center gap-1.5 md:gap-2 px-2 md:px-3 py-1.5 md:py-2 rounded-full hover:bg-[#F2F3FF] transition">
                    <span class="w-5 h-5 rounded-full bg-[#1A1743] text-white text-[10px] font-bold flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-check text-[10px]"></i>
                    </span>
                    <span class="hidden md:inline text-sm font-semibold text-[#47464E]">Pilih Layanan</span>
                </a>
                <div class="w-3 md:w-6 h-px bg-[#E2E7FF]"></div>
                <a href="{{ route('pengiriman') }}" class="flex items-center gap-1.5 md:gap-2 px-2 md:px-3 py-1.5 md:py-2 rounded-full hover:bg-[#F2F3FF] transition">
                    <span class="w-5 h-5 rounded-full bg-[#1A1743] text-white text-[10px] font-bold flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-check text-[10px]"></i>
                    </span>
                    <span class="hidden md:inline text-sm font-semibold text-[#47464E]">Pengiriman</span>
                </a>
                <div class="w-3 md:w-6 h-px bg-[#E2E7FF]"></div>
                <div class="flex items-center gap-1.5 md:gap-2 px-2.5 md:px-4 py-1.5 md:py-2 bg-[#1A1743] text-white rounded-full shadow-sm">
                    <span class="w-5 h-5 rounded-full bg-[#BFF138] text-[#131B2E] text-[11px] font-extrabold flex items-center justify-center shrink-0">3</span>
                    <span class="text-xs md:text-sm font-bold">Pembayaran</span>
                </div>
            </div>
        </div>

        <!-- ================= MAIN CARD ================= -->
        <div class="bg-white rounded-[24px] md:rounded-[32px] p-4 md:p-8 lg:p-10 shadow-sm border border-[#E2E7FF]/60">

            <!-- ====== RINGKASAN PESANAN ====== -->
            <div class="border border-[#E2E7FF] rounded-2xl md:rounded-3xl overflow-hidden mb-6 md:mb-8">
                <button @click="summaryOpen = !summaryOpen"
                        class="w-full px-4 md:px-6 py-3.5 md:py-4 flex items-center justify-between hover:bg-[#FAF8FF] transition gap-2">
                    <div class="flex items-center gap-2 md:gap-3 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-[#EAEDFF] text-[#1A1743] flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-receipt text-sm"></i>
                        </div>
                        <span class="font-bold text-[#1A1743] text-sm md:text-base truncate">Ringkasan Pesanan</span>
                        <span class="px-2 py-0.5 bg-[#EAEDFF] rounded-full text-[10px] md:text-xs font-bold text-[#1A1743] shrink-0"
                              x-text="totalItems + ' Layanan'"></span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-xs text-[#47464E] transition-transform duration-300 shrink-0"
                       :class="summaryOpen ? 'rotate-180' : 'rotate-0'"></i>
                </button>

                <div x-show="summaryOpen" x-collapse
                     class="border-t border-[#E2E7FF] px-4 md:px-6 py-4 md:py-5">

                    <div class="space-y-3 md:space-y-4 mb-4">
                        <!-- Cuci Kiloan -->
                        <template x-if="kiloan > 0">
                            <div class="flex justify-between items-start gap-3">
                                <div class="flex items-start gap-2.5 flex-1 min-w-0">
                                    <div class="w-7 h-7 rounded-lg bg-[#FAF8FF] border border-[#E2E7FF] flex items-center justify-center text-[#1A1743] shrink-0 mt-0.5">
                                        <i class="fa-solid fa-weight-hanging text-xs"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-bold text-[#1A1743] text-sm md:text-base" x-text="kiloanName"></span>
                                            <span class="text-xs md:text-sm text-[#47464E]" x-text="'(' + kiloan + ' kg)'"></span>
                                        </div>
                                        <template x-if="notes && notes.trim().length > 0">
                                            <p class="text-xs text-[#47464E] mt-1 pl-3 border-l-2 border-[#BFF138]"
                                               x-text="'↳ Catatan: ' + notes"></p>
                                        </template>
                                    </div>
                                </div>
                                <div class="text-right font-bold text-[#1A1743] text-sm md:text-base shrink-0"
                                     x-text="formatRp(kiloan * hargaKiloan)"></div>
                            </div>
                        </template>

                        <!-- Layanan Satuan -->
                        <template x-for="service in selectedServices" :key="'sum-' + service.id">
                            <div class="flex justify-between items-start gap-3">
                                <div class="flex items-start gap-2.5 flex-1 min-w-0">
                                    <div class="w-7 h-7 rounded-lg bg-[#FAF8FF] border border-[#E2E7FF] flex items-center justify-center text-[#1A1743] shrink-0 mt-0.5">
                                        <i :class="getServiceIcon(service.name)" class="text-xs"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-bold text-[#1A1743] text-sm md:text-base" x-text="service.name"></span>
                                            <span class="text-xs md:text-sm text-[#47464E]"
                                                  x-text="'(' + (serviceQty[service.id] || 0) + ' ' + service.unit + ')'"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right font-bold text-[#1A1743] text-sm md:text-base shrink-0"
                                     x-text="formatRp(service.price * (serviceQty[service.id] || 0))"></div>
                            </div>
                        </template>

                        <template x-if="!hasAnySelected">
                            <p class="text-sm text-[#47464E] italic py-2">Belum ada layanan yang dipilih. <a href="{{ route('pemesanan') }}" class="text-[#1A1743] font-bold underline">Pilih sekarang →</a></p>
                        </template>
                    </div>

                    <!-- Ringkasan Info Pengiriman jika ada -->
                    <template x-if="cartAddress || pickupMethod">
                        <div class="mt-4 pt-3 border-t border-[#E2E7FF]/70 bg-[#FAF8FF] p-3 md:p-4 rounded-xl border border-[#E2E7FF]/60 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-[#EAEDFF] text-[#1A1743] flex items-center justify-center shrink-0 mt-0.5">
                                <i class="fa-solid fa-truck text-xs"></i>
                            </div>
                            <div class="min-w-0 flex-1 text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-[#1A1743]" x-text="pickupMethod === 'courier' ? 'Dijemput &amp; Diantar Kurir Rodeo' : 'Drop &amp; Ambil di Outlet Soekarno-Hatta'"></span>
                                    <span class="px-2 py-0.5 bg-[#C2F43B] text-[#4E6700] rounded-full text-[10px] font-bold">Gratis Ongkir</span>
                                </div>
                                <p class="text-[#47464E] mt-1 text-[11px]" x-show="cartAddress" x-text="'Alamat: ' + cartAddress"></p>
                                <p class="text-[#47464E] mt-0.5 text-[11px]" x-show="courierNotes" x-text="'Catatan Kurir: ' + courierNotes"></p>
                            </div>
                        </div>
                    </template>

                    <!-- Total Baris -->
                    <div class="border-t border-[#E2E7FF] pt-4 mt-4 flex justify-between items-center gap-3">
                        <span class="text-base md:text-lg font-bold text-[#1A1743]">Total Pembayaran</span>
                        <span class="text-xl md:text-2xl font-extrabold text-[#1A1743] shrink-0" x-text="formatRp(totalBayar)"></span>
                    </div>
                </div>
            </div>

            <!-- ====== METODE PEMBAYARAN ====== -->
            <div class="flex items-center justify-between mb-4 md:mb-5 gap-2">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-[#BFF138] rounded-full"></span>
                    <h2 class="text-base md:text-lg font-bold">Metode Pembayaran</h2>
                </div>
                <span class="text-[10px] md:text-xs text-[#47464E] flex items-center gap-1">
                    <i class="fa-solid fa-shield-halved text-[#4E6700]"></i> Diproses Instan &amp; Aman
                </span>
            </div>

            <div class="space-y-3">

                <!-- 1. QRIS -->
                <label class="cursor-pointer block">
                    <input type="radio" name="payment" value="qris" class="peer sr-only" x-model="paymentMethod">
                    <div class="p-4 md:p-5 rounded-2xl border-2 transition-all border-[#E2E7FF] bg-white peer-checked:border-[#BFF138] peer-checked:bg-[#FAF8FF]">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 md:gap-4 flex-1 min-w-0">
                                <div class="w-5 h-5 rounded-full border-2 border-[#E2E7FF] flex items-center justify-center shrink-0 mt-1 peer-checked:border-[#1A1743] peer-checked:bg-[#1A1743]">
                                    <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                                </div>
                                <div class="w-10 h-10 md:w-11 md:h-11 bg-[#1A1743] text-[#BFF138] rounded-xl flex items-center justify-center shrink-0 shadow-sm">
                                    <i class="fa-solid fa-qrcode text-lg"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap mb-1">
                                        <span class="font-bold text-[#1A1743] text-sm md:text-base">QRIS (Semua E-Wallet &amp; M-Banking)</span>
                                        <span class="px-2 py-0.5 bg-[#C2F43B] rounded-full text-[10px] font-bold text-[#4E6700]">Otomatis</span>
                                    </div>
                                    <p class="text-xs text-[#47464E]">BCA Mobile, GoPay, OVO, ShopeePay, DANA, Livin' by Mandiri, LinkAja</p>

                                    <template x-if="paymentMethod === 'qris'">
                                        <div class="mt-3 md:mt-4 p-3 md:p-4 bg-white rounded-xl border border-[#E2E7FF] flex items-start gap-3">
                                            <div class="w-9 h-9 md:w-10 md:h-10 bg-[#FAF8FF] border border-[#E2E7FF] rounded-lg flex items-center justify-center text-[#1A1743] shrink-0">
                                                <i class="fa-solid fa-bolt text-[#BFF138] text-base"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-xs md:text-sm font-bold text-[#1A1743] mb-0.5">Kode QR Dinamis Otomatis</p>
                                                <p class="text-[10px] md:text-xs text-[#47464E]">Kode QRIS resmi akan terbit otomatis di layar setelah Anda menekan tombol konfirmasi di bawah.</p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <span class="hidden sm:inline-flex items-center gap-1 px-3 py-1 border border-[#E2E7FF] rounded-lg text-xs font-bold text-[#1A1743] shrink-0 bg-white">
                                <i class="fa-solid fa-qrcode text-xs"></i> QRIS
                            </span>
                        </div>
                    </div>
                </label>

                <!-- 2. Transfer Bank / VA -->
                <label class="cursor-pointer block">
                    <input type="radio" name="payment" value="va" class="peer sr-only" x-model="paymentMethod">
                    <div class="p-4 md:p-5 rounded-2xl border-2 transition-all border-[#E2E7FF] bg-white peer-checked:border-[#BFF138] peer-checked:bg-[#FAF8FF]">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 md:gap-4 flex-1 min-w-0">
                                <div class="w-5 h-5 rounded-full border-2 border-[#E2E7FF] flex items-center justify-center shrink-0 mt-1 peer-checked:border-[#1A1743] peer-checked:bg-[#1A1743]">
                                    <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                                </div>
                                <div class="w-10 h-10 md:w-11 md:h-11 bg-[#EAEDFF] text-[#1A1743] rounded-xl flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-building-columns text-base md:text-lg"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap mb-1">
                                        <p class="font-bold text-[#1A1743] text-sm md:text-base">Transfer Bank / Virtual Account</p>
                                        <span class="px-2 py-0.5 bg-[#EAEDFF] rounded-full text-[10px] font-bold text-[#1A1743]">Bebas Biaya</span>
                                    </div>
                                    <p class="text-xs text-[#47464E]">BCA Virtual Account, Mandiri, BNI, BRI, Permata Bank</p>

                                    <template x-if="paymentMethod === 'va'">
                                        <div class="mt-3 md:mt-4 p-3 md:p-4 bg-white rounded-xl border border-[#E2E7FF] flex items-start gap-3">
                                            <div class="w-9 h-9 md:w-10 md:h-10 bg-[#FAF8FF] border border-[#E2E7FF] rounded-lg flex items-center justify-center text-[#1A1743] shrink-0">
                                                <i class="fa-solid fa-credit-card text-[#1A1743] text-sm"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-xs md:text-sm font-bold text-[#1A1743] mb-0.5">Nomor Virtual Account Instan</p>
                                                <p class="text-[10px] md:text-xs text-[#47464E]">Nomor VA unik akan langsung dibuatkan dan dicek otomatis tanpa perlu upload bukti transfer manual.</p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <span class="hidden sm:inline-flex items-center gap-1 px-3 py-1 border border-[#E2E7FF] rounded-lg text-xs font-bold text-[#1A1743] shrink-0 bg-white">
                                <i class="fa-solid fa-building-columns text-xs"></i> VA Bank
                            </span>
                        </div>
                    </div>
                </label>

                <!-- 3. E-Wallet -->
                <label class="cursor-pointer block">
                    <input type="radio" name="payment" value="ewallet" class="peer sr-only" x-model="paymentMethod">
                    <div class="p-4 md:p-5 rounded-2xl border-2 transition-all border-[#E2E7FF] bg-white peer-checked:border-[#BFF138] peer-checked:bg-[#FAF8FF]">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 md:gap-4 flex-1 min-w-0">
                                <div class="w-5 h-5 rounded-full border-2 border-[#E2E7FF] flex items-center justify-center shrink-0 mt-1 peer-checked:border-[#1A1743] peer-checked:bg-[#1A1743]">
                                    <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                                </div>
                                <div class="w-10 h-10 md:w-11 md:h-11 bg-[#EAEDFF] text-[#1A1743] rounded-xl flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-wallet text-base md:text-lg"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap mb-1">
                                        <p class="font-bold text-[#1A1743] text-sm md:text-base">E-Wallet Direct Debit</p>
                                        <span class="px-2 py-0.5 bg-[#EAEDFF] rounded-full text-[10px] font-bold text-[#1A1743]">Instan</span>
                                    </div>
                                    <p class="text-xs text-[#47464E]">Notifikasi langsung ke aplikasi GoPay, ShopeePay, atau OVO</p>

                                    <template x-if="paymentMethod === 'ewallet'">
                                        <div class="mt-3 md:mt-4 p-3 md:p-4 bg-white rounded-xl border border-[#E2E7FF] flex items-start gap-3">
                                            <div class="w-9 h-9 md:w-10 md:h-10 bg-[#FAF8FF] border border-[#E2E7FF] rounded-lg flex items-center justify-center text-[#1A1743] shrink-0">
                                                <i class="fa-solid fa-mobile-screen-button text-[#1A1743] text-sm"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-xs md:text-sm font-bold text-[#1A1743] mb-0.5">Notifikasi Push Aplikasi</p>
                                                <p class="text-[10px] md:text-xs text-[#47464E]">Permintaan pembayaran akan langsung dikirim ke ponsel Anda untuk konfirmasi 1-klik yang mudah.</p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <span class="hidden sm:inline-flex items-center gap-1 px-3 py-1 border border-[#E2E7FF] rounded-lg text-xs font-bold text-[#1A1743] shrink-0 bg-white">
                                <i class="fa-solid fa-wallet text-xs"></i> E-Wallet
                            </span>
                        </div>
                    </div>
                </label>

                <!-- 4. COD -->
                <label class="cursor-pointer block">
                    <input type="radio" name="payment" value="cod" class="peer sr-only" x-model="paymentMethod">
                    <div class="p-4 md:p-5 rounded-2xl border-2 transition-all border-[#E2E7FF] bg-white peer-checked:border-[#BFF138] peer-checked:bg-[#FAF8FF]">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 md:gap-4 flex-1 min-w-0">
                                <div class="w-5 h-5 rounded-full border-2 border-[#E2E7FF] flex items-center justify-center shrink-0 mt-1 peer-checked:border-[#1A1743] peer-checked:bg-[#1A1743]">
                                    <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                                </div>
                                <div class="w-10 h-10 md:w-11 md:h-11 bg-[#EAEDFF] text-[#1A1743] rounded-xl flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-hand-holding-dollar text-base md:text-lg"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap mb-1">
                                        <p class="font-bold text-[#1A1743] text-sm md:text-base">Bayar di Tempat (COD / Tunai)</p>
                                        <span class="px-2 py-0.5 bg-[#C2F43B] rounded-full text-[10px] font-bold text-[#4E6700]">Praktis</span>
                                    </div>
                                    <p class="text-xs text-[#47464E]">Bayar tunai atau QR saat kurir mengantar pakaian bersih ke rumah Anda</p>

                                    <template x-if="paymentMethod === 'cod'">
                                        <div class="mt-3 md:mt-4 p-3 md:p-4 bg-white rounded-xl border border-[#E2E7FF] flex items-start gap-3">
                                            <div class="w-9 h-9 md:w-10 md:h-10 bg-[#FAF8FF] border border-[#E2E7FF] rounded-lg flex items-center justify-center text-[#1A1743] shrink-0">
                                                <i class="fa-solid fa-handshake text-[#4E6700] text-sm"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-xs md:text-sm font-bold text-[#1A1743] mb-0.5">Bayar Saat Pakaian Selesai</p>
                                                <p class="text-[10px] md:text-xs text-[#47464E]">Pakaian dicuci terlebih dahulu. Pembayaran diserahkan ke kurir saat pengantaran selesai.</p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <span class="hidden sm:inline-flex items-center gap-1 px-3 py-1 border border-[#E2E7FF] rounded-lg text-xs font-bold text-[#1A1743] shrink-0 bg-white">
                                <i class="fa-solid fa-hand-holding-dollar text-xs"></i> COD
                            </span>
                        </div>
                    </div>
                </label>
            </div>

            <!-- ====== TOMBOL KONFIRMASI & BAYAR ====== -->
            <p x-show="error" class="mt-4 text-sm font-semibold text-red-600 flex items-center gap-1.5">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span x-text="error"></span>
            </p>

            <button @click="checkout()" 
                    :disabled="!hasAnySelected || submitting" 
                    class="disabled:opacity-50 disabled:cursor-not-allowed w-full mt-6 md:mt-8 px-5 md:px-8 py-4 bg-[#BFF138] text-[#131B2E] font-bold rounded-2xl
                           flex flex-col sm:flex-row items-center justify-between gap-2 sm:gap-0
                           transition-all duration-300 ease-out
                           hover:shadow-[0_15px_35px_rgba(204,255,70,0.65)]
                           hover:-translate-y-1
                           hover:brightness-110">
                <span class="text-sm md:text-base flex items-center gap-2">
                    <template x-if="submitting">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin"></i>
                            Memproses Pesanan...
                        </span>
                    </template>
                    <template x-if="!submitting">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-lock text-sm"></i>
                            Konfirmasi &amp; Bayar Sekarang
                        </span>
                    </template>
                </span>
                <span class="flex items-center gap-2" x-show="!submitting">
                    <span class="text-base md:text-lg font-extrabold" x-text="formatRp(totalBayar)"></span>
                    <i class="fa-solid fa-arrow-right"></i>
                </span>
            </button>

            <!-- Reassurance Badges -->
            <div class="mt-4 flex flex-wrap items-center justify-center gap-4 text-[10px] md:text-xs text-[#47464E]">
                <span class="flex items-center gap-1"><i class="fa-solid fa-shield-halved text-[#4E6700]"></i> Transaksi Enkripsi 256-bit</span>
                <span class="flex items-center gap-1"><i class="fa-solid fa-rotate-left text-[#4E6700]"></i> Garansi Layanan 100%</span>
                <span class="flex items-center gap-1"><i class="fa-solid fa-headset text-[#4E6700]"></i> Bantuan CS WhatsApp 24/7</span>
            </div>

        </div>
    </main>

    <!-- ================= FOOTER ================= -->
    <footer class="bg-white border-t border-gray-100 mt-16">
        <div class="max-w-[1440px] mx-auto px-4 md:px-6 py-12 md:py-16">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 mb-10 md:mb-12">
                <div class="sm:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-9 h-9 bg-[#2F2D59] rounded-xl flex items-center justify-center text-[#BFF108]">
                            <i class="fa-solid fa-soap text-lg"></i>
                        </div>
                        <span class="text-lg md:text-xl font-bold">Rodeo Laundry</span>
                    </div>
                    <p class="text-[#47464E] text-xs md:text-sm mb-4 max-w-sm">Laundry profesional, cepat, bersih, dan wangi di Malang. Layanan antar-jemput ekspres untuk kenyamanan pakaian higienis Anda setiap hari.</p>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#C2F43B] rounded-full text-[10px] md:text-xs font-bold text-[#151F00]">
                        <i class="fa-solid fa-location-dot text-[#4E6700] text-[10px]"></i> Outlet Soekarno Hatta Malang
                    </span>
                </div>
                <div>
                    <h4 class="font-bold text-xs md:text-sm uppercase tracking-wider mb-3 md:mb-4">Layanan Kami</h4>
                    <ul class="space-y-2 text-xs md:text-sm text-[#47464E]">
                        <li>Kiloan Premium</li>
                        <li>Dry Cleaning Satuan</li>
                        <li>Cuci Sepatu &amp; Tas</li>
                        <li>Bed Cover &amp; Karpet</li>
                        <li>Express 3 Jam</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-xs md:text-sm uppercase tracking-wider mb-3 md:mb-4">Area &amp; Bantuan</h4>
                    <ul class="space-y-2 text-xs md:text-sm text-[#47464E]">
                        <li>Lowokwaru &amp; Soekarno Hatta</li>
                        <li>Klojen &amp; Sekitar Kampus UB/UM</li>
                        <li>Blimbing &amp; Sukun</li>
                        <li>Pertanyaan Umum (FAQ)</li>
                        <li>Syarat &amp; Ketentuan Garansi</li>
                    </ul>
                </div>
            </div>
            <div class="mb-10 md:mb-12">
                <h4 class="font-bold text-xs md:text-sm uppercase tracking-wider mb-3 md:mb-4">Operasional &amp; Kontak</h4>
                <div class="space-y-2 text-xs md:text-sm text-[#47464E]">
                    <p class="flex items-start gap-2">
                        <i class="fa-solid fa-location-dot text-[#9895C8] mt-1 shrink-0"></i>
                        <span>Jl. Soekarno Hatta No. 45, Mojolangu, Kec. Lowokwaru, Kota Malang, Jawa Timur 65142</span>
                    </p>
                    <p class="flex items-center gap-2">
                        <i class="fa-regular fa-clock text-[#9895C8] shrink-0"></i>
                        <span>Buka Setiap Hari: 07.00 - 21.00 WIB</span>
                    </p>
                    <p class="flex items-center gap-2">
                        <i class="fa-brands fa-whatsapp text-[#25D366] shrink-0"></i>
                        <span>WhatsApp: 0812-3456-7890</span>
                    </p>
                </div>
            </div>
            <div class="pt-6 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-3 md:gap-4 text-[10px] md:text-xs text-[#47464E]">
                <p class="text-center md:text-left">© 2025 Rodeo Laundry Malang. Hak Cipta Dilindungi Undang-Undang.</p>
                <div class="flex gap-4 flex-wrap justify-center">
                    <span class="flex items-center gap-1.5 font-bold text-[#1A1743]">
                        <i class="fa-solid fa-shield-halved text-[#4E6700]"></i> Garansi Bersih Higienis
                    </span>
                    <span class="flex items-center gap-1.5 font-bold text-[#1A1743]">
                        <i class="fa-solid fa-circle-check text-[#4E6700]"></i> 1 Mesin 1 Pelanggan
                    </span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>