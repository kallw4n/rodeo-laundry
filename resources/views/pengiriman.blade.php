<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengiriman - Rodeo Laundry Malang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
    window.pengirimanData = {
        services: @json($services),
        kiloanService: @json($kiloanService),
    };
    </script>
</head>
<body class="bg-[#FAF8FF] text-[#1A1743] font-sans antialiased pb-40 md:pb-36"
      x-data="{
        ...window.pengirimanData,

        kiloan: $persist(0).as('cart_kiloan'),
        serviceQty: $persist({}).as('cart_service_qty'),
        courierNotes: $persist('').as('cart_courier_notes'),

        selectedAddress: 1,
        addresses: [
            {
                id: 1,
                label: 'Rumah Utama',
                isDefault: true,
                name: 'Fahri Haikal',
                phone: '0822-8840-8699',
                address: 'Jl. Soekarno Hatta No. 45, Lowokwaru',
                detail: 'Kecamatan Lowokwaru, Kota Malang, Jawa Timur 65141 (Depan Ruko Niaga Graha Polinema)',
            },
            {
                id: 2,
                label: 'Kantor',
                isDefault: false,
                name: 'Fahri Haikal',
                phone: '0822-8840-8699',
                address: 'Gedung Coworking Lantai 2, Jl. Ijen No. 12, Klojen',
                detail: 'Kecamatan Klojen, Kota Malang 65115 (Titip Resepsionis / Lobby Barat)',
            },
        ],

        pickupMethod: 'courier',
        deliveryMethod: 'courier',

        quickNotes: [
            'Titip di Satpam',
            'Pagar Hitam',
            'Hubungi WA Dulu',
            'Jangan Bunyikan Bel',
        ],
        addQuickNote(text) {
            if (this.courierNotes.length > 0 && !this.courierNotes.endsWith(' ')) {
                this.courierNotes += ', ';
            }
            this.courierNotes += text;
        },

        get hargaKiloan() { return this.kiloanService?.price ?? 0; },
        get kiloanName() { return this.kiloanService?.name ?? 'Cuci Kiloan'; },
        get kiloanExists() { return this.kiloanService !== null; },
        getService(id) { return this.services.find(s => s.id === id); },
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
        get selectedServices() {
            return this.services.filter(s => (this.serviceQty[s.id] || 0) > 0);
        },
        get hasAnySelected() { return this.kiloan > 0 || this.selectedServices.length > 0; },
        formatRp(n) { return 'Rp ' + n.toLocaleString('id-ID'); }
      }">

    <!-- ================= HEADER ================= -->
    <header class="fixed top-0 w-full h-16 md:h-20 bg-[#FAF8FF]/85 backdrop-blur-md shadow-sm z-50"
            x-data="{ mobileOpen: false }">
        <div class="max-w-[1440px] mx-auto h-full px-4 md:px-6 flex items-center justify-between">

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

            <nav class="hidden md:flex items-center gap-1 bg-[#F2F3FF] p-1.5 rounded-full">
                <a href="{{ url('/') }}" class="px-4 py-1.5 text-sm font-semibold text-[#47464E] hover:text-[#1A1743] rounded-full">Beranda</a>
                <a href="{{ route('pemesanan') }}" class="px-4 py-1.5 bg-[#EAEDFF] rounded-full text-sm font-bold">Layanan</a>
                <a href="{{ route('pesanan.index') }}" class="px-4 py-1.5 text-sm font-semibold text-[#47464E] hover:text-[#1A1743]">Pesanan</a>
            </nav>

            <div class="flex items-center gap-2 md:gap-3">
                <a href="#" class="hidden sm:flex items-center gap-2 px-3 md:px-4 py-2 bg-[#EAEDFF] rounded-full text-xs md:text-sm font-semibold text-[#131B2E]">
                    <div class="w-3 h-3 md:w-4 md:h-4 bg-[#4E6700]"></div> WhatsApp
                </a>

                <div class="w-8 h-8 bg-[#1A1743] rounded-full flex items-center justify-center">
                    <div class="w-3 h-3 bg-white"></div>
                </div>

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

    <main class="pt-24 md:pt-28 max-w-[1440px] mx-auto px-4 md:px-6">

        <!-- ================= HERO + STEPPER ================= -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 md:gap-6 mb-6 md:mb-8">
            <div>
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-[#C2F43B]/50 text-[#4E6700] text-[10px] md:text-xs font-bold uppercase rounded-full mb-3">
                    <span class="w-2 h-2 bg-[#4E6700] rounded-full"></span> Layanan On-Demand Malang
                </span>
                <h1 class="text-2xl md:text-4xl lg:text-5xl font-extrabold leading-tight mb-2 md:mb-3">Jadwal & Pengiriman</h1>
                <p class="text-xs md:text-base text-[#47464E] max-w-lg">Pilih alamat, metode penjemputan, dan cara pengantaran cucian Anda.</p>
            </div>

            <!-- Stepper -->
            <div class="flex items-center gap-1.5 md:gap-3 bg-white p-1.5 md:p-2 rounded-full shadow-sm border border-[#E2E7FF]/60 self-start md:self-auto">
                <a href="{{ route('pemesanan') }}" class="flex items-center gap-1.5 md:gap-2 px-2 md:px-3 py-1.5 md:py-2 rounded-full hover:bg-[#F2F3FF] transition">
                    <div class="w-4 h-4 bg-[#1A1743] rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <span class="hidden md:inline text-sm font-semibold text-[#47464E]">Pilih Layanan</span>
                </a>
                <div class="w-3 md:w-6 h-px bg-[#E2E7FF]"></div>
                <div class="flex items-center gap-1.5 md:gap-2 px-2.5 md:px-4 py-1.5 md:py-2 bg-[#1A1743] text-white rounded-full">
                    <div class="w-4 h-4 bg-[#BFF138] rounded-full shrink-0"></div>
                    <span class="text-xs md:text-sm font-bold">Pengiriman</span>
                </div>
                <div class="w-3 md:w-6 h-px bg-[#E2E7FF]"></div>
                <div class="flex items-center gap-1.5 md:gap-2 px-2 md:px-3 py-1.5 md:py-2 rounded-full">
                    <span class="w-5 h-5 rounded-full bg-[#F2F3FF] text-[#47464E] text-[10px] font-bold flex items-center justify-center shrink-0">3</span>
                    <span class="hidden md:inline text-sm font-semibold text-[#47464E]">Pembayaran</span>
                </div>
            </div>
        </div>

        <!-- ================= SECTION 1: ALAMAT ================= -->
        <div class="bg-white rounded-[24px] md:rounded-[32px] p-4 md:p-8 shadow-sm border border-[#E2E7FF]/60 mb-4 md:mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 md:gap-4 mb-4 md:mb-6">
                <div class="flex gap-3 md:gap-4 items-start">
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-[#EAEDFF] rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base md:text-lg font-bold">Alamat Penjemputan</h2>
                        <p class="text-xs md:text-sm text-[#47464E]">Pilih alamat kurir menjemput cucian kotor Anda</p>
                    </div>
                </div>
                <button class="px-3 md:px-4 py-2 bg-[#F2F3FF] hover:bg-[#EAEDFF] text-[#1A1743] rounded-full text-xs md:text-sm font-bold transition self-start sm:self-auto">
                    + Alamat Baru
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="addr in addresses" :key="addr.id">
                    <label class="block cursor-pointer">
                        <input type="radio" name="address" class="peer sr-only" 
                               :value="addr.id" x-model="selectedAddress">
                        <div class="p-4 md:p-5 rounded-2xl border-2 transition-all
                                    border-[#E2E7FF] bg-white
                                    peer-checked:border-[#BFF138] peer-checked:bg-[#FAF8FF]">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3 md:gap-4">
                                <div class="flex gap-3 flex-1 min-w-0">
                                    <div class="w-5 h-5 rounded-full border-2 border-[#E2E7FF] flex items-center justify-center shrink-0 mt-1
                                                peer-checked:border-[#1A1743] peer-checked:bg-[#1A1743]">
                                        <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                                            <span class="px-2 md:px-2.5 py-0.5 bg-[#EAEDFF] rounded-full text-[10px] md:text-xs font-bold text-[#1A1743]" x-text="addr.label"></span>
                                            <template x-if="addr.isDefault">
                                                <span class="px-2 md:px-2.5 py-0.5 bg-[#C2F43B] rounded-full text-[10px] font-bold text-[#4E6700] uppercase">Utama</span>
                                            </template>
                                        </div>
                                        <h3 class="text-sm md:text-base font-bold mb-1" x-text="addr.address"></h3>
                                        <p class="text-[10px] md:text-xs text-[#47464E] mb-2" x-text="addr.detail"></p>
                                        <div class="flex flex-wrap items-center gap-3 md:gap-4 text-[10px] md:text-xs text-[#47464E]">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                <span x-text="addr.name"></span>
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                <span x-text="addr.phone"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <button class="px-3 py-1.5 bg-[#F2F3FF] hover:bg-[#EAEDFF] text-[#1A1743] rounded-full text-[10px] md:text-xs font-bold transition shrink-0 self-start">
                                    Ubah Alamat
                                </button>
                            </div>
                        </div>
                    </label>
                </template>
            </div>

            <div class="mt-4 md:mt-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-3 md:px-4 py-3 bg-[#F2F3FF] rounded-2xl">
                <div class="flex items-start gap-2 md:gap-3">
                    <svg class="w-4 h-4 text-[#1A1743] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-[10px] md:text-sm text-[#47464E]">Punya lokasi kost, apartemen, atau vila lain di Malang?</p>
                </div>
                <button class="text-xs md:text-sm font-bold text-[#1A1743] hover:underline self-start sm:self-auto shrink-0">Tambahkan →</button>
            </div>
        </div>

        <!-- ================= SECTION 2: METODE PENJEMPUTAN ================= -->
        <div class="bg-white rounded-[24px] md:rounded-[32px] p-4 md:p-8 shadow-sm border border-[#E2E7FF]/60 mb-4 md:mb-6">
            <div class="flex gap-3 md:gap-4 items-start mb-4 md:mb-6">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#EAEDFF] rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </div>
                <div>
                    <h2 class="text-base md:text-lg font-bold">Metode Penjemputan</h2>
                    <p class="text-xs md:text-sm text-[#47464E]">Pilih cara penyerahan pakaian kotor Anda ke Rodeo</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                <!-- Dijemput Kurir -->
                <label class="cursor-pointer">
                    <input type="radio" name="pickup" value="courier" class="peer sr-only" x-model="pickupMethod">
                    <div class="p-4 md:p-5 rounded-2xl border-2 h-full transition-all
                                border-[#E2E7FF] bg-white
                                peer-checked:border-[#BFF138] peer-checked:bg-[#FAF8FF]">
                        <div class="flex justify-between items-start mb-3 gap-2">
                            <div class="w-9 h-9 md:w-10 md:h-10 bg-[#BFF138] rounded-xl flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 md:w-5 md:h-5 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            </div>
                            <div class="flex gap-1.5 md:gap-2 flex-wrap">
                                <span class="px-2 md:px-2.5 py-0.5 bg-[#1A1743] text-white rounded-full text-[10px] font-bold">Populer</span>
                                <span class="px-2 md:px-2.5 py-0.5 bg-[#C2F43B] text-[#4E6700] rounded-full text-[10px] font-bold">Gratis</span>
                            </div>
                        </div>
                        <h3 class="text-sm md:text-base font-bold mb-1">Dijemput Kurir Rodeo</h3>
                        <p class="text-xs text-[#47464E] mb-3 md:mb-4">Estimasi tiba 20-30 menit. Kurir kami membawa laundry bag steril khusus anti-kontaminasi langsung ke pintu Anda.</p>
                        <div class="flex justify-between items-center pt-3 border-t border-[#E2E7FF]/60 text-[10px] md:text-xs gap-2">
                            <span class="flex items-center gap-1 text-[#4E6700] font-bold">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                Respon Cepat
                            </span>
                            <span class="font-bold text-[#1A1743]">Rp 0</span>
                        </div>
                    </div>
                </label>

                <!-- Antar Sendiri -->
                <label class="cursor-pointer">
                    <input type="radio" name="pickup" value="self" class="peer sr-only" x-model="pickupMethod">
                    <div class="p-4 md:p-5 rounded-2xl border-2 h-full transition-all
                                border-[#E2E7FF] bg-white
                                peer-checked:border-[#BFF138] peer-checked:bg-[#FAF8FF]">
                        <div class="flex justify-between items-start mb-3 gap-2">
                            <div class="w-9 h-9 md:w-10 md:h-10 bg-[#EAEDFF] rounded-xl flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 md:w-5 md:h-5 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <span class="px-2 md:px-2.5 py-0.5 bg-[#EAEDFF] text-[#1A1743] rounded-full text-[10px] font-bold shrink-0">Drop & Go</span>
                        </div>
                        <h3 class="text-sm md:text-base font-bold mb-1">Antar Sendiri ke Outlet</h3>
                        <p class="text-xs text-[#47464E] mb-3 md:mb-4">Bawa ke Outlet Hub Soekarno-Hatta (07.00 - 21.00 WIB). Tanpa antre, cukup scan QR order di loker kasir express.</p>
                        <div class="flex justify-between items-center pt-3 border-t border-[#E2E7FF]/60 text-[10px] md:text-xs gap-2">
                            <span class="text-[#47464E]">Lokasi Outlet Utama</span>
                            <span class="font-bold text-[#4E6700]">Gratis Ongkir</span>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <!-- ================= SECTION 3: METODE PENGANTARAN ================= -->
        <div class="bg-white rounded-[24px] md:rounded-[32px] p-4 md:p-8 shadow-sm border border-[#E2E7FF]/60 mb-4 md:mb-6">
            <div class="flex gap-3 md:gap-4 items-start mb-4 md:mb-6">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#EAEDFF] rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                </div>
                <div>
                    <h2 class="text-base md:text-lg font-bold">Metode Pengantaran</h2>
                    <p class="text-xs md:text-sm text-[#47464E]">Bagaimana cucian yang telah bersih & wangi dikembalikan?</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                <!-- Diantar Kurir -->
                <label class="cursor-pointer">
                    <input type="radio" name="delivery" value="courier" class="peer sr-only" x-model="deliveryMethod">
                    <div class="p-4 md:p-5 rounded-2xl border-2 h-full transition-all
                                border-[#E2E7FF] bg-white
                                peer-checked:border-[#BFF138] peer-checked:bg-[#FAF8FF]">
                        <div class="flex justify-between items-start mb-3 gap-2">
                            <div class="w-9 h-9 md:w-10 md:h-10 bg-[#BFF138] rounded-xl flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 md:w-5 md:h-5 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"/></svg>
                            </div>
                            <span class="px-2 md:px-2.5 py-0.5 bg-[#1A1743] text-white rounded-full text-[10px] font-bold shrink-0">Rekomendasi</span>
                        </div>
                        <h3 class="text-sm md:text-base font-bold mb-1">Diantar Kurir Rodeo</h3>
                        <p class="text-xs text-[#47464E] mb-3 md:mb-4">Pakaian diantar rapi, wangi, berstandar boutique dalam garment bag waterproof steril langsung ke alamat utama.</p>
                        <div class="flex justify-between items-center pt-3 border-t border-[#E2E7FF]/60 text-[10px] md:text-xs gap-2">
                            <span class="font-bold text-[#4E6700]">Garansi Sampai Rapi</span>
                            <span class="font-bold text-[#1A1743]">Gratis Ongkir</span>
                        </div>
                    </div>
                </label>

                <!-- Ambil Sendiri -->
                <label class="cursor-pointer">
                    <input type="radio" name="delivery" value="locker" class="peer sr-only" x-model="deliveryMethod">
                    <div class="p-4 md:p-5 rounded-2xl border-2 h-full transition-all
                                border-[#E2E7FF] bg-white
                                peer-checked:border-[#BFF138] peer-checked:bg-[#FAF8FF]">
                        <div class="flex justify-between items-start mb-3 gap-2">
                            <div class="w-9 h-9 md:w-10 md:h-10 bg-[#EAEDFF] rounded-xl flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 md:w-5 md:h-5 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <span class="px-2 md:px-2.5 py-0.5 bg-[#EAEDFF] text-[#1A1743] rounded-full text-[10px] font-bold shrink-0">24 Jam</span>
                        </div>
                        <h3 class="text-sm md:text-base font-bold mb-1">Ambil Mandiri di Smart Locker</h3>
                        <p class="text-xs text-[#47464E] mb-3 md:mb-4">Ambil kapan pun di Smart Locker Outlet Soekarno-Hatta menggunakan kode OTP SMS/WhatsApp tanpa antre.</p>
                        <div class="flex justify-between items-center pt-3 border-t border-[#E2E7FF]/60 text-[10px] md:text-xs gap-2">
                            <span class="text-[#47464E]">Akses Fleksibel 24/7</span>
                            <span class="font-bold text-[#4E6700]">Bebas Biaya</span>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <!-- ================= SECTION 4: CATATAN KURIR ================= -->
        <div class="bg-white rounded-[24px] md:rounded-[32px] p-4 md:p-8 shadow-sm border border-[#E2E7FF]/60 mb-12 md:mb-16">
            <div class="flex gap-3 md:gap-4 items-start mb-3 md:mb-4">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#EAEDFF] rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <h2 class="text-base md:text-lg font-bold">Catatan untuk Kurir <span class="text-xs md:text-sm font-normal text-[#47464E]">(Opsional)</span></h2>
                    <p class="text-xs md:text-sm text-[#47464E]">Beri panduan patokan rumah atau instruksi serah terima cucian</p>
                </div>
            </div>

            <div class="relative">
                <textarea x-model="courierNotes"
                          maxlength="200"
                          rows="4"
                          placeholder="Titip ke pos satpam cluster depan ruko, rumah pagar hitam No. 45, hubungi WA jika sudah sampai."
                          class="w-full px-3 md:px-4 py-2.5 md:py-3 rounded-xl md:rounded-2xl border-2 border-[#E2E7FF] 
                                 bg-[#FAF8FF] text-xs md:text-sm text-[#1A1743]
                                 placeholder:text-[#9895C8]
                                 focus:outline-none focus:border-[#BFF138] focus:bg-white
                                 transition-all resize-none"></textarea>
                <div class="absolute bottom-3 right-3 md:bottom-4 md:right-4 text-[10px] md:text-xs font-semibold text-[#47464E] bg-[#FAF8FF] px-1.5 rounded">
                    <span x-text="courierNotes.length"></span> / 200
                </div>
            </div>

            <div class="mt-3 md:mt-4 flex items-center gap-2 flex-wrap">
                <span class="text-[10px] md:text-xs font-semibold text-[#47464E] w-full md:w-auto mb-1 md:mb-0">Instruksi Cepat:</span>
                <template x-for="chip in quickNotes" :key="chip">
                    <button @click="addQuickNote(chip)"
                            class="px-2.5 md:px-3 py-1 md:py-1.5 bg-[#F2F3FF] hover:bg-[#EAEDFF] text-[#1A1743] rounded-full text-[10px] md:text-xs font-semibold transition">
                        <span x-text="chip"></span>
                    </button>
                </template>
            </div>
        </div>
    </main>

    <!-- ================= FOOTER ================= -->
    <footer class="bg-white border-t border-gray-100 mt-12 md:mt-16">
        <div class="max-w-[1440px] mx-auto px-4 md:px-6 py-12 md:py-16">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 mb-10 md:mb-12">
                <div class="sm:col-span-2">
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
                <p class="text-center md:text-left">© 2025 Rodeo Laundry Malang. Hak Cipta Dilindungi Undang-Undang.</p>
                <div class="flex gap-4 flex-wrap justify-center">
                    <span class="flex items-center gap-1 font-bold"><div class="w-3 h-3 bg-[#4E6700]"></div> Garansi Bersih Higienis</span>
                    <span class="flex items-center gap-1 font-bold"><div class="w-3 h-3 bg-[#4E6700]"></div> 1 Mesin 1 Pelanggan</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- ================= STICKY BOTTOM BAR ================= -->
    <div class="fixed bottom-0 left-0 right-0 z-40 bg-white shadow-[0_-12px_32px_-8px_rgba(26,23,67,0.12)] border-t border-gray-100">
        <div class="max-w-[1440px] mx-auto px-3 md:px-6 py-3 md:py-4">
            <div class="flex items-center justify-between gap-3 md:gap-4">

                <div class="flex items-center gap-2 md:gap-4 min-w-0 flex-1">
                    <div class="relative w-10 h-10 md:w-12 md:h-12 bg-[#1A1743] rounded-xl flex items-center justify-center shrink-0">
                        <div class="w-4 h-5 md:w-5 md:h-6 bg-[#BFF138]"></div>
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
                                <span x-text="' · ' + s.name + ' (' + (serviceQty[s.id] || 0) + ' ' + s.unit + ')'"></span>
                            </template>
                            <template x-if="!hasAnySelected">
                                <span>Belum ada layanan dipilih</span>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 md:gap-6 shrink-0">
                    <div class="text-right">
                        <div class="text-[10px] md:text-xs text-[#47464E]">Total</div>
                        <div class="text-base md:text-2xl font-extrabold" x-text="formatRp(subtotal)"></div>
                    </div>

                    <template x-if="hasAnySelected">
                        <a href="{{ route('pembayaran') }}" 
                           class="hidden sm:flex px-4 md:px-8 py-2.5 md:py-3.5 bg-[#BFF138] text-[#131B2E] font-bold rounded-full text-xs md:text-sm
                                  transition-all duration-300 ease-out
                                  hover:shadow-[0_15px_35px_rgba(204,255,70,0.65)]
                                  hover:-translate-y-1
                                  hover:brightness-110
                                  items-center gap-2">
                            <span class="hidden md:inline">Lanjut ke Pembayaran</span>
                            <span class="md:hidden">Lanjut</span>
                            <span>→</span>
                        </a>
                    </template>
                    <template x-if="!hasAnySelected">
                        <button disabled
                                class="hidden sm:flex px-4 md:px-8 py-2.5 md:py-3.5 bg-[#E2E7FF] text-[#9895C8] font-bold rounded-full text-xs md:text-sm
                                       cursor-not-allowed items-center gap-2">
                            <span class="hidden md:inline">Pilih Layanan Dulu</span>
                            <span class="md:hidden">Pilih</span>
                            <span>→</span>
                        </button>
                    </template>
                </div>
            </div>

            <template x-if="hasAnySelected">
                <a href="{{ route('pembayaran') }}" 
                   class="sm:hidden mt-3 w-full py-3 bg-[#BFF138] text-[#131B2E] font-bold rounded-full text-sm
                          transition-all duration-300 ease-out
                          hover:shadow-[0_15px_35px_rgba(204,255,70,0.65)]
                          flex items-center justify-center gap-2">
                    Lanjut ke Pembayaran
                    <span>→</span>
                </a>
            </template>
            <template x-if="!hasAnySelected">
                <button disabled
                        class="sm:hidden mt-3 w-full py-3 bg-[#E2E7FF] text-[#9895C8] font-bold rounded-full text-sm
                               cursor-not-allowed flex items-center justify-center gap-2">
                    Pilih Layanan Dulu
                    <span>→</span>
                </button>
            </template>
        </div>
    </div>

</body>
</html>