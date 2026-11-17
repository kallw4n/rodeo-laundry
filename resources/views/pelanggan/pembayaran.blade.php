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
<body class="bg-[#D8E1F0] text-[#1A1743] font-sans antialiased min-h-screen flex flex-col justify-between"
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
        paymentMethod: 'cod',
        submitting: false,
        error: '',
        
        showQrisModal: false,
        showTransferModal: false,
        copiedRekening: false,
        copiedNominal: false,
        transferSenderName: '',
        transferSenderBank: 'Bank Jatim',
        transferRef: '',
        orderNumber: '#RD-' + Math.floor(10000 + Math.random() * 90000),
        timer: 899,
        timerInterval: null,

        copyToClipboard(text, type = 'rekening') {
            const strText = String(text);
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(strText);
            } else {
                const textArea = document.createElement('textarea');
                textArea.value = strText;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                } catch (err) {}
                document.body.removeChild(textArea);
            }
            if (type === 'rekening') {
                this.copiedRekening = true;
                setTimeout(() => { this.copiedRekening = false; }, 2000);
            } else if (type === 'nominal') {
                this.copiedNominal = true;
                setTimeout(() => { this.copiedNominal = false; }, 2000);
            }
        },

        get waTransferLink() {
            const lines = [
                'Halo Admin Rodeo Laundry, saya ingin konfirmasi pembayaran Transfer Bank untuk pesanan ' + this.orderNumber + ':',
                '',
                '• Total Tagihan: ' + this.formatRp(this.totalBayar),
                '• Rekening Tujuan: Bank Jatim 0402667320 a.n Dzauq Bachrul \'Ulum',
                '• Nama Pengirim: ' + (this.transferSenderName || '-'),
                '• Bank Pengirim: ' + (this.transferSenderBank || 'Bank Jatim'),
            ];
            if (this.transferRef && this.transferRef.trim() !== '') {
                lines.push('• Catatan/Ref: ' + this.transferRef.trim());
            }
            lines.push('');
            lines.push('Mohon dicek dan diverifikasi ya min, terima kasih!');
            return 'https://wa.me/6281358055257?text=' + encodeURIComponent(lines.join('\n'));
        },

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

        init() {
            if (this.hasKiloan) {
                this.paymentMethod = 'cod';
            }
        },

        openTransferModal() {
            if (this.hasKiloan) return;
            this.paymentMethod = 'va';
            this.startTimer();
            this.showTransferModal = true;
        },

        openQrisModal() {
            if (this.hasKiloan) return;
            this.paymentMethod = 'qris';
            this.startTimer();
            this.showQrisModal = true;
        },

        handleConfirmPayment() {
            if (this.hasKiloan) {
                this.paymentMethod = 'cod';
            }
            if (this.paymentMethod === 'qris') {
                this.openQrisModal();
            } else if (this.paymentMethod === 'va') {
                this.openTransferModal();
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
            
            // Ambil catatan kurir dari halaman pengiriman
            if (this.courierNotes && this.courierNotes.trim() !== '') {
                notesArray.push(`Catatan Kurir: ${this.courierNotes.trim()}`);
            }

            // Ambil catatan transfer jika metode pembayaran VA / Transfer Bank
            if (this.paymentMethod === 'va') {
                const sender = (this.transferSenderName || '').trim();
                const bank = (this.transferSenderBank || '').trim();
                const ref = (this.transferRef || '').trim();
                let tfInfo = 'Transfer: Bank Jatim (0402667320 a.n Dzauq Bachrul \'Ulum)';
                if (sender) tfInfo += ` | Pengirim: ${sender}`;
                if (bank) tfInfo += ` via ${bank}`;
                if (ref) tfInfo += ` | Ref: ${ref}`;
                notesArray.push(tfInfo);
            }

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
                    const errData = await res.json().catch(() => ({}));
                    this.error = errData.message || 'Gagal membuat pesanan, silakan coba lagi.'; 
                    this.submitting = false; 
                    return;
                }

                ['cart_kiloan', 'cart_service_qty', 'cart_notes', 'cart_service_notes', 'cart_courier_notes', 'cart_pickup', 'cart_delivery', 'cart_address'].forEach(k => {
                    localStorage.removeItem(k);
                    localStorage.removeItem('_x_' + k);
                });
                this.kiloan = 0;
                this.serviceQty = {};
                this.serviceNotes = {};
                this.notes = '';
                this.courierNotes = '';
                this.cartAddress = '';
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
            let t = (Number(this.kiloan) || 0) * (Number(this.hargaKiloan) || 0);
            (this.services || []).forEach(s => t += (Number(s.price) || 0) * (Number(this.serviceQty[s.id]) || 0));
            return t;
        },
        get totalItems() {
            let count = (Number(this.kiloan) || 0) > 0 ? 1 : 0;
            (this.services || []).forEach(s => { count += (Number(this.serviceQty[s.id]) || 0); });
            return count;
        },
        get hasAnySelected() { return (Number(this.kiloan) || 0) > 0 || this.selectedServices.length > 0; },
        get hasKiloan() {
            if ((Number(this.kiloan) || 0) > 0) return true;
            return this.selectedServices.some(s => s.unit === 'kg');
        },
        formatRp(n) { 
            const val = Number(n) || 0;
            return 'Rp ' + Math.round(val).toLocaleString('id-ID'); 
        }
    }">

    <!-- ================= HEADER ================= -->
    <header class="fixed top-0 w-full h-16 md:h-20 bg-white/90 backdrop-blur-md shadow-sm z-40 border-b border-slate-200/80"
            x-data="{ mobileOpen: false }">
        <div class="max-w-[1440px] mx-auto h-full px-4 md:px-6 flex items-center justify-between">

            <!-- Logo -->
            <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                <img src="{{ asset('images/logo-rodeo.png') }}" alt="Logo Rodeo Laundry" width="44" height="44" class="w-10 h-10 md:w-11 md:h-11 rounded-full object-contain shadow-xs shrink-0 bg-white">
                <div>
                    <div class="text-base md:text-lg font-semibold leading-none">
                        Rodeo Laundry<span class="text-[#bff108ff]">.</span>
                    </div>
                    <div class="text-[9px] md:text-[10px] font-bold uppercase tracking-wider text-[#47464E] mt-1">Batu</div>
                </div>
            </a>

            <!-- Nav Desktop -->
            <nav class="hidden md:flex items-center gap-1.5 bg-[#E0E6FD] p-1.5 rounded-full border border-[#BAC7FA] shadow-inner">
                <a href="{{ url('/') }}" class="px-4 md:px-5 py-2 text-sm font-bold text-[#1A1743] hover:text-white hover:bg-[#1A1743] hover:shadow-md rounded-full transition-all duration-200 cursor-pointer active:scale-95">Beranda</a>
                <a href="{{ route('pemesanan') }}" class="inline-flex items-center gap-1.5 px-4 md:px-5 py-2 bg-[#1A1743] text-white rounded-full text-sm font-extrabold shadow-md ring-2 ring-[#BFF108]/50 transition-all duration-200 cursor-default">
                    <span class="w-2 h-2 rounded-full bg-[#BFF108] shadow-[0_0_8px_#BFF108] shrink-0"></span>
                    <span>Layanan</span>
                </a>
                <a href="{{ route('pesanan.index') }}" class="px-4 md:px-5 py-2 text-sm font-bold text-[#1A1743] hover:text-white hover:bg-[#1A1743] hover:shadow-md rounded-full transition-all duration-200 cursor-pointer active:scale-95">Pesanan</a>
            </nav>

            <!-- Right Side -->
            <div class="flex items-center gap-2 md:gap-3">
                <a href="https://wa.me/6281358055257" target="_blank" class="hidden sm:flex items-center gap-2 px-3 md:px-4 py-2 bg-[#EAEDFF] hover:bg-[#dfe4ff] hover:shadow-xs active:scale-95 rounded-full text-xs md:text-sm font-semibold text-[#131B2E] transition-all duration-200">
                    <i class="fa-brands fa-whatsapp text-sm md:text-base text-[#25D366]"></i> WhatsApp
                </a>

                @auth
                    <div class="flex items-center gap-2">
                        <a href="{{ route('profile.edit') }}" 
                           class="w-8 h-8 md:w-9 md:h-9 bg-[#1A1743] hover:bg-[#2F2D59] hover:scale-105 active:scale-95 text-white rounded-full flex items-center justify-center transition shadow-sm shrink-0" 
                           title="{{ Auth::user()->username ?? 'Profil Saya' }}">
                            <i class="fa-regular fa-user text-xs md:text-sm"></i>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-extrabold text-slate-700 bg-white hover:bg-rose-600 hover:text-white hover:border-rose-600 border border-slate-300 shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer active:scale-95" 
                                    title="Keluar dari akun">
                                <i class="fa-solid fa-arrow-right-from-bracket text-[11px]"></i>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>
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
                <a href="https://wa.me/6281358055257" target="_blank" class="flex items-center px-4 py-3 rounded-xl text-sm font-semibold text-[#47464E] hover:bg-[#F2F3FF] hover:text-[#1A1743] transition">
                    <i class="fa-brands fa-whatsapp w-5 text-center text-[#25D366] mr-2"></i> Hubungi WhatsApp
                </a>
                @auth
                    <form method="POST" action="{{ route('logout') }}" class="pt-2 border-t border-[#E2E7FF]">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-4 py-3 rounded-xl text-sm font-semibold text-red-600 hover:bg-red-50 transition">
                            <i class="fa-solid fa-arrow-right-from-bracket w-5 text-center text-red-500 mr-2"></i> Keluar
                        </button>
                    </form>
                @endauth
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
            <div class="flex items-center gap-1.5 md:gap-3 bg-white p-1.5 md:p-2 rounded-full shadow-sm border border-slate-300/80 self-start md:self-auto">
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
        <div class="bg-white rounded-[24px] md:rounded-[32px] p-4 md:p-8 lg:p-10 shadow-[0_10px_30px_-5px_rgba(26,23,67,0.08),0_4px_12px_rgba(0,0,0,0.03)] border border-slate-300/80">

            <!-- ====== RINGKASAN PESANAN ====== -->
            <div class="border border-slate-200/90 rounded-2xl md:rounded-3xl overflow-hidden mb-6 md:mb-8">
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
                        <div>
                            <span class="text-base md:text-lg font-bold text-[#1A1743]">Estimasi Total Biaya</span>
                            <span class="block text-[11px] text-[#717082]">*Tagihan final disesuaikan dengan hasil timbangan riil di outlet</span>
                        </div>
                        <span class="text-xl md:text-2xl font-extrabold text-[#1A1743] shrink-0" x-text="formatRp(totalBayar)"></span>
                    </div>

                    <!-- Notice Penimbangan & Estimasi Biaya -->
                    <div class="mt-4 p-4 rounded-2xl bg-amber-50/90 border border-amber-200/80 flex items-start gap-3.5 text-xs text-[#78350F] leading-relaxed shadow-xs">
                        <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs mt-0.5">
                            <i class="fa-solid fa-scale-balanced text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-bold text-amber-900 text-xs md:text-sm mb-0.5 flex items-center gap-1.5">
                                <span>Informasi Penimbangan Pakaian</span>
                            </div>
                            <p class="text-[11px] md:text-xs text-amber-800 leading-relaxed">
                                Total di atas adalah <span class="font-bold">estimasi awal</span>. Berat pakaian sebenarnya akan ditimbang secara presisi oleh tim kami saat pakaian tiba di outlet (atau saat Anda serahkan langsung ke kasir).
                                <span class="block mt-1">
                                    💡 <span class="font-semibold">Solusi Praktis:</span> Anda dapat memilih opsi <span class="font-bold underline" x-text="pickupMethod === 'courier' ? 'Jemput Dulu & Bayar Nanti' : 'Antar Sendiri & Bayar di Outlet'"></span> di bawah agar tagihan final menyesuaikan berat timbangan pasti tanpa selisih pembayaran di awal.
                                </span>
                            </p>
                        </div>
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

                <!-- 1. OPSI BAYAR NANTI (COD / PASCA-TIMBANG) -->
                <label class="cursor-pointer block">
                    <input type="radio" name="payment" value="cod" class="sr-only" x-model="paymentMethod">
                    <div class="p-4 md:p-5 rounded-2xl border-2 transition-all duration-200"
                         :class="paymentMethod === 'cod' ? 'border-[#BFF138] bg-[#FAF8FF] shadow-sm' : 'border-[#E2E7FF] bg-white hover:border-[#CAD2FF]'">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 md:gap-4 flex-1 min-w-0">
                                <!-- Radio Button Circle -->
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 mt-1 transition-all duration-200"
                                     :class="paymentMethod === 'cod' ? 'border-[#1A1743] bg-[#1A1743]' : 'border-[#CAD2FF] bg-white'">
                                    <div class="w-2 h-2 bg-white rounded-full transition-all duration-200"
                                         :class="paymentMethod === 'cod' ? 'opacity-100 scale-100' : 'opacity-0 scale-50'"></div>
                                </div>
                                <div class="w-10 h-10 md:w-11 md:h-11 bg-emerald-50 text-emerald-700 rounded-xl flex items-center justify-center shrink-0 border border-emerald-100">
                                    <i :class="pickupMethod === 'courier' ? 'fa-solid fa-truck-ramp-box text-base md:text-lg' : 'fa-solid fa-store text-base md:text-lg'"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap mb-1">
                                        <span class="font-bold text-[#1A1743] text-sm md:text-base"
                                              x-text="pickupMethod === 'courier' ? 'Jemput Dulu & Bayar Nanti (COD / Pasca-Timbang)' : 'Antar Sendiri & Bayar di Outlet (Pasca-Timbang)'"></span>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#BFF138] text-[#131B2E]">
                                            Rekomendasi
                                        </span>
                                    </div>
                                    <p class="text-xs text-[#47464E] leading-relaxed"
                                       x-text="pickupMethod === 'courier' 
                                            ? 'Kurir akan menjemput pakaian Anda terlebih dahulu. Pembayaran (Tunai/QRIS) dilakukan setelah cucian ditimbang di outlet atau saat pakaian bersih diantar kembali.' 
                                            : 'Bawa pakaian Anda langsung ke outlet Rodeo Laundry. Berat akan ditimbang bersama Anda di kasir, dan pembayaran dapat langsung dilakukan di tempat (Tunai/QRIS) sesuai berat pasti.'"></p>
                                </div>
                            </div>
                            <span class="hidden sm:inline-flex items-center gap-1 px-3 py-1 border border-emerald-200 rounded-lg text-xs font-bold text-emerald-800 shrink-0 bg-emerald-50/60">
                                <i class="fa-solid fa-hand-holding-dollar text-xs"></i>
                                <span x-text="pickupMethod === 'courier' ? 'COD / Bayar Nanti' : 'Bayar di Outlet'"></span>
                            </span>
                        </div>
                    </div>
                </label>

                <!-- 2. QRIS -->
                <label :class="hasKiloan ? 'cursor-not-allowed opacity-60' : 'cursor-pointer'" class="block">
                    <input type="radio" name="payment" value="qris" class="sr-only" x-model="paymentMethod" :disabled="hasKiloan">
                    <div class="p-4 md:p-5 rounded-2xl border-2 transition-all duration-200"
                         :class="hasKiloan ? 'border-dashed border-slate-300 bg-slate-50' : (paymentMethod === 'qris' ? 'border-[#BFF138] bg-[#FAF8FF] shadow-sm' : 'border-[#E2E7FF] bg-white hover:border-[#CAD2FF]')">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 md:gap-4 flex-1 min-w-0">
                                <!-- Radio Button Circle -->
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 mt-1 transition-all duration-200"
                                     :class="hasKiloan ? 'border-slate-300 bg-slate-200' : (paymentMethod === 'qris' ? 'border-[#1A1743] bg-[#1A1743]' : 'border-[#CAD2FF] bg-white')">
                                    <div class="w-2 h-2 bg-white rounded-full transition-all duration-200"
                                         :class="paymentMethod === 'qris' && !hasKiloan ? 'opacity-100 scale-100' : 'opacity-0 scale-50'"></div>
                                </div>
                                <div class="w-10 h-10 md:w-11 md:h-11 rounded-xl flex items-center justify-center shrink-0 shadow-sm"
                                     :class="hasKiloan ? 'bg-slate-200 text-slate-400' : 'bg-[#1A1743] text-[#BFF138]'">
                                    <i class="fa-solid fa-qrcode text-lg"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap mb-1">
                                        <span class="font-bold text-[#1A1743] text-sm md:text-base">QRIS (Semua E-Wallet &amp; M-Banking)</span>
                                        <template x-if="hasKiloan">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200 flex items-center gap-1 shrink-0">
                                                <i class="fa-solid fa-lock text-[9px]"></i> Terkunci untuk Kiloan
                                            </span>
                                        </template>
                                    </div>
                                    <p class="text-xs text-[#47464E]"
                                       x-text="hasKiloan ? 'Metode bayar di depan dikunci khusus kiloan karena berat pakaian (seperti 2.7 kg) wajib ditimbang resmi di outlet/kasir.' : 'BCA Mobile, Livin\' by Mandiri, Wondr by BNI, BRImo, GoPay, OVO, ShopeePay, DANA'"></p>
                                </div>
                            </div>
                            <div class="hidden sm:flex items-center gap-2 shrink-0">
                                <button type="button"
                                        x-show="!hasKiloan"
                                        @click.prevent.stop="openQrisModal()"
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-white hover:bg-[#EAEDFF] text-[#1A1743] border border-[#CAD2FF] rounded-lg text-xs font-bold transition shadow-2xs cursor-pointer">
                                    <i class="fa-solid fa-qrcode text-xs text-[#1A1743]"></i>
                                    <span>Lihat QR</span>
                                </button>
                                <span class="inline-flex items-center gap-1 px-3 py-1 border border-[#E2E7FF] rounded-lg text-xs font-bold text-[#1A1743] bg-white">
                                    QRIS
                                </span>
                            </div>
                        </div>
                    </div>
                </label>

                <!-- 3. Transfer Bank / VA -->
                <label :class="hasKiloan ? 'cursor-not-allowed opacity-60' : 'cursor-pointer'"
                       @click="if (!hasKiloan) paymentMethod = 'va'"
                       class="block">
                    <input type="radio" name="payment" value="va" class="sr-only" x-model="paymentMethod" :disabled="hasKiloan">
                    <div class="p-4 md:p-5 rounded-2xl border-2 transition-all duration-200"
                         :class="hasKiloan ? 'border-dashed border-slate-300 bg-slate-50' : (paymentMethod === 'va' ? 'border-[#BFF138] bg-[#FAF8FF] shadow-sm' : 'border-[#E2E7FF] bg-white hover:border-[#CAD2FF]')">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 md:gap-4 flex-1 min-w-0">
                                <!-- Radio Button Circle -->
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 mt-1 transition-all duration-200"
                                     :class="hasKiloan ? 'border-slate-300 bg-slate-200' : (paymentMethod === 'va' ? 'border-[#1A1743] bg-[#1A1743]' : 'border-[#CAD2FF] bg-white')">
                                    <div class="w-2 h-2 bg-white rounded-full transition-all duration-200"
                                         :class="paymentMethod === 'va' && !hasKiloan ? 'opacity-100 scale-100' : 'opacity-0 scale-50'"></div>
                                </div>
                                <div class="w-10 h-10 md:w-11 md:h-11 rounded-xl flex items-center justify-center shrink-0"
                                     :class="hasKiloan ? 'bg-slate-200 text-slate-400' : 'bg-[#EAEDFF] text-[#1A1743]'">
                                    <i class="fa-solid fa-building-columns text-base md:text-lg"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap mb-1">
                                        <p class="font-bold text-[#1A1743] text-sm md:text-base">Transfer Bank (Bank Jatim)</p>
                                        <template x-if="hasKiloan">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200 flex items-center gap-1 shrink-0">
                                                <i class="fa-solid fa-lock text-[9px]"></i> Terkunci untuk Kiloan
                                            </span>
                                        </template>
                                        <template x-if="!hasKiloan">
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center gap-1 shrink-0">
                                                <i class="fa-solid fa-check text-[9px]"></i> Bank Jatim
                                            </span>
                                        </template>
                                    </div>
                                    <p class="text-xs text-[#47464E]"
                                       x-text="hasKiloan ? 'Metode bayar di depan dikunci khusus kiloan karena berat pakaian (seperti 2.7 kg) wajib ditimbang resmi di outlet/kasir.' : 'Bank Jatim 0402667320 a.n Dzauq Bachrul \'Ulum (Bisa dari m-Banking / ATM semua bank)'"></p>
                                </div>
                            </div>
                            <div class="hidden sm:flex items-center gap-2 shrink-0">
                                <button type="button"
                                        x-show="!hasKiloan"
                                        @click.prevent.stop="openTransferModal()"
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-white hover:bg-[#EAEDFF] text-[#1A1743] border border-[#CAD2FF] rounded-lg text-xs font-bold transition shadow-2xs cursor-pointer">
                                    <i class="fa-solid fa-eye text-xs text-[#4E6700]"></i>
                                    <span>Lihat Rekening</span>
                                </button>
                                <span class="inline-flex items-center gap-1 px-3 py-1 border border-[#E2E7FF] rounded-lg text-xs font-bold text-[#1A1743] bg-white">
                                    <i class="fa-solid fa-building-columns text-xs text-[#4E6700]"></i> Bank Jatim
                                </span>
                            </div>
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
                            <span x-text="paymentMethod === 'cod' ? (pickupMethod === 'courier' ? 'Konfirmasi Jemput & Bayar Nanti' : 'Konfirmasi Antar Sendiri & Bayar di Outlet') : 'Konfirmasi & Bayar Sekarang'"></span>
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

    <!-- ================= MODAL SIMULASI TRANSFER BANK ================= -->
    <div x-show="showTransferModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-[#0E0E1B]/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 overflow-y-auto"
         style="display: none;">
        
        <div @click.outside="showTransferModal = false"
             class="w-full max-w-[480px] bg-white rounded-[28px] p-6 sm:p-7 shadow-2xl relative text-[#1A1743] my-8 max-h-[92vh] overflow-y-auto">

            <!-- Close Button -->
            <button @click="showTransferModal = false"
                    class="absolute top-5 right-5 text-gray-400 hover:text-gray-700 transition w-8 h-8 rounded-full flex items-center justify-center bg-gray-100 cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>

            <!-- Modal Header -->
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <div class="w-8 h-8 rounded-xl bg-[#1A1743] text-[#BFF138] flex items-center justify-center text-xs shadow-xs">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                    <h3 class="text-xl font-extrabold text-[#1A1743]">Transfer Bank</h3>
                    <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-[11px] font-extrabold">Bank Jatim</span>
                </div>
                <p class="text-xs text-[#717082]">Silakan transfer sesuai total tagihan ke rekening resmi Rodeo Laundry di bawah ini</p>
            </div>

            <!-- Countdown Banner -->
            <div class="bg-[#EAEDFF]/70 rounded-xl px-4 py-2.5 flex items-center justify-between text-xs font-semibold text-[#1A1743] mt-4">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-hourglass-half text-[#1A1743]"></i>
                    <span>Selesaikan transfer dalam</span>
                </div>
                <div class="font-extrabold tracking-wider" x-text="formattedTimer">14 : 59</div>
            </div>

            <!-- Bank Account Card -->
            <div class="bg-gradient-to-br from-[#1A1743] to-[#2B276B] rounded-2xl p-5 text-white shadow-md mt-4 relative overflow-hidden">
                <!-- Watermark shape -->
                <div class="absolute -right-6 -bottom-6 w-32 h-32 rounded-full bg-white/5 pointer-events-none"></div>

                <div class="flex items-center justify-between gap-2 mb-3">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-lg bg-white/15 text-white text-xs font-black tracking-wider uppercase border border-white/10">
                            Bank Jatim
                        </span>
                        <span class="text-[10px] text-white/70">Cabang Batu / Malang</span>
                    </div>
                    <span class="text-[10px] font-bold text-[#BFF138] flex items-center gap-1">
                        <i class="fa-solid fa-shield-halved"></i> Rekening Resmi
                    </span>
                </div>

                <div class="text-[11px] text-white/75 font-medium uppercase tracking-wider mb-1">Nomor Rekening Tujuan</div>
                <div class="flex items-center justify-between gap-3 bg-white/10 rounded-xl px-3.5 py-2.5 backdrop-blur-xs border border-white/10">
                    <span class="font-mono text-xl sm:text-2xl font-black tracking-widest text-[#BFF138]">0402667320</span>
                    <button type="button"
                            @click="copyToClipboard('0402667320', 'rekening')"
                            class="px-3 py-1.5 rounded-lg text-xs font-extrabold transition flex items-center gap-1.5 cursor-pointer shadow-xs active:scale-95"
                            :class="copiedRekening ? 'bg-emerald-500 text-white' : 'bg-white text-[#1A1743] hover:bg-slate-100'">
                        <i :class="copiedRekening ? 'fa-solid fa-check' : 'fa-regular fa-copy'"></i>
                        <span x-text="copiedRekening ? 'Tersalin!' : 'Salin'"></span>
                    </button>
                </div>

                <div class="mt-3 flex items-center justify-between text-xs pt-2.5 border-t border-white/10">
                    <div>
                        <div class="text-[10px] text-white/70 uppercase tracking-wider">Atas Nama (a.n)</div>
                        <div class="font-bold text-sm text-white tracking-wide">Dzauq Bachrul 'Ulum</div>
                    </div>
                    <div class="text-right">
                        <div class="text-[10px] text-white/70 uppercase tracking-wider">Jaringan</div>
                        <div class="font-bold text-xs text-white">m-Banking / ATM / BI-FAST</div>
                    </div>
                </div>
            </div>

            <!-- Details Section (Nominal & Order) -->
            <div class="mt-4 p-3.5 bg-[#FAF8FF] border border-[#E2E7FF] rounded-2xl space-y-2 text-xs">
                <div class="flex justify-between items-center">
                    <span class="text-[#717082] font-medium">Total yang Harus Ditransfer</span>
                    <div class="flex items-center gap-2">
                        <span class="text-base font-black text-[#1A1743]" x-text="formatRp(totalBayar)"></span>
                        <button type="button"
                                @click="copyToClipboard(totalBayar, 'nominal')"
                                class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-white border border-[#CAD2FF] text-[#1A1743] hover:bg-[#EAEDFF] cursor-pointer"
                                :title="'Salin nominal ' + totalBayar">
                            <span x-text="copiedNominal ? 'Tersalin' : 'Salin'"></span>
                        </button>
                    </div>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-[#E2E7FF]/70">
                    <span class="text-[#717082]">Nomor Pesanan</span>
                    <span class="font-bold text-[#1A1743]" x-text="orderNumber"></span>
                </div>
            </div>

            <!-- Section Input Pelanggan ("tolong ditambahkan ya buat inputannya") -->
            <div class="mt-4 pt-3.5 border-t border-slate-100 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-extrabold text-[#1A1743] flex items-center gap-1.5">
                        <i class="fa-solid fa-pen-to-square text-[#4E6700]"></i>
                        <span>Konfirmasi Data Pengirim</span>
                    </label>
                    <span class="text-[10px] text-slate-500 font-semibold">(Untuk pencocokan mutasi)</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Nama Pemilik Rekening</label>
                        <input type="text"
                               x-model="transferSenderName"
                               placeholder="Nama pengirim di buku rekening"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-[#1A1743] placeholder-slate-400 focus:outline-none focus:bg-white focus:border-[#1A1743] focus:ring-1 focus:ring-[#1A1743]">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Bank yang Digunakan</label>
                        <select x-model="transferSenderBank"
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-[#1A1743] focus:outline-none focus:bg-white focus:border-[#1A1743] focus:ring-1 focus:ring-[#1A1743]">
                            <option value="Bank Jatim">Bank Jatim</option>
                            <option value="BCA">BCA (Bank Central Asia)</option>
                            <option value="Mandiri">Bank Mandiri</option>
                            <option value="BRI">Bank BRI</option>
                            <option value="BNI">Bank BNI</option>
                            <option value="BSI">BSI (Bank Syariah Indonesia)</option>
                            <option value="SeaBank">SeaBank</option>
                            <option value="Bank Jago">Bank Jago</option>
                            <option value="DANA / E-Wallet">DANA / E-Wallet</option>
                            <option value="Lainnya">Bank Lainnya</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Catatan / No. Referensi Transfer <span class="text-slate-400 font-normal">(Opsional)</span></label>
                    <input type="text"
                           x-model="transferRef"
                           placeholder="Contoh: No Ref 123456 / transfer via Livin"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-[#1A1743] placeholder-slate-400 focus:outline-none focus:bg-white focus:border-[#1A1743] focus:ring-1 focus:ring-[#1A1743]">
                </div>
            </div>

            <!-- Action Button -->
            <button type="button"
                    @click="checkout()"
                    :disabled="submitting"
                    class="w-full bg-[#BFF108] hover:brightness-105 text-[#131B2E] font-extrabold rounded-2xl py-3.5 flex items-center justify-center gap-2 mt-5 transition shadow-sm cursor-pointer active:scale-98">
                <template x-if="submitting">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin"></i>
                        Memproses Pesanan Anda...
                    </span>
                </template>
                <template x-if="!submitting">
                    <span class="flex items-center gap-2">
                        <span>Saya Sudah Transfer &amp; Selesaikan Pesanan</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </span>
                </template>
            </button>

            <!-- WA Confirmation Button -->
            <a :href="waTransferLink"
               target="_blank"
               class="w-full bg-[#EAEDFF] hover:bg-[#dfe4ff] text-[#1A1743] font-bold rounded-2xl py-3 flex items-center justify-center gap-2 mt-2.5 transition text-xs shadow-2xs">
                <i class="fa-brands fa-whatsapp text-base text-[#25D366]"></i>
                <span>Kirim Bukti Transfer ke WhatsApp Admin</span>
            </a>

            <!-- Cancel Button -->
            <button type="button"
                    @click="showTransferModal = false"
                    class="w-full text-center text-xs font-semibold text-[#717082] hover:text-[#1A1743] mt-3 transition cursor-pointer">
                Batal / Ubah Metode Pembayaran
            </button>
        </div>
    </div>

    <!-- ================= FOOTER ================= -->
    <footer class="bg-[#17143A] text-white border-t border-[#2A2655] mt-12 md:mt-16">
        <div class="max-w-[1440px] mx-auto px-4 md:px-6 py-12 md:py-16">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 mb-10 md:mb-12">
                <div>
                    <div class="flex items-center gap-2.5 mb-4">
                        <img src="{{ asset('images/logo-rodeo.png') }}" alt="Logo Rodeo Laundry" width="44" height="44" class="w-10 h-10 md:w-11 md:h-11 rounded-full object-contain shadow-lg shrink-0 bg-white p-0.5">
                        <span class="text-lg md:text-xl font-bold tracking-tight text-white">Rodeo Laundry<span class="text-[#BFF108]">.</span></span>
                    </div>
                    <p class="text-slate-300 text-xs md:text-sm mb-5 leading-relaxed">
                        Laundry profesional, cepat, bersih, dan wangi di Batu. Layanan antar-jemput ekspres untuk kenyamanan pakaian higienis Anda.
                    </p>
                </div>

                <div>
                    <h4 class="font-bold text-xs md:text-sm uppercase tracking-wider mb-3 md:mb-4 text-white">Sitemap</h4>
                    <ul class="space-y-2.5 text-xs md:text-sm text-slate-300">
                        <li><a href="{{ url('/') }}" class="hover:text-[#BFF108] transition">Beranda</a></li>
                        <li><a href="{{ route('pemesanan') }}" class="hover:text-[#BFF108] transition">Layanan</a></li>
                        <li><a href="{{ route('pesanan.index') }}" class="hover:text-[#BFF108] transition">Pesanan Saya</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-xs md:text-sm uppercase tracking-wider mb-3 md:mb-4 text-white">Layanan Kami</h4>
                    <ul class="space-y-2.5 text-xs md:text-sm text-slate-300">
                        <li><a href="{{ route('pemesanan') }}" class="hover:text-[#BFF108] transition">Cuci Setrika Kiloan</a></li>
                        <li><a href="{{ route('pemesanan') }}" class="hover:text-[#BFF108] transition">Cuci Gorden</a></li>
                        <li><a href="{{ route('pemesanan') }}" class="hover:text-[#BFF108] transition">Cuci Boneka & Bantal</a></li>
                        <li><a href="{{ route('pemesanan') }}" class="hover:text-[#BFF108] transition">Cuci Sepatu Premium</a></li>
                        <li><a href="{{ route('pemesanan') }}" class="hover:text-[#BFF108] transition">Cuci Tas & Ransel</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-xs md:text-sm uppercase tracking-wider mb-3 md:mb-4 text-white">Kontak & Area</h4>
                    <div class="space-y-2.5 text-xs md:text-sm text-slate-300">
                        <p class="flex items-start gap-2.5">
                            <i class="fa-solid fa-location-dot text-[#BFF108] mt-1 shrink-0"></i>
                            <span>Gg. Rodeo, RT.04/RW.10, Sumberejo, Kec. Batu, Kota Batu, Jawa Timur 65318</span>
                        </p>
                        <p class="flex items-center gap-2.5">
                            <i class="fa-regular fa-clock text-[#BFF108] shrink-0"></i>
                            <span>Buka Setiap Hari: 07.00 - 21.00 WIB</span>
                        </p>
                        <p class="flex items-center gap-2.5">
                            <i class="fa-brands fa-whatsapp text-[#25D366] shrink-0"></i>
                            <span class="text-white font-medium">WhatsApp: +62 813-5805-5257</span>
                        </p>
                        <div class="mt-3 pt-3 border-t border-white/10">
                            <p class="flex items-center gap-2 text-white font-semibold mb-1">
                                <i class="fa-solid fa-truck-fast text-[#BFF108]"></i> Area Layanan Antar-Jemput:
                            </p>
                            <p class="text-xs text-slate-400">Gratis ongkir khusus melayani Area Kota Malang & Kota Batu.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-white/10 flex flex-col md:flex-row justify-between items-center gap-3 md:gap-4 text-[10px] md:text-xs text-slate-400">
                <p class="text-center md:text-left">© 2025 Rodeo Laundry. Hak Cipta Dilindungi Undang-Undang.</p>
                <div class="flex gap-4 sm:gap-6 flex-wrap justify-center">
                    <span class="flex items-center gap-1.5 font-bold text-white">
                        <i class="fa-solid fa-shield-halved text-[#BFF108]"></i> Garansi Bersih Higienis
                    </span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>