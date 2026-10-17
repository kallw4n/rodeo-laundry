<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Rodeo Laundry Malang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
                    notes: [this.notes, this.courierNotes].filter(Boolean).join(' / '),
                }),
            });
            if (!res.ok) { this.error = 'Gagal membuat pesanan, coba lagi.'; this.submitting = false; return; }
            ['cart_kiloan', 'cart_service_qty', 'cart_notes', 'cart_courier_notes', 'cart_pickup'].forEach(k => localStorage.removeItem('_x_' + k));
            window.location = (await res.json()).redirect;
        },

        get hargaKiloan() { return this.kiloanService?.price ?? 0; },
        get kiloanName() { return this.kiloanService?.name ?? 'Cuci Kiloan'; },
        getService(id) { return this.services.find(s => s.id === id); },
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
                <div class="w-9 h-9 md:w-10 md:h-10 bg-[#2F2D59] flex items-center justify-center shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#BFF108" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h3"/><path d="M17 6h.01"/><rect width="18" height="20" x="3" y="2" rx="2"/><circle cx="12" cy="13" r="5"/><path d="M12 18a2.5 2.5 0 0 0 0-5 2.5 2.5 0 0 1 0-5"/></svg>
                </div>
                <div>
                    <div class="text-base md:text-lg font-semibold leading-none">
                        Rodeo Laundry<span class="text-[#A7D717]">.</span>
                    </div>
                    <div class="text-[9px] md:text-[10px] font-bold uppercase tracking-wider text-[#47464E] mt-1">Malang</div>
                </div>
            </a>

            <!-- Nav Desktop -->
            <nav class="hidden md:flex items-center gap-1 bg-[#F2F3FF] p-1.5 rounded-full">
                <a href="{{ url('/') }}" class="px-4 py-1.5 text-sm font-semibold text-[#47464E] hover:text-[#1A1743] rounded-full">Beranda</a>
                <a href="{{ route('pemesanan') }}" class="px-4 py-1.5 bg-[#EAEDFF] rounded-full text-sm font-bold">Layanan</a>
                <a href="{{ route('pesanan.index') }}" class="px-4 py-1.5 text-sm font-semibold text-[#47464E] hover:text-[#1A1743]">Pesanan</a>
            </nav>

            <!-- Right Side -->
            <div class="flex items-center gap-2 md:gap-3">
                <a href="#" class="hidden sm:flex items-center gap-2 px-3 md:px-4 py-2 bg-[#EAEDFF] rounded-full text-xs md:text-sm font-semibold text-[#131B2E]">
                    <div class="w-3 h-3 md:w-4 md:h-4 bg-[#4E6700]"></div> WhatsApp
                </a>

                <div class="w-8 h-8 bg-[#1A1743] rounded-full flex items-center justify-center">
                    <div class="w-3 h-3 bg-white"></div>
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
                <a href="{{ url('/') }}" class="block px-4 py-3 rounded-xl text-sm font-semibold text-[#47464E] hover:bg-[#F2F3FF] hover:text-[#1A1743] transition">
                    Beranda
                </a>
                <a href="{{ route('pemesanan') }}" class="block px-4 py-3 rounded-xl text-sm font-bold bg-[#EAEDFF] text-[#1A1743]">
                    Layanan
                </a>
                <a href="{{ route('pesanan.index') }}" class="block px-4 py-3 rounded-xl text-sm font-semibold text-[#47464E] hover:bg-[#F2F3FF] hover:text-[#1A1743] transition">
                    Pesanan
                </a>
                <a href="#" class="block px-4 py-3 rounded-xl text-sm font-semibold text-[#47464E] hover:bg-[#F2F3FF] hover:text-[#1A1743] transition">
                    Hubungi WhatsApp
                </a>
            </div>
        </div>
    </header>

    <!-- ================= MAIN ================= -->
    <main class="pt-32 md:pt-36 max-w-[1440px] mx-auto px-4 md:px-6 pb-16">

        <!-- ================= HERO + STEPPER ================= -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 md:gap-6 mb-6 md:mb-8">
            <div>
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-[#C2F43B]/50 text-[#4E6700] text-[10px] md:text-xs font-bold uppercase rounded-full mb-3">
                    <span class="w-2 h-2 bg-[#4E6700] rounded-full"></span> Layanan On-Demand Malang
                </span>
                <h1 class="text-2xl md:text-4xl lg:text-5xl font-extrabold leading-tight mb-2 md:mb-3">Pembayaran</h1>
                <p class="text-xs md:text-base text-[#47464E] max-w-lg">Pilih paket cucian & treatment pakaian dengan jaminan higienis 1 drum 1 pelanggan serta antar-jemput tepat waktu.</p>
            </div>

            <!-- Stepper -->
            <div class="flex items-center gap-1.5 md:gap-3 bg-white p-1.5 md:p-2 rounded-full shadow-sm border border-[#E2E7FF]/60 self-start md:self-auto">
                <a href="{{ route('pemesanan') }}" class="flex items-center gap-1.5 md:gap-2 px-2 md:px-3 py-1.5 md:py-2 rounded-full hover:bg-[#F2F3FF] transition">
                    <span class="w-5 h-5 rounded-full bg-[#1A1743] text-white text-[10px] font-bold flex items-center justify-center shrink-0">1</span>
                    <span class="hidden md:inline text-sm font-semibold text-[#47464E]">Pilih Layanan</span>
                </a>
                <div class="w-3 md:w-6 h-px bg-[#E2E7FF]"></div>
                <a href="{{ route('pengiriman') }}" class="flex items-center gap-1.5 md:gap-2 px-2 md:px-3 py-1.5 md:py-2 rounded-full hover:bg-[#F2F3FF] transition">
                    <span class="w-5 h-5 rounded-full bg-[#1A1743] text-white text-[10px] font-bold flex items-center justify-center shrink-0">2</span>
                    <span class="hidden md:inline text-sm font-semibold text-[#47464E]">Pengiriman</span>
                </a>
                <div class="w-3 md:w-6 h-px bg-[#E2E7FF]"></div>
                <div class="flex items-center gap-1.5 md:gap-2 px-2.5 md:px-4 py-1.5 md:py-2 bg-[#1A1743] text-white rounded-full">
                    <div class="w-4 h-4 bg-[#BFF138] rounded-full shrink-0"></div>
                    <span class="text-xs md:text-sm font-bold">Pembayaran</span>
                </div>
            </div>
        </div>

        <!-- ================= MAIN CARD ================= -->
        <div class="bg-white rounded-[24px] md:rounded-[32px] p-4 md:p-10 shadow-sm border border-[#E2E7FF]/60">

            <!-- ====== RINGKASAN PESANAN ====== -->
            <div class="border border-[#E2E7FF] rounded-2xl md:rounded-3xl overflow-hidden mb-6 md:mb-8">
                <button @click="summaryOpen = !summaryOpen"
                        class="w-full px-4 md:px-6 py-3 md:py-4 flex items-center justify-between hover:bg-[#FAF8FF] transition gap-2">
                    <div class="flex items-center gap-2 md:gap-3 min-w-0">
                        <svg class="w-5 h-5 text-[#1A1743] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="font-bold text-[#1A1743] text-sm md:text-base truncate">Ringkasan Pesanan</span>
                        <span class="px-2 py-0.5 bg-[#EAEDFF] rounded-full text-[10px] md:text-xs font-bold text-[#1A1743] shrink-0"
                              x-text="totalItems + ' Layanan'"></span>
                    </div>
                    <svg class="w-4 h-4 md:w-5 md:h-5 text-[#47464E] transition-transform duration-300 shrink-0"
                         :class="summaryOpen ? 'rotate-180' : 'rotate-0'"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="summaryOpen" x-collapse
                     class="border-t border-[#E2E7FF] px-4 md:px-6 py-4 md:py-5">

                    <div class="space-y-3 md:space-y-4 mb-5 md:mb-6">
                        <template x-if="kiloan > 0">
                            <div class="flex justify-between items-start gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-[#1A1743] text-sm md:text-base" x-text="kiloanName"></span>
                                        <span class="text-xs md:text-sm text-[#47464E]" x-text="'(' + kiloan + ' kg)'"></span>
                                    </div>
                                    <template x-if="notes && notes.trim().length > 0">
                                        <p class="text-xs text-[#47464E] mt-1 pl-4 border-l-2 border-[#BFF138]"
                                           x-text="'↳ ' + notes"></p>
                                    </template>
                                </div>
                                <div class="text-right font-bold text-[#1A1743] text-sm md:text-base shrink-0"
                                     x-text="formatRp(kiloan * hargaKiloan)"></div>
                            </div>
                        </template>

                        <template x-for="service in selectedServices" :key="'sum-' + service.id">
                            <div class="flex justify-between items-start gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-[#1A1743] text-sm md:text-base" x-text="service.name"></span>
                                        <span class="text-xs md:text-sm text-[#47464E]"
                                              x-text="'(' + (serviceQty[service.id] || 0) + ' ' + service.unit + ')'"></span>
                                    </div>
                                </div>
                                <div class="text-right font-bold text-[#1A1743] text-sm md:text-base shrink-0"
                                     x-text="formatRp(service.price * (serviceQty[service.id] || 0))"></div>
                            </div>
                        </template>

                        <template x-if="!hasAnySelected">
                            <p class="text-sm text-[#47464E] italic">Belum ada layanan yang dipilih.</p>
                        </template>
                    </div>

                    <div class="border-t border-[#E2E7FF] pt-4 flex justify-between items-center gap-3">
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
                <span class="text-[10px] md:text-xs text-[#47464E]">Diproses Instan &amp; Aman</span>
            </div>

            <div class="space-y-2.5 md:space-y-3">

                <!-- QRIS -->
                <label class="cursor-pointer block">
                    <input type="radio" name="payment" value="qris" class="peer sr-only" x-model="paymentMethod">
                    <div class="p-4 md:p-5 rounded-2xl border-2 transition-all border-[#E2E7FF] bg-white peer-checked:border-[#BFF138] peer-checked:bg-[#FAF8FF]">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 flex-1 min-w-0">
                                <div class="w-5 h-5 rounded-full border-2 border-[#E2E7FF] flex items-center justify-center shrink-0 mt-0.5 peer-checked:border-[#1A1743] peer-checked:bg-[#1A1743]">
                                    <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap mb-1">
                                        <span class="font-bold text-[#1A1743] text-sm md:text-base">QRIS (Semua E-Wallet &amp; M-Banking)</span>
                                        <span class="px-2 py-0.5 bg-[#C2F43B] rounded-full text-[10px] font-bold text-[#4E6700]">Otomatis</span>
                                    </div>
                                    <p class="text-xs text-[#47464E]">BCA Mobile, GoPay, OVO, ShopeePay, DANA, Livin', LinkAja</p>

                                    <template x-if="paymentMethod === 'qris'">
                                        <div class="mt-3 md:mt-4 p-3 md:p-4 bg-white rounded-xl border border-[#E2E7FF] flex items-start gap-3">
                                            <div class="w-9 h-9 md:w-10 md:h-10 bg-[#1A1743] rounded-lg flex items-center justify-center shrink-0">
                                                <svg class="w-4 h-4 md:w-5 md:h-5 text-[#BFF138]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-xs md:text-sm font-bold text-[#1A1743] mb-0.5">Kode QR Dinamis Otomatis</p>
                                                <p class="text-[10px] md:text-xs text-[#47464E]">Kode bayar terbit otomatis setelah tombol konfirmasi ditekan.</p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <span class="hidden sm:inline-block px-3 py-1 border border-[#E2E7FF] rounded-lg text-xs font-bold text-[#1A1743] shrink-0">QRIS</span>
                        </div>
                    </div>
                </label>

                <!-- Transfer Bank -->
                <label class="cursor-pointer block">
                    <input type="radio" name="payment" value="va" class="peer sr-only" x-model="paymentMethod">
                    <div class="p-4 md:p-5 rounded-2xl border-2 transition-all border-[#E2E7FF] bg-white peer-checked:border-[#BFF138] peer-checked:bg-[#FAF8FF]">
                        <div class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full border-2 border-[#E2E7FF] flex items-center justify-center shrink-0 mt-0.5 peer-checked:border-[#1A1743] peer-checked:bg-[#1A1743]">
                                <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-[#1A1743] mb-1 text-sm md:text-base">Transfer Bank / Virtual Account</p>
                                <p class="text-xs text-[#47464E]">BCA VA, Mandiri, BNI, BRI, Permata Bank</p>
                            </div>
                        </div>
                    </div>
                </label>

                <!-- E-Wallet -->
                <label class="cursor-pointer block">
                    <input type="radio" name="payment" value="ewallet" class="peer sr-only" x-model="paymentMethod">
                    <div class="p-4 md:p-5 rounded-2xl border-2 transition-all border-[#E2E7FF] bg-white peer-checked:border-[#BFF138] peer-checked:bg-[#FAF8FF]">
                        <div class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full border-2 border-[#E2E7FF] flex items-center justify-center shrink-0 mt-0.5 peer-checked:border-[#1A1743] peer-checked:bg-[#1A1743]">
                                <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-[#1A1743] mb-1 text-sm md:text-base">E-Wallet Direct Debit</p>
                                <p class="text-xs text-[#47464E]">Notifikasi langsung ke GoPay, ShopeePay, atau OVO</p>
                            </div>
                        </div>
                    </div>
                </label>

                <!-- COD -->
                <label class="cursor-pointer block">
                    <input type="radio" name="payment" value="cod" class="peer sr-only" x-model="paymentMethod">
                    <div class="p-4 md:p-5 rounded-2xl border-2 transition-all border-[#E2E7FF] bg-white peer-checked:border-[#BFF138] peer-checked:bg-[#FAF8FF]">
                        <div class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full border-2 border-[#E2E7FF] flex items-center justify-center shrink-0 mt-0.5 peer-checked:border-[#1A1743] peer-checked:bg-[#1A1743]">
                                <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-[#1A1743] mb-1 text-sm md:text-base">Bayar di Tempat (COD / Tunai)</p>
                                <p class="text-xs text-[#47464E]">Bayar tunai atau QR saat kurir mengantar pakaian bersih ke rumah</p>
                            </div>
                        </div>
                    </div>
                </label>
            </div>

            <!-- ====== TOMBOL KONFIRMASI & BAYAR ====== -->
            <p x-show="error" x-text="error" class="mt-4 text-sm font-semibold text-red-600"></p>
            <button @click="checkout()" :disabled="!hasAnySelected || submitting" class="disabled:opacity-50 disabled:cursor-not-allowed w-full mt-6 md:mt-8 px-5 md:px-8 py-4 bg-[#BFF138] text-[#131B2E] font-bold rounded-2xl
                           flex flex-col sm:flex-row items-center justify-between gap-2 sm:gap-0
                           transition-all duration-300 ease-out
                           hover:shadow-[0_15px_35px_rgba(204,255,70,0.65)]
                           hover:-translate-y-1
                           hover:brightness-110">
                <span class="text-sm md:text-base">Konfirmasi &amp; Bayar</span>
                <span class="flex items-center gap-2">
                    <span class="text-base md:text-lg font-extrabold" x-text="formatRp(totalBayar)"></span>
                    <span>→</span>
                </span>
            </button>

        </div>
    </main>

    <!-- ================= FOOTER ================= -->
    <footer class="bg-white border-t border-gray-100 mt-16">
        <div class="max-w-[1440px] mx-auto px-4 md:px-6 py-12 md:py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 md:gap-12 mb-10 md:mb-12">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-9 h-9 bg-[#2F2D59] flex items-center justify-center"><div class="w-3.5 h-5 bg-[#BFF138]"></div></div>
                        <span class="text-lg md:text-xl font-bold">Rodeo Laundry</span>
                    </div>
                    <p class="text-[#47464E] text-xs md:text-sm mb-4 max-w-sm">Laundry profesional, cepat, bersih, dan wangi di Malang. Layanan antar-jemput ekspres untuk kenyamanan pakaian higienis Anda setiap hari.</p>
                    <span class="inline-flex items-center gap-2 px-3 py-1 bg-[#C2F43B] rounded-full text-[10px] md:text-xs font-bold text-[#151F00]">
                        <div class="w-2 h-2 bg-[#4E6700] rounded-full"></div> Outlet Soekarno Hatta Malang
                    </span>
                </div>
                <div>
                    <h4 class="font-bold text-xs md:text-sm uppercase tracking-wider mb-3 md:mb-4">Layanan Kami</h4>
                    <ul class="space-y-2 text-xs md:text-sm text-[#47464E]">
                        <li>Kiloan Premium</li>
                        <li>Dry Cleaning Satuan</li>
                        <li>Cuci Sepatu & Tas</li>
                        <li>Bed Cover & Karpet</li>
                        <li>Express 3 Jam</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-xs md:text-sm uppercase tracking-wider mb-3 md:mb-4">Area & Bantuan</h4>
                    <ul class="space-y-2 text-xs md:text-sm text-[#47464E]">
                        <li>Lowokwaru & Soekarno Hatta</li>
                        <li>Klojen & Sekitar Kampus UB/UM</li>
                        <li>Blimbing & Sukun</li>
                        <li>Pertanyaan Umum (FAQ)</li>
                        <li>Syarat & Ketentuan Garansi</li>
                    </ul>
                </div>
            </div>
            <div class="mb-10 md:mb-12">
                <h4 class="font-bold text-xs md:text-sm uppercase tracking-wider mb-3 md:mb-4">Operasional & Kontak</h4>
                <div class="space-y-2 text-xs md:text-sm text-[#47464E]">
                    <p>Jl. Soekarno Hatta No. 45, Mojolangu, Kec. Lowokwaru, Kota Malang, Jawa Timur 65142</p>
                    <p>Buka Setiap Hari: 07.00 - 21.00 WIB</p>
                    <p>WhatsApp: 0812-3456-7890</p>
                </div>
            </div>
            <div class="pt-6 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-3 md:gap-4 text-[10px] md:text-xs text-[#47464E]">
                <p>© 2025 Rodeo Laundry Malang. Hak Cipta Dilindungi Undang-Undang.</p>
                <div class="flex gap-4 flex-wrap justify-center">
                    <span class="flex items-center gap-1 font-bold"><div class="w-3 h-3 bg-[#4E6700]"></div> Garansi Bersih Higienis</span>
                    <span class="flex items-center gap-1 font-bold"><div class="w-3 h-3 bg-[#4E6700]"></div> 1 Mesin 1 Pelanggan</span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>