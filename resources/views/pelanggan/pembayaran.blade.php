<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Rodeo Laundry Batu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google Fonts Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>

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
        serviceNotes: $persist({}).as('cart_service_notes'), // <-- TAMBAHKAN INI
        notes: $persist('').as('cart_notes'),
        courierNotes: $persist('').as('cart_courier_notes'),
        pickupMethod: $persist('courier').as('cart_pickup'),
        cartAddress: $persist('').as('cart_address'),
        
        summaryOpen: true,
        paymentMethod: 'qris',
        submitting: false,
        error: '',
        
        showQrisModal: false,
        orderNumber: '#RD-' + Math.floor(10000 + Math.random() * 90000),
        timer: 899,
        timerInterval: null,

        startTimer() {
            if (this.timerInterval) clearInterval(this.timerInterval);
            this.timer = 899;
            this.timerInterval = setInterval(() => {
                if (this.timer > 0) {
                    this.timer--;
                } else {
                    clearInterval(this.timerInterval);
                }
            }, 1000);
        },

        get formattedTimer() {
            const m = Math.floor(this.timer / 60).toString().padStart(2, '0');
            const s = (this.timer % 60).toString().padStart(2, '0');
            return `${m} : ${s}`;
        },

        handleConfirmPayment() {
            if (this.paymentMethod === 'qris') {
                this.startTimer();
                this.showQrisModal = true;
            } else {
                this.checkout();
            }
        },

        async checkout() {
            this.submitting = true;
            this.error = '';
            const items = this.selectedServices.map(s => ({ id: s.id, qty: this.serviceQty[s.id] }));
            if (this.kiloan > 0 && this.kiloanService) items.unshift({ id: this.kiloanService.id, qty: this.kiloan });
            
            // ==== PROSES GABUNGIN CATATAN ====
            const notesArray = [];
            
            // Ambil catatan Cuci Kiloan
            if (this.serviceNotes['kiloan'] && this.serviceNotes['kiloan'].trim() !== '') {
                notesArray.push(`Cuci Kiloan: ${this.serviceNotes['kiloan'].trim()}`);
            }
            
            // Ambil catatan layanan satuan
            this.selectedServices.forEach(s => {
                if (this.serviceNotes[s.id] && this.serviceNotes[s.id].trim() !== '') {
                    notesArray.push(`${s.name}: ${this.serviceNotes[s.id].trim()}`);
                }
            });
            
            // Ambil catatan lama (kalau ada dari versi sebelumnya)
            if (this.notes && this.notes.trim() !== '') {
                notesArray.push(this.notes.trim());
            }

            const combinedNotes = notesArray.join(' | ');
            // =================================

            try {
                const res = await fetch('{{ route('pesan.store') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({
                        items,
                        payment_method: this.paymentMethod,
                        pickup: this.pickupMethod,
                        address: this.cartAddress,
                        notes: combinedNotes, // <-- Kirim string gabungan
                    }),
                });
                if (!res.ok) { 
                    this.error = 'Gagal membuat pesanan, silakan coba lagi.'; 
                    this.submitting = false; 
                    return; 
                }
                ['cart_kiloan', 'cart_service_qty', 'cart_notes', 'cart_service_notes', 'cart_courier_notes', 'cart_pickup', 'cart_address'].forEach(k => localStorage.removeItem('_x_' + k));
                const data = await res.json();
                window.location = data.redirect;
            } catch (err) {
                this.error = 'Terjadi kesalahan koneksi, silakan coba lagi.';
                this.submitting = false;
            }
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
    <header class="fixed top-0 w-full h-16 md:h-20 bg-[#FAF8FF]/85 backdrop-blur-md shadow-sm z-40"
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
                    <div class="text-[9px] md:text-[10px] font-bold uppercase tracking-wider text-[#47464E] mt-1">Batu</div>
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

                @auth
                    <a href="{{ route('profile.edit') }}" 
                class="w-8 h-8 md:w-9 md:h-9 bg-[#1A1743] hover:bg-[#2F2D59] text-white rounded-full flex items-center justify-center transition shadow-sm shrink-0" 
                title="Profil Saya">
                    <i class="fa-regular fa-user text-xs md:text-sm"></i>
                </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 md:px-5 py-2 bg-[#1A1743] text-white rounded-full text-xs md:text-sm font-bold hover:bg-[#2F2D59] shadow-sm transition">
                        Masuk
                    </a>
                @endauth

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
                                        <!-- TAMPILKAN CATATAN KILOAN -->
                                        <template x-if="serviceNotes['kiloan'] && serviceNotes['kiloan'].trim() !== ''">
                                            <p class="text-xs text-[#47464E] mt-1 pl-3 border-l-2 border-[#BFF138]"
                                               x-text="'↳ ' + serviceNotes['kiloan']"></p>
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
                                        <!-- TAMPILKAN CATATAN LAYANAN -->
                                        <template x-if="serviceNotes[service.id] && serviceNotes[service.id].trim() !== ''">
                                            <p class="text-xs text-[#47464E] mt-1 pl-3 border-l-2 border-[#BFF138]"
                                               x-text="'↳ ' + serviceNotes[service.id]"></p>
                                        </template>
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
                        <div class="mt-4 pt-3 border-t border-[#E2E7FF]/70 bg-[#FAF8FF] p-3 md:p-4 rounded-xl border border-[#E2E7FF]/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex items-start gap-3 min-w-0 flex-1">
                                <div class="w-8 h-8 rounded-lg bg-[#EAEDFF] text-[#1A1743] flex items-center justify-center shrink-0 mt-0.5">
                                    <i :class="pickupMethod === 'courier' ? 'fa-solid fa-truck text-xs' : 'fa-solid fa-store text-xs'"></i>
                                </div>
                                <div class="min-w-0 flex-1 text-xs">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-[#1A1743]" x-text="pickupMethod === 'courier' ? 'Dijemput & Diantar Kurir' : 'Antar Sendiri & Ambil Sendiri'"></span>
                                    </div>
                                    <template x-if="pickupMethod !== 'courier'">
                                        <p class="text-[#47464E] text-[11px] leading-relaxed mt-0.5">
                                            <span class="font-semibold text-[#1A1743]">Outlet:</span> Gg. Rodeo, RT.04/RW.10, Sumberejo, Kec. Batu, Kota Batu, Jawa Timur 65318
                                        </p>
                                    </template>
                                    <template x-if="pickupMethod === 'courier'">
                                        <div>
                                            <p class="text-[#47464E] mt-1 text-[11px]" x-show="cartAddress" x-text="'Alamat: ' + cartAddress"></p>
                                            <p class="text-[#47464E] mt-0.5 text-[11px]" x-show="courierNotes" x-text="'Catatan Kurir: ' + courierNotes"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Tombol Navigasi di Ujung Kanan Card -->
                            <template x-if="pickupMethod !== 'courier'">
                                <div class="shrink-0 sm:self-center pl-11 sm:pl-0">
                                    <a href="https://maps.app.goo.gl/VNZ24TUTkq7HTJbp9" 
                                       target="_blank" 
                                       rel="noopener noreferrer"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-[#CAD2FF] hover:border-[#1A1743] hover:bg-[#EAEDFF]/50 text-[#1A1743] rounded-full text-xs font-bold shadow-2xs transition">
                                        <i class="fa-solid fa-diamond-turn-right text-emerald-600 text-xs"></i>
                                        <span>Buka Maps</span>
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[9px] text-[#717082]"></i>
                                    </a>
                                </div>
                            </template>
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
                    <input type="radio" name="payment" value="qris" class="sr-only" x-model="paymentMethod">
                    <div class="p-4 md:p-5 rounded-2xl border-2 transition-all duration-200"
                         :class="paymentMethod === 'qris' ? 'border-[#BFF138] bg-[#FAF8FF] shadow-sm' : 'border-[#E2E7FF] bg-white hover:border-[#CAD2FF]'">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 md:gap-4 flex-1 min-w-0">
                                <!-- Radio Button Circle -->
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 mt-1 transition-all duration-200"
                                     :class="paymentMethod === 'qris' ? 'border-[#1A1743] bg-[#1A1743]' : 'border-[#CAD2FF] bg-white'">
                                    <div class="w-2 h-2 bg-white rounded-full transition-all duration-200"
                                         :class="paymentMethod === 'qris' ? 'opacity-100 scale-100' : 'opacity-0 scale-50'"></div>
                                </div>
                                <div class="w-10 h-10 md:w-11 md:h-11 bg-[#1A1743] text-[#BFF138] rounded-xl flex items-center justify-center shrink-0 shadow-sm">
                                    <i class="fa-solid fa-qrcode text-lg"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap mb-1">
                                        <span class="font-bold text-[#1A1743] text-sm md:text-base">QRIS (Semua E-Wallet &amp; M-Banking)</span>
                                    </div>
                                    <p class="text-xs text-[#47464E]">BCA Mobile, Livin' by Mandiri, Wondr by BNI, BRImo, GoPay, OVO, ShopeePay, DANA,</p>
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
                    <input type="radio" name="payment" value="va" class="sr-only" x-model="paymentMethod">
                    <div class="p-4 md:p-5 rounded-2xl border-2 transition-all duration-200"
                         :class="paymentMethod === 'va' ? 'border-[#BFF138] bg-[#FAF8FF] shadow-sm' : 'border-[#E2E7FF] bg-white hover:border-[#CAD2FF]'">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 md:gap-4 flex-1 min-w-0">
                                <!-- Radio Button Circle -->
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 mt-1 transition-all duration-200"
                                     :class="paymentMethod === 'va' ? 'border-[#1A1743] bg-[#1A1743]' : 'border-[#CAD2FF] bg-white'">
                                    <div class="w-2 h-2 bg-white rounded-full transition-all duration-200"
                                         :class="paymentMethod === 'va' ? 'opacity-100 scale-100' : 'opacity-0 scale-50'"></div>
                                </div>
                                <div class="w-10 h-10 md:w-11 md:h-11 bg-[#EAEDFF] text-[#1A1743] rounded-xl flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-building-columns text-base md:text-lg"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap mb-1">
                                        <p class="font-bold text-[#1A1743] text-sm md:text-base">Transfer Bank / Virtual Account</p>
                                    </div>
                                    <p class="text-xs text-[#47464E]">BCA Virtual Account, Mandiri, BNI, BRI, Permata Bank</p>
                                </div>
                            </div>
                            <span class="hidden sm:inline-flex items-center gap-1 px-3 py-1 border border-[#E2E7FF] rounded-lg text-xs font-bold text-[#1A1743] shrink-0 bg-white">
                                <i class="fa-solid fa-building-columns text-xs"></i> VA Bank
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

            <button type="button"
                    @click="handleConfirmPayment()" 
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

        </div>
    </main>

    <!-- ================= MODAL SIMULASI QRIS ================= -->
    <div x-show="showQrisModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-[#0E0E1B]/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 overflow-y-auto"
         style="display: none;">
        
        <div @click.outside="showQrisModal = false"
             class="w-full max-w-[460px] bg-white rounded-[28px] p-6 shadow-2xl relative text-[#1A1743] my-8">

            <!-- Close Button -->
            <button @click="showQrisModal = false"
                    class="absolute top-5 right-5 text-gray-400 hover:text-gray-700 transition w-8 h-8 rounded-full flex items-center justify-center bg-gray-100">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>

            <!-- Modal Header -->
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h3 class="text-xl font-extrabold text-[#1A1743]">Bayar via QRIS</h3>
                    <span class="px-2 py-0.5 bg-[#EAEDFF] rounded-full text-[11px] font-bold text-[#1A1743]">QRIS</span>
                </div>
                <p class="text-xs text-[#717082]">Scan kode QR dengan m-Banking atau E-Wallet apa saja</p>
            </div>

            <!-- Countdown Banner -->
            <div class="bg-[#EAEDFF]/70 rounded-xl px-4 py-2.5 flex items-center justify-between text-xs font-semibold text-[#1A1743] mt-4">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-hourglass-half text-[#1A1743]"></i>
                    <span>Selesaikan pembayaran dalam</span>
                </div>
                <div class="font-extrabold tracking-wider" x-text="formattedTimer">14 : 59</div>
            </div>

            <!-- QR Card Area -->
            <div class="bg-[#F6F7FB] rounded-2xl p-5 flex flex-col items-center mt-4">
                <!-- White QR Card -->
                <div class="w-80 bg-white rounded-2xl p-6 shadow-sm border border-[#E2E7FF] flex flex-col items-center relative overflow-hidden">
                    <div class="w-74 h-74 bg-white flex items-center justify-center p-1 rounded-lg z-10">
                        <img src="{{ asset('images/qris-rodeo.jpg') }}" 
                        alt="QRIS Pembayaran" 
                        class="w-full h-full object-contain">
                    </div>
                </div>
                <!-- Unduh QR button -->
                <a href="{{ asset('images/qris-rodeo.jpg') }}" 
                download="QRIS-Rodeo-Laundry.jpg"
                class="mt-3 px-4 py-1.5 bg-white border border-[#E2E7FF] rounded-lg text-xs font-bold text-[#1A1743] hover:bg-gray-50 flex items-center gap-1.5 shadow-2xs transition">
                    <i class="fa-solid fa-arrow-down-to-bracket text-xs"></i>
                    Unduh QR
                </a>
            </div>

            <!-- Details Section -->
            <div class="mt-4 space-y-2 text-xs">
                <div class="flex justify-between items-center">
                    <span class="text-[#717082]">Total Tagihan</span>
                    <span class="text-base font-extrabold text-[#1A1743]" x-text="formatRp(totalBayar)"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[#717082]">Nomor Pesanan</span>
                    <span class="font-bold text-[#1A1743]" x-text="orderNumber"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[#717082]">Merchant</span>
                    <span class="font-bold text-[#1A1743]">Rodeo Laundry</span>
                </div>
            </div>

            <!-- Action Button -->
            <button type="button"
                    @click="checkout()"
                    :disabled="submitting"
                    class="w-full bg-[#BFF108] hover:brightness-105 text-[#131B2E] font-extrabold rounded-2xl py-3.5 flex items-center justify-center gap-2 mt-5 transition shadow-sm">
                <template x-if="submitting">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin"></i>
                        Memverifikasi Pembayaran...
                    </span>
                </template>
                <template x-if="!submitting">
                    <span class="flex items-center gap-2">
                        Cek Status Pembayaran
                        <i class="fa-solid fa-rotate text-sm"></i>
                    </span>
                </template>
            </button>

            <!-- Cancel Button -->
            <button type="button"
                    @click="showQrisModal = false"
                    class="w-full text-center text-xs font-semibold text-[#717082] hover:text-[#1A1743] mt-3 transition">
                Batal / Ubah Metode Pembayaran
            </button>
        </div>
    </div>

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
                        Laundry profesional, cepat, bersih, dan wangi di Batu. Layanan antar-jemput ekspres untuk kenyamanan pakaian higienis Anda.
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