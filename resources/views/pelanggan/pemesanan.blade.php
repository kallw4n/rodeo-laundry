<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Layanan - Rodeo Laundry Malang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script>
        window.pemesananData = {
            services: @json($services),
            kiloanService: @json($kiloanService),
            preselectId: @json($preselectId),
            preselectName: @json($preselectName),
        };
    </script>
</head>
<body class="bg-[#FAF8FF] text-[#1A1743] font-sans antialiased pb-40 md:pb-36"
      x-data="{
        ...window.pemesananData,

        kiloan: $persist(0).as('cart_kiloan'),
        notes: $persist('').as('cart_notes'),
        serviceQty: $persist({}).as('cart_service_qty'),

        get hargaKiloan() { return this.kiloanService?.price ?? 0; },
        get kiloanName() { return this.kiloanService?.name ?? 'Cuci Kiloan'; },
        get kiloanDescription() { return this.kiloanService?.description ?? ''; },
        get kiloanExists() { return this.kiloanService !== null; },

        init() {
            if (!this.preselectId) return;
            if (this.preselectName && this.preselectName.toLowerCase().includes('kiloan')) {
                if (this.kiloan === 0) this.kiloan = 1;
            } else {
                const found = this.services.find(s => s.id === Number(this.preselectId));
                if (found && (this.serviceQty[found.id] || 0) === 0) {
                    this.serviceQty = { ...this.serviceQty, [found.id]: 1 };
                }
            }
            if (window.history.replaceState) {
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        },

        getQty(id) { return this.serviceQty[id] || 0; },
        changeQty(id, delta) {
            const next = Math.max(0, (this.serviceQty[id] || 0) + delta);
            this.serviceQty = { ...this.serviceQty, [id]: next };
        },
        addService(id) {
            this.serviceQty = { ...this.serviceQty, [id]: 1 };
        },
        addKiloan() { this.kiloan = 1; },
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
        get availableServices() {
            return this.services.filter(s => (this.serviceQty[s.id] || 0) === 0);
        },
        get subtotal() {
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

                <!-- Hamburger -->
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

    <main class="pt-24 md:pt-28 max-w-[1440px] mx-auto px-4 md:px-6">

        <!-- ================= HERO + STEPPER ================= -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 md:gap-6 mb-6 md:mb-8">
            <div>
                <h1 class="text-2xl md:text-4xl lg:text-5xl font-extrabold leading-tight mb-2 md:mb-3">Pesan Layanan Laundry</h1>
                <p class="text-xs md:text-base text-[#47464E] max-w-lg">Pilih paket cucian dengan jaminan higienis 1 drum 1 pelanggan serta antar-jemput tepat waktu.</p>
            </div>

            <!-- Stepper -->
            <div class="flex items-center gap-1.5 md:gap-3 bg-white p-1.5 md:p-2 rounded-full shadow-sm border border-[#E2E7FF]/60 self-start md:self-auto">
                <div class="flex items-center gap-1.5 md:gap-2 px-2.5 md:px-4 py-1.5 md:py-2 bg-[#1A1743] text-white rounded-full shadow-sm">
                    <span class="w-5 h-5 rounded-full bg-[#BFF138] text-[#131B2E] text-[11px] font-extrabold flex items-center justify-center shrink-0">1</span>
                    <span class="text-xs md:text-sm font-bold">Pilih Layanan</span>
                </div>
                <div class="w-3 md:w-6 h-px bg-[#E2E7FF]"></div>
                <div class="flex items-center gap-1.5 md:gap-2 px-2 md:px-3 py-1.5 md:py-2 rounded-full">
                    <span class="w-5 h-5 rounded-full bg-[#F2F3FF] text-[#47464E] text-[10px] font-bold flex items-center justify-center shrink-0">2</span>
                    <span class="hidden md:inline text-sm font-semibold text-[#47464E]">Pengiriman</span>
                </div>
                <div class="w-3 md:w-6 h-px bg-[#E2E7FF]"></div>
                <div class="flex items-center gap-1.5 md:gap-2 px-2 md:px-3 py-1.5 md:py-2 rounded-full">
                    <span class="w-5 h-5 rounded-full bg-[#F2F3FF] text-[#47464E] text-[10px] font-bold flex items-center justify-center shrink-0">3</span>
                    <span class="hidden md:inline text-sm font-semibold text-[#47464E]">Pembayaran</span>
                </div>
            </div>
        </div>

        <!-- ================= SEMUA LAYANAN TERPILIH ================= -->
        <template x-if="hasAnySelected">
            <div class="space-y-3 mb-6 md:mb-8">

                <!-- Cuci Kiloan -->
                <template x-if="kiloan > 0 && kiloanExists">
                    <div class="bg-white rounded-[24px] md:rounded-[32px] p-4 md:p-8 shadow-sm border border-[#E2E7FF]/60">
                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                            <div class="flex gap-3 md:gap-4">
                                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#1A1743] rounded-xl flex items-center justify-center shrink-0 text-[#BFF138]">
                                    <i class="fa-solid fa-weight-hanging text-base md:text-lg"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <h2 class="text-base md:text-xl font-bold" x-text="kiloanName"></h2>
                                        <span class="px-2 md:px-2.5 py-0.5 bg-[#C2F43B] rounded-full text-[10px] md:text-xs font-bold text-[#4E6700] flex items-center gap-1">
                                            <i class="fa-solid fa-star text-[9px]"></i> Populer
                                        </span>
                                    </div>
                                    <p class="text-xs md:text-sm text-[#47464E]" x-text="kiloanDescription"></p>
                                </div>
                            </div>

                            <div class="flex flex-row md:flex-col items-center md:items-end justify-between md:justify-start gap-3 md:gap-2 shrink-0">
                                <div class="flex items-center gap-2 md:gap-3 bg-[#F2F3FF] rounded-full p-1 md:p-1.5">
                                    <button @click="if(kiloan > 1) kiloan--; else kiloan = 0"
                                            class="w-7 h-7 md:w-8 md:h-8 bg-white rounded-full flex items-center justify-center font-bold text-[#1A1743] hover:bg-[#EAEDFF] transition"
                                            title="Kurangi">
                                        <i class="fa-solid fa-minus text-[10px]"></i>
                                    </button>
                                    <span class="text-xs md:text-sm font-bold min-w-[36px] md:min-w-[40px] text-center" x-text="kiloan + ' kg'"></span>
                                    <button @click="kiloan++" 
                                            class="w-7 h-7 md:w-8 md:h-8 bg-[#1A1743] text-white rounded-full flex items-center justify-center font-bold hover:bg-[#2F2D59] transition"
                                            title="Tambah">
                                        <i class="fa-solid fa-plus text-[10px]"></i>
                                    </button>
                                </div>
                                <div class="text-right">
                                    <div class="text-base md:text-2xl font-extrabold">
                                        <span x-text="formatRp(hargaKiloan)"></span>
                                        <span class="text-xs md:text-sm font-normal text-[#47464E]">/ kg</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Layanan lain yang terpilih -->
                <template x-for="service in selectedServices" :key="'sel-' + service.id">
                    <div class="bg-white rounded-[24px] md:rounded-[32px] p-4 md:p-8 shadow-sm border border-[#E2E7FF]/60">
                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                            <div class="flex gap-3 md:gap-4">
                                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#1A1743] rounded-xl flex items-center justify-center shrink-0 text-[#C2F43B]">
                                    <i :class="getServiceIcon(service.name)" class="text-base md:text-lg"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <h2 class="text-base md:text-xl font-bold" x-text="service.name"></h2>
                                        <span class="px-2 md:px-2.5 py-0.5 bg-[#EAEDFF] rounded-full text-[10px] md:text-xs font-bold text-[#1A1743]" x-text="service.tag"></span>
                                    </div>
                                    <p class="text-xs md:text-sm text-[#47464E]" x-text="service.description"></p>
                                </div>
                            </div>

                            <div class="flex flex-row md:flex-col items-center md:items-end justify-between md:justify-start gap-3 md:gap-2 shrink-0">
                                <div class="flex items-center gap-2 md:gap-3 bg-[#F2F3FF] rounded-full p-1 md:p-1.5">
                                    <button @click="changeQty(service.id, -1)"
                                            class="w-7 h-7 md:w-8 md:h-8 bg-white rounded-full flex items-center justify-center font-bold text-[#1A1743] hover:bg-[#EAEDFF] transition"
                                            title="Kurangi">
                                        <i class="fa-solid fa-minus text-[10px]"></i>
                                    </button>
                                    <span class="text-xs md:text-sm font-bold min-w-[36px] md:min-w-[40px] text-center" x-text="getQty(service.id) + ' ' + service.unit"></span>
                                    <button @click="changeQty(service.id, 1)"
                                            class="w-7 h-7 md:w-8 md:h-8 bg-[#1A1743] text-white rounded-full flex items-center justify-center font-bold hover:bg-[#2F2D59] transition"
                                            title="Tambah">
                                        <i class="fa-solid fa-plus text-[10px]"></i>
                                    </button>
                                </div>
                                <div class="text-right">
                                    <div class="text-base md:text-2xl font-extrabold">
                                        <span x-text="formatRp(service.price)"></span>
                                        <span class="text-xs md:text-sm font-normal text-[#47464E]" x-text="'/ ' + service.unit"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        <!-- ================= SECTION: LAYANAN LAINNYA ================= -->
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-3 md:mb-4">
                <span class="w-2 h-2 bg-[#1A1743] rounded-full"></span>
                <h3 class="text-base md:text-lg font-bold">Layanan Lainnya</h3>
                <span class="px-2 py-0.5 bg-[#EAEDFF] rounded-full text-[10px] md:text-xs font-semibold text-[#47464E]"
                      x-text="(availableServices.length + (kiloan === 0 && kiloanExists ? 1 : 0)) + ' Tersedia'"></span>
            </div>

            <template x-if="availableServices.length === 0 && kiloan > 0">
                <div class="text-center py-8 md:py-10 bg-white rounded-[20px] md:rounded-[24px] border border-dashed border-[#E2E7FF]">
                    <p class="text-xs md:text-sm text-[#47464E]">Semua layanan sudah kamu pilih! 🎉</p>
                </div>
            </template>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-5">
                <!-- Cuci Kiloan di grid -->
                <template x-if="kiloan === 0 && kiloanExists">
                    <div class="bg-white p-4 md:p-6 rounded-[20px] md:rounded-[28px] border border-[#E2E7FF]/60 flex flex-col justify-between 
                                hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                        <div>
                            <div class="flex justify-between items-start mb-3 md:mb-4 gap-2">
                                <div class="w-9 h-9 md:w-10 md:h-10 bg-[#1A1743] rounded-xl flex items-center justify-center shrink-0 text-[#BFF138]">
                                    <i class="fa-solid fa-weight-hanging text-base"></i>
                                </div>
                            </div>
                            <h3 class="text-base md:text-lg font-bold mb-1" x-text="kiloanName"></h3>
                            <p class="text-xs md:text-sm text-[#47464E] mb-4 md:mb-5 line-clamp-2" x-text="kiloanDescription"></p>
                        </div>
                        <div class="flex justify-between items-center pt-3 md:pt-4 border-t border-[#E2E7FF]/60 gap-2">
                            <div class="text-base md:text-lg font-extrabold shrink-0">
                                <span x-text="formatRp(hargaKiloan)"></span>
                                <span class="text-[10px] md:text-xs font-normal text-[#47464E]">/ kg</span>
                            </div>
                            <button @click="addKiloan()"
                                    class="px-3 md:px-4 py-1.5 bg-[#EAEDFF] hover:bg-[#1A1743] hover:text-white text-[#1A1743] rounded-full text-[10px] md:text-xs font-bold transition shrink-0 flex items-center gap-1">
                                <i class="fa-solid fa-plus text-[10px]"></i> Tambah
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Layanan lain -->
                <template x-for="service in availableServices" :key="'avail-' + service.id">
                    <div class="bg-white p-4 md:p-6 rounded-[20px] md:rounded-[28px] border border-[#E2E7FF]/60 flex flex-col justify-between 
                                hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                        <div>
                            <div class="flex justify-between items-start mb-3 md:mb-4 gap-2">
                                <div class="w-9 h-9 md:w-10 md:h-10 bg-[#1A1743] rounded-xl flex items-center justify-center shrink-0 text-[#C2F43B]">
                                    <i :class="getServiceIcon(service.name)" class="text-base"></i>
                                </div>
                                <span class="px-2 md:px-2.5 py-0.5 bg-[#EAEDFF] rounded-full text-[10px] md:text-xs font-bold text-[#1A1743] shrink-0" x-text="service.tag"></span>
                            </div>
                            <h3 class="text-base md:text-lg font-bold mb-1" x-text="service.name"></h3>
                            <p class="text-xs md:text-sm text-[#47464E] mb-4 md:mb-5 line-clamp-2" x-text="service.description"></p>
                        </div>
                        <div class="flex justify-between items-center pt-3 md:pt-4 border-t border-[#E2E7FF]/60 gap-2">
                            <div class="text-base md:text-lg font-extrabold shrink-0">
                                <span x-text="formatRp(service.price)"></span>
                                <span class="text-[10px] md:text-xs font-normal text-[#47464E]" x-text="'/ ' + service.unit"></span>
                            </div>
                            <button @click="addService(service.id)"
                                    class="px-3 md:px-4 py-1.5 bg-[#EAEDFF] hover:bg-[#1A1743] hover:text-white text-[#1A1743] rounded-full text-[10px] md:text-xs font-bold transition shrink-0 flex items-center gap-1">
                                <i class="fa-solid fa-plus text-[10px]"></i> Tambah
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- ================= SECTION: CATATAN ================= -->
        <div class="mb-12 md:mb-16">
            <div class="flex items-center gap-2 mb-3 md:mb-4">
                <i class="fa-regular fa-pen-to-square text-[#1A1743] text-sm"></i>
                <h3 class="text-base md:text-lg font-bold">Catatan Tambahan</h3>
                <span class="text-[10px] md:text-xs text-[#47464E]">(Opsional)</span>
            </div>

            <div class="bg-white rounded-[20px] md:rounded-[24px] p-4 md:p-6 border border-[#E2E7FF]/60">
                <textarea id="notes"
                          x-model="notes"
                          maxlength="500"
                          rows="4"
                          placeholder="Contoh: Pisahkan kemeja putih dengan yang berwarna, jangan pakai pengering panas untuk sweater..."
                          class="w-full px-3 md:px-4 py-2.5 md:py-3 rounded-xl md:rounded-2xl border-2 border-[#E2E7FF] 
                                 bg-[#FAF8FF] text-xs md:text-sm text-[#1A1743]
                                 placeholder:text-[#9895C8]
                                 focus:outline-none focus:border-[#BFF138] focus:bg-white
                                 transition-all resize-none"></textarea>
                <div class="flex justify-between items-center mt-2 md:mt-3 gap-2">
                    <span class="text-[10px] md:text-xs font-semibold text-[#47464E] shrink-0">
                        <span x-text="notes.length"></span> / 500
                    </span>
                </div>
            </div>
        </div>
    </main>

    <!-- ================= FOOTER ================= -->
    <footer class="bg-white border-t border-[#E2E7FF]/70 mt-12 md:mt-16">
        <div class="max-w-[1440px] mx-auto px-4 md:px-6 py-12 md:py-16">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 mb-10 md:mb-12">
                <div class="sm:col-span-2">
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-9 h-9 md:w-10 md:h-10 bg-[#2F2D59] rounded-xl flex items-center justify-center shadow-lg shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#bff108ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h3"/><path d="M17 6h.01"/><rect width="18" height="20" x="3" y="2" rx="2"/><circle cx="12" cy="13" r="5"/><path d="M12 18a2.5 2.5 0 0 0 0-5 2.5 2.5 0 0 1 0-5"/></svg>
                        </div>
                        <span class="text-lg md:text-xl font-bold tracking-tight text-[#1A1743]">Rodeo Laundry</span>
                    </div>
                    <p class="text-[#47464E] text-xs md:text-sm mb-4 max-w-sm leading-relaxed">
                        Laundry profesional, cepat, bersih, dan wangi di Malang. Layanan antar-jemput ekspres untuk kenyamanan pakaian higienis Anda setiap hari.
                    </p>
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
                    <h4 class="font-bold text-xs md:text-sm uppercase tracking-wider mb-3 md:mb-4 text-[#1A1743]">Area & Bantuan</h4>
                    <ul class="space-y-2 text-xs md:text-sm text-[#47464E]">
                        <li>Lowokwaru & Soekarno Hatta</li>
                        <li>Klojen & Sekitar Kampus UB/UM</li>
                        <li>Blimbing & Sukun</li>
                        <li>Pertanyaan Umum (FAQ)</li>
                        <li>Syarat & Ketentuan Garansi</li>
                    </ul>
                </div>
            </div>

            <div class="mb-10 md:mb-12 pt-6 border-t border-[#E2E7FF]/50">
                <h4 class="font-bold text-xs md:text-sm uppercase tracking-wider mb-3 md:mb-4 text-[#1A1743]">Operasional & Kontak</h4>
                <div class="space-y-2.5 text-xs md:text-sm text-[#47464E]">
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

            <div class="pt-6 border-t border-[#E2E7FF]/60 flex flex-col md:flex-row justify-between items-center gap-3 md:gap-4 text-[10px] md:text-xs text-[#47464E]">
                <p class="text-center md:text-left">© 2025 Rodeo Laundry Malang. Hak Cipta Dilindungi Undang-Undang.</p>
                <div class="flex gap-4 sm:gap-6 flex-wrap justify-center">
                    <span class="flex items-center gap-1.5 font-bold text-[#1A1743]">
                        <i class="fa-solid fa-shield-halved text-[#4E6700]"></i> Garansi Bersih Higienis
                    </span>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- ================= STICKY BOTTOM BAR ================= -->
    <div class="fixed bottom-0 left-0 right-0 z-40 bg-white shadow-[0_-12px_32px_-8px_rgba(26,23,67,0.12)] border-t border-gray-100">
        <div class="max-w-[1440px] mx-auto px-3 md:px-6 py-3 md:py-4">
            <div class="flex items-center justify-between gap-3 md:gap-4">

                <!-- Kiri: Info -->
                <div class="flex items-center gap-2 md:gap-4 min-w-0 flex-1">
                    <div class="relative w-10 h-10 md:w-12 md:h-12 bg-[#1A1743] rounded-xl flex items-center justify-center shrink-0 text-[#BFF138]">
                        <i class="fa-solid fa-basket-shopping text-base md:text-lg"></i>
                        <div class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-[#BFF138] text-[#131B2E] rounded-full text-[10px] md:text-xs font-extrabold flex items-center justify-center border-2 border-white"
                            x-text="totalItems"></div>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs md:text-sm font-bold truncate" x-text="totalItems + ' Layanan Dipilih'"></div>
                        <div class="text-[10px] md:text-xs text-[#47464E] truncate mt-0.5">
                            <template x-if="kiloan > 0">
                                <span x-text="kiloanName + ' (' + kiloan + 'kg)'"></span>
                            </template>
                            <template x-for="s in selectedServices" :key="'sum-' + s.id">
                                <span x-text="' · ' + s.name + ' (' + getQty(s.id) + ' ' + s.unit + ')'"></span>
                            </template>
                            <template x-if="!hasAnySelected">
                                <span>Belum ada layanan dipilih</span>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Kanan: Total + Button -->
                <div class="flex items-center gap-3 md:gap-6 shrink-0">
                    <div class="text-right">
                        <div class="text-[10px] md:text-xs text-[#47464E]">Total</div>
                        <div class="text-base md:text-2xl font-extrabold" x-text="formatRp(subtotal)"></div>
                    </div>

                    <!-- Tombol Desktop: Enabled/Disabled -->
                    <template x-if="hasAnySelected">
                        <a href="{{ route('pengiriman') }}" 
                           class="hidden sm:flex px-4 md:px-8 py-2.5 md:py-3.5 bg-[#BFF138] text-[#131B2E] font-bold rounded-full text-xs md:text-sm
                                  transition-all duration-300 ease-out
                                  hover:shadow-[0_15px_35px_rgba(204,255,70,0.65)]
                                  hover:-translate-y-1
                                  hover:brightness-110
                                  items-center gap-2">
                            <span class="hidden md:inline">Lanjut ke Jadwal & Pengiriman</span>
                            <span class="md:hidden">Lanjut</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </template>
                    <template x-if="!hasAnySelected">
                        <button disabled
                                class="hidden sm:flex px-4 md:px-8 py-2.5 md:py-3.5 bg-[#E2E7FF] text-[#9895C8] font-bold rounded-full text-xs md:text-sm
                                       cursor-not-allowed items-center gap-2"
                                title="Pilih minimal 1 layanan dulu">
                            <span class="hidden md:inline">Pilih Layanan Dulu</span>
                            <span class="md:hidden">Pilih</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Tombol Mobile (full width di bawah) -->
            <template x-if="hasAnySelected">
                <a href="{{ route('pengiriman') }}" 
                   class="sm:hidden mt-3 w-full py-3 bg-[#BFF138] text-[#131B2E] font-bold rounded-full text-sm
                          transition-all duration-300 ease-out
                          hover:shadow-[0_15px_35px_rgba(204,255,70,0.65)]
                          flex items-center justify-center gap-2">
                    Lanjut ke Jadwal & Pengiriman
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </template>
            <template x-if="!hasAnySelected">
                <button disabled
                        class="sm:hidden mt-3 w-full py-3 bg-[#E2E7FF] text-[#9895C8] font-bold rounded-full text-sm
                               cursor-not-allowed flex items-center justify-center gap-2">
                    Pilih Layanan Dulu
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </template>
        </div>
    </div>

</body>
</html>