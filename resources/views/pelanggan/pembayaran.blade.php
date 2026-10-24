<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Rodeo Laundry Malang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
        notes: $persist('').as('cart_notes'),
        courierNotes: $persist('').as('cart_courier_notes'),
        pickupMethod: $persist('courier').as('cart_pickup'),
        cartAddress: $persist('').as('cart_address'),

        summaryOpen: true,
        paymentMethod: 'qris',
        showQrisModal: false,
        submitting: false,
        error: '',
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
            
            try {
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
                if (!res.ok) { 
                    this.error = 'Gagal membuat pesanan, silakan coba lagi.'; 
                    this.submitting = false; 
                    return; 
                }
                ['cart_kiloan', 'cart_service_qty', 'cart_notes', 'cart_courier_notes', 'cart_pickup', 'cart_address'].forEach(k => localStorage.removeItem('_x_' + k));
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
            this.services.forEach(s => {
                if ((this.serviceQty[s.id] || 0) > 0) count++;
            });
            return count;
        },
        get hasAnySelected() { return this.kiloan > 0 || this.selectedServices.length > 0; },
        formatRp(n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); }
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
                    <div class="text-[9px] md:text-[10px] font-bold uppercase tracking-wider text-[#47464E] mt-1">Malang</div>
                </div>
            </a>

            <!-- Nav Desktop -->
            <nav class="hidden md:flex items-center gap-1 bg-[#F2F3FF] p-1.5 rounded-full">
                <a href="{{ url('/') }}" class="px-4 py-1.5 text-sm font-semibold text-[#47464E] hover:text-[#1A1743] rounded-full transition">Beranda</a>
                <a href="{{ route('pemesanan') }}" class="px-4 py-1.5 bg-[#EAEDFF] rounded-full text-sm font-bold text-[#1A1743]">Layanan</a>
                <a href="{{ route('pesanan.index') }}" class="px-4 py-1.5 text-sm font-semibold text-[#47464E] hover:text-[#1A1743] transition">Pesanan</a>
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
                <!-- Badge Pill -->
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#E5F992] text-[#4E6700] text-xs font-bold tracking-wide mb-3">
                    <span class="w-2 h-2 rounded-full bg-[#4E6700]"></span>
                    LAYANAN ON-DEMAND MALANG
                </div>
                <h1 class="text-3xl md:text-5xl font-extrabold text-[#1A1743] tracking-tight mb-2">Pembayaran</h1>
                <p class="text-xs md:text-sm text-[#47464E] max-w-xl leading-relaxed">
                    Pilih paket cucian &amp; treatment pakaian dengan jaminan higienis 1 drum 1 pelanggan serta antar-jemput tepat waktu.
                </p>
            </div>

            <!-- Stepper Container -->
            <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-full shadow-sm border border-[#E2E7FF]/60 self-start md:self-auto">
                <a href="{{ route('pemesanan') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-full hover:bg-[#F2F3FF] transition text-xs md:text-sm font-semibold text-[#47464E]">
                    <span class="w-5 h-5 rounded-full bg-[#1A1743] text-white text-[10px] font-bold flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-check text-[9px]"></i>
                    </span>
                    <span class="hidden sm:inline">Pilih Layanan</span>
                </a>
                <span class="text-[#E2E7FF] text-xs">——</span>
                <a href="{{ route('pengiriman') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-full hover:bg-[#F2F3FF] transition text-xs md:text-sm font-semibold text-[#47464E]">
                    <span class="w-5 h-5 rounded-full bg-[#1A1743] text-white text-[10px] font-bold flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-check text-[9px]"></i>
                    </span>
                    <span class="hidden sm:inline">Pengiriman</span>
                </a>
                <span class="text-[#E2E7FF] text-xs">——</span>
                <div class="flex items-center gap-2 px-3.5 py-1.5 bg-[#1A1743] text-white rounded-full shadow-sm text-xs md:text-sm font-bold">
                    <span class="w-5 h-5 rounded-full bg-[#BFF108] text-[#131B2E] text-[11px] font-extrabold flex items-center justify-center shrink-0">3</span>
                    <span>Pembayaran</span>
                </div>
            </div>
        </div>

        <!-- ================= MAIN CARD CONTAINER ================= -->
        <div class="bg-white rounded-[24px] md:rounded-[32px] p-5 md:p-8 lg:p-10 shadow-sm border border-[#E2E7FF]/60">

            <!-- ====== RINGKASAN PESANAN (COLLAPSIBLE) ====== -->
            <div class="bg-[#F6F7FB] border border-[#E2E7FF] rounded-2xl md:rounded-3xl overflow-hidden mb-8">
                <!-- Header Collapsible -->
                <button type="button" 
                        @click="summaryOpen = !summaryOpen"
                        class="w-full px-5 md:px-6 py-4 flex items-center justify-between text-left hover:bg-[#F0F2FA] transition">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-[#EAEDFF] text-[#1A1743] flex items-center justify-center shadow-xs">
                            <i class="fa-solid fa-receipt text-sm"></i>
                        </div>
                        <span class="font-extrabold text-[#1A1743] text-sm md:text-base">Ringkasan Pesanan</span>
                        <span class="px-2.5 py-0.5 bg-[#E5E9F8] rounded-full text-xs font-bold text-[#343A60]"
                              x-text="totalItems + ' Layanan'"></span>
                    </div>
                    <i class="fa-solid fa-chevron-up text-xs text-[#47464E] transition-transform duration-300"
                       :class="summaryOpen ? 'rotate-0' : 'rotate-180'"></i>
                </button>

                <!-- Body Summary -->
                <div x-show="summaryOpen" x-collapse class="px-5 md:px-6 pb-6 pt-2">
                    <div class="space-y-4">
                        <!-- Cuci Kiloan -->
                        <template x-if="kiloan > 0">
                            <div class="flex justify-between items-start gap-4">
                                <div class="min-w-0">
                                    <div class="font-bold text-[#1A1743] text-sm md:text-base">
                                        <span x-text="kiloanName"></span>
                                        <span class="text-xs text-[#47464E] font-normal" x-text="' (' + kiloan + ' kg)'"></span>
                                    </div>
                                    <template x-if="notes && notes.trim().length > 0">
                                        <div class="text-xs text-[#717082] mt-0.5" x-text="'↳ ' + notes"></div>
                                    </template>
                                </div>
                                <div class="text-right font-bold text-[#1A1743] text-sm md:text-base shrink-0"
                                     x-text="formatRp(kiloan * hargaKiloan)"></div>
                            </div>
                        </template>

                        <!-- Layanan Satuan -->
                        <template x-for="service in selectedServices" :key="'sum-' + service.id">
                            <div class="flex justify-between items-start gap-4">
                                <div class="min-w-0">
                                    <div class="font-bold text-[#1A1743] text-sm md:text-base">
                                        <span x-text="service.name"></span>
                                        <span class="text-xs text-[#47464E] font-normal" x-text="' (' + (serviceQty[service.id] || 0) + ' ' + service.unit + ')'"></span>
                                    </div>
                                    <div class="text-xs text-[#717082] mt-0.5" x-text="service.description || 'Treatment profesional & higienis'"></div>
                                </div>
                                <div class="text-right font-bold text-[#1A1743] text-sm md:text-base shrink-0"
                                     x-text="formatRp(service.price * (serviceQty[service.id] || 0))"></div>
                            </div>
                        </template>

                        <template x-if="!hasAnySelected">
                            <p class="text-sm text-[#47464E] italic py-2">Belum ada layanan yang dipilih. <a href="{{ route('pemesanan') }}" class="text-[#1A1743] font-bold underline">Pilih sekarang →</a></p>
                        </template>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-[#E2E7FF] my-4"></div>

                    <!-- Subtotal & Fees Breakdown -->
                    <div class="space-y-2 text-xs md:text-sm text-[#47464E]">
                        <div class="flex justify-between items-center">
                            <span>Subtotal Item</span>
                            <span class="font-semibold text-[#1A1743]" x-text="formatRp(totalBayar)"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="flex items-center gap-1.5">
                                <span x-text="pickupMethod === 'self' ? 'Antar Sendiri' : 'Antar Jemput'"></span>
                                <span class="px-1.5 py-0.5 bg-[#BFF108] text-[#131B2E] text-[10px] font-extrabold rounded-full">GRATIS</span>
                            </span>
                            <span class="font-semibold text-[#1A1743]">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Biaya Layanan</span>
                            <span class="font-semibold text-[#1A1743]">Rp 0</span>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-[#E2E7FF] my-4"></div>

                    <!-- Total Pembayaran -->
                    <div class="flex justify-between items-center">
                        <span class="text-base md:text-lg font-bold text-[#1A1743]">Total Pembayaran</span>
                        <span class="text-xl md:text-2xl font-extrabold text-[#1A1743]" x-text="formatRp(totalBayar)"></span>
                    </div>
                </div>
            </div>

            <!-- ====== METODE PEMBAYARAN ====== -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-credit-card text-[#1A1743] text-sm md:text-base"></i>
                    <h2 class="text-base md:text-lg font-bold text-[#1A1743]">Metode Pembayaran</h2>
                </div>
                <span class="text-xs text-[#717082]">Diproses Instan &amp; Aman</span>
            </div>

            <div class="space-y-3">
                <!-- 1. QRIS -->
                <label class="cursor-pointer block">
                    <input type="radio" name="payment" value="qris" class="sr-only" x-model="paymentMethod">
                    <div class="p-4 md:p-5 rounded-2xl border-2 transition-all duration-200"
                         :class="paymentMethod === 'qris' ? 'border-[#BFF108] bg-[#FDFEF8] shadow-sm' : 'border-[#E2E7FF] bg-white hover:border-[#CAD2FF]'">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 md:gap-4 flex-1 min-w-0">
                                <!-- Radio Circle -->
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 mt-0.5 transition-all"
                                     :class="paymentMethod === 'qris' ? 'border-[#1A1743] bg-white' : 'border-[#CAD2FF] bg-white'">
                                    <div class="w-2.5 h-2.5 bg-[#1A1743] rounded-full transition-all"
                                         :class="paymentMethod === 'qris' ? 'opacity-100 scale-100' : 'opacity-0 scale-50'"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap mb-0.5">
                                        <span class="font-bold text-[#1A1743] text-sm md:text-base">QRIS (Semua E-Wallet &amp; M-Banking)</span>
                                        <span class="px-2 py-0.5 bg-[#C2F43B] rounded-full text-[10px] font-bold text-[#4E6700]">Otomatis</span>
                                    </div>
                                    <p class="text-xs text-[#717082]">BCA Mobile, GoPay, OVO, ShopeePay, DANA, Livin', LinkAja</p>

                                    <!-- Selected Child Banner -->
                                    <template x-if="paymentMethod === 'qris'">
                                        <div class="mt-3 p-3.5 bg-white rounded-xl border border-[#E2E7FF] flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-[#1A1743] text-[#BFF108] flex items-center justify-center shrink-0">
                                                <i class="fa-solid fa-qrcode text-sm"></i>
                                            </div>
                                            <div>
                                                <div class="text-xs font-bold text-[#1A1743]">Kode QR Dinamis Otomatis</div>
                                                <div class="text-[11px] text-[#717082]">Kode bayar terbit otomatis setelah tombol konfirmasi ditekan.</div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 border border-[#E2E7FF] rounded-lg text-xs font-bold text-[#1A1743] shrink-0 bg-white shadow-2xs">
                                QRIS <i class="fa-solid fa-qrcode text-xs"></i>
                            </span>
                        </div>
                    </div>
                </label>

                <!-- 2. Transfer Bank / VA -->
                <label class="cursor-pointer block">
                    <input type="radio" name="payment" value="va" class="sr-only" x-model="paymentMethod">
                    <div class="p-4 md:p-5 rounded-2xl border-2 transition-all duration-200"
                         :class="paymentMethod === 'va' ? 'border-[#BFF108] bg-[#FDFEF8] shadow-sm' : 'border-[#E2E7FF] bg-white hover:border-[#CAD2FF]'">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 md:gap-4 flex-1 min-w-0">
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 mt-0.5 transition-all"
                                     :class="paymentMethod === 'va' ? 'border-[#1A1743] bg-white' : 'border-[#CAD2FF] bg-white'">
                                    <div class="w-2.5 h-2.5 bg-[#1A1743] rounded-full transition-all"
                                         :class="paymentMethod === 'va' ? 'opacity-100 scale-100' : 'opacity-0 scale-50'"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-[#1A1743] text-sm md:text-base mb-0.5">Transfer Bank / Virtual Account</div>
                                    <p class="text-xs text-[#717082]">BCA VA, Mandiri, BNI, BRI, Permata Bank</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </label>

                <!-- 3. E-Wallet Direct Debit -->
                <label class="cursor-pointer block">
                    <input type="radio" name="payment" value="ewallet" class="sr-only" x-model="paymentMethod">
                    <div class="p-4 md:p-5 rounded-2xl border-2 transition-all duration-200"
                         :class="paymentMethod === 'ewallet' ? 'border-[#BFF108] bg-[#FDFEF8] shadow-sm' : 'border-[#E2E7FF] bg-white hover:border-[#CAD2FF]'">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 md:gap-4 flex-1 min-w-0">
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 mt-0.5 transition-all"
                                     :class="paymentMethod === 'ewallet' ? 'border-[#1A1743] bg-white' : 'border-[#CAD2FF] bg-white'">
                                    <div class="w-2.5 h-2.5 bg-[#1A1743] rounded-full transition-all"
                                         :class="paymentMethod === 'ewallet' ? 'opacity-100 scale-100' : 'opacity-0 scale-50'"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-[#1A1743] text-sm md:text-base mb-0.5">E-Wallet Direct Debit</div>
                                    <p class="text-xs text-[#717082]">Notifikasi langsung ke GoPay, ShopeePay, atau OVO</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </label>

                <!-- 4. COD -->
                <label class="cursor-pointer block">
                    <input type="radio" name="payment" value="cod" class="sr-only" x-model="paymentMethod">
                    <div class="p-4 md:p-5 rounded-2xl border-2 transition-all duration-200"
                         :class="paymentMethod === 'cod' ? 'border-[#BFF108] bg-[#FDFEF8] shadow-sm' : 'border-[#E2E7FF] bg-white hover:border-[#CAD2FF]'">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 md:gap-4 flex-1 min-w-0">
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 mt-0.5 transition-all"
                                     :class="paymentMethod === 'cod' ? 'border-[#1A1743] bg-white' : 'border-[#CAD2FF] bg-white'">
                                    <div class="w-2.5 h-2.5 bg-[#1A1743] rounded-full transition-all"
                                         :class="paymentMethod === 'cod' ? 'opacity-100 scale-100' : 'opacity-0 scale-50'"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-[#1A1743] text-sm md:text-base mb-0.5">Bayar di Tempat (COD / Tunai)</div>
                                    <p class="text-xs text-[#717082]">Bayar tunai atau QR saat kurir mengantar pakaian bersih ke rumah</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </label>
            </div>

            <!-- Error message if any -->
            <p x-show="error" class="mt-4 text-sm font-semibold text-red-600 flex items-center gap-1.5">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span x-text="error"></span>
            </p>

            <!-- ====== TOMBOL KONFIRMASI & BAYAR ====== -->
            <button type="button"
                    @click="handleConfirmPayment()" 
                    :disabled="!hasAnySelected || submitting" 
                    class="disabled:opacity-50 disabled:cursor-not-allowed w-full mt-8 px-6 md:px-8 py-4 bg-[#BFF108] text-[#131B2E] font-extrabold rounded-2xl
                           flex items-center justify-between transition-all duration-300 ease-out hover:brightness-105 hover:-translate-y-0.5 shadow-sm">
                <span class="text-sm md:text-base">Konfirmasi &amp; Bayar</span>
                <span class="flex items-center gap-2">
                    <span class="text-base md:text-lg font-black" x-text="formatRp(totalBayar)"></span>
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
                    <span class="px-2 py-0.5 bg-[#C2F43B] rounded-full text-[11px] font-bold text-[#4E6700]">Otomatis</span>
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
                <div class="w-64 bg-white rounded-2xl p-4 shadow-sm border border-[#E2E7FF] flex flex-col items-center relative">
                    <!-- Red Corner Flags -->
                    <div class="absolute top-2 left-2 w-4 h-4 border-t-2 border-l-2 border-red-500 rounded-tl-sm"></div>
                    <div class="absolute bottom-2 right-2 w-4 h-4 border-b-2 border-r-2 border-red-500 rounded-br-sm"></div>

                    <!-- Header -->
                    <div class="text-[10px] font-extrabold uppercase tracking-wide text-center text-[#1A1743] mb-0.5">RODEO LAUNDRY</div>
                    <div class="text-[8px] text-[#717082] text-center mb-1">NMID: ID1026484259563</div>
                    <div class="text-[7px] text-[#717082] text-center mb-2">A01</div>

                    <!-- QR Code Render (High Res SVG simulation) -->
                    <div class="w-44 h-44 bg-white flex items-center justify-center p-1 border border-gray-100 rounded-lg">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=https%3A%2F%2Frodeolaundry.id%2Fqris%2Fdemo" 
                             alt="QR Code QRIS" 
                             class="w-full h-full object-contain">
                    </div>

                    <div class="text-[8px] text-[#717082] text-center mt-2">Dicetak oleh: 93600914</div>
                </div>

                <!-- Unduh QR button -->
                <button type="button" 
                        class="mt-3 px-4 py-1.5 bg-white border border-[#E2E7FF] rounded-lg text-xs font-bold text-[#1A1743] hover:bg-gray-50 flex items-center gap-1.5 shadow-2xs transition">
                    <i class="fa-solid fa-arrow-down-to-bracket text-xs"></i>
                    Unduh QR
                </button>
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
                    <span class="font-bold text-[#1A1743]">Rodeo Laundry Malang</span>
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
                <!-- Col 1 -->
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
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-[#BFF108]/20 border border-[#BFF108] rounded-full text-xs font-bold text-[#4E6700]">
                        <span class="w-2 h-2 rounded-full bg-[#4E6700]"></span>
                        Outlet Soekarno Hatta Malang
                    </div>
                </div>

                <!-- Col 2 -->
                <div>
                    <h4 class="font-bold text-xs md:text-sm uppercase tracking-wider mb-3 md:mb-4 text-[#1A1743]">Layanan Kami</h4>
                    <ul class="space-y-2 text-xs md:text-sm text-[#47464E]">
                        <li><a href="{{ route('pemesanan') }}" class="hover:text-[#1A1743] transition">Kiloan Premium</a></li>
                        <li><a href="{{ route('pemesanan') }}" class="hover:text-[#1A1743] transition">Dry Cleaning Satuan</a></li>
                        <li><a href="{{ route('pemesanan') }}" class="hover:text-[#1A1743] transition">Cuci Sepatu &amp; Tas</a></li>
                        <li><a href="{{ route('pemesanan') }}" class="hover:text-[#1A1743] transition">Bed Cover &amp; Karpet</a></li>
                        <li><a href="{{ route('pemesanan') }}" class="hover:text-[#1A1743] transition">Express 3 Jam</a></li>
                    </ul>
                </div>

                <!-- Col 3 -->
                <div>
                    <h4 class="font-bold text-xs md:text-sm uppercase tracking-wider mb-3 md:mb-4 text-[#1A1743]">Area &amp; Bantuan</h4>
                    <ul class="space-y-2 text-xs md:text-sm text-[#47464E]">
                        <li>Lowokwaru &amp; Soekarno Hatta</li>
                        <li>Klojen &amp; Sekitar Kampus UB/UM</li>
                        <li>Blimbing &amp; Sukun</li>
                        <li>Pertanyaan Umum (FAQ)</li>
                        <li>Syarat &amp; Ketentuan Garansi</li>
                    </ul>
                </div>

                <!-- Col 4 -->
                <div>
                    <h4 class="font-bold text-xs md:text-sm uppercase tracking-wider mb-3 md:mb-4 text-[#1A1743]">Operasional &amp; Kontak</h4>
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
            </div>

            <!-- Bottom Copyright & Badges -->
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