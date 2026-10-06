<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengiriman - Rodeo Laundry Malang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.14.1/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

    <script>
        window.pengirimanData = {
            services: @json($services),
        };
    </script>
</head>
<body class="bg-[#FAF8FF] text-[#1A1743] font-sans antialiased pb-36"
      x-data="{
        ...window.pengirimanData,

        // Baca cart dari localStorage (sinkron dengan halaman pemesanan)
        kiloan: $persist(2).as('cart_kiloan'),
        serviceQty: $persist({}).as('cart_service_qty'),
        courierNotes: $persist('').as('cart_courier_notes'),

        // State alamat dummy
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

        // Metode penjemputan
        pickupMethod: 'courier',
        // Metode pengantaran
        deliveryMethod: 'courier',

        // Quick chips untuk catatan
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

        // ====== Perhitungan (sama seperti pemesanan) ======
        hargaKiloan: 15000,
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
                <a href="{{ url('/') }}" class="px-4 py-1.5 text-sm font-semibold text-[#47464E] hover:text-[#1A1743] rounded-full">Beranda</a>
                <a href="{{ route('pemesanan') }}" class="px-4 py-1.5 bg-[#EAEDFF] rounded-full text-sm font-bold">Layanan</a>
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

    <main class="pt-28 max-w-[1440px] mx-auto px-6">

        <!-- ================= HERO + STEPPER ================= -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-8">
            <div>
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-[#C2F43B]/50 text-[#4E6700] text-xs font-bold uppercase rounded-full mb-3">
                    <span class="w-2 h-2 bg-[#4E6700] rounded-full"></span> Layanan On-Demand Malang
                </span>
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold leading-tight mb-3">Pesan Layanan Laundry</h1>
                <p class="text-[#47464E] max-w-lg">Pilih paket cucian & treatment pakaian dengan jaminan higienis 1 drum 1 pelanggan serta antar-jemput tepat waktu.</p>
            </div>

            <!-- Stepper: Step 1 Done, Step 2 Active -->
            <div class="flex items-center gap-3 bg-white p-2 rounded-full shadow-sm border border-[#E2E7FF]/60">
                <a href="{{ route('pemesanan') }}" class="flex items-center gap-2 px-4 py-2 rounded-full hover:bg-[#F2F3FF] transition">
                    <div class="w-4 h-4 bg-[#1A1743] rounded-full flex items-center justify-center">
                        <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <span class="text-sm font-semibold text-[#47464E]">Pilih Layanan</span>
                </a>
                <div class="flex items-center gap-2 px-4 py-2 bg-[#1A1743] text-white rounded-full">
                    <div class="w-4 h-4 bg-[#BFF138] rounded-full"></div>
                    <span class="text-sm font-bold">Pengiriman</span>
                </div>
                <div class="flex items-center gap-2 px-3">
                    <span class="text-xs text-[#47464E]">3</span>
                    <span class="text-sm font-semibold text-[#47464E]">Pembayaran</span>
                </div>
            </div>
        </div>

        <!-- ================= SECTION 1: ALAMAT PENJEMPUTAN ================= -->
        <div class="bg-white rounded-[32px] p-6 md:p-8 shadow-sm border border-[#E2E7FF]/60 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <div class="flex gap-4 items-start">
                    <div class="w-12 h-12 bg-[#EAEDFF] rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold">Alamat Penjemputan</h2>
                        <p class="text-sm text-[#47464E]">Pilih alamat kurir menjemput cucian kotor Anda</p>
                    </div>
                </div>
                <button class="px-4 py-2 bg-[#F2F3FF] hover:bg-[#EAEDFF] text-[#1A1743] rounded-full text-sm font-bold transition">
                    + Alamat Baru
                </button>
            </div>

            <!-- List Alamat -->
            <div class="space-y-3">
                <template x-for="addr in addresses" :key="addr.id">
                    <label class="block cursor-pointer">
                        <input type="radio" name="address" class="peer sr-only" 
                               :value="addr.id" x-model="selectedAddress">
                        <div class="p-5 rounded-2xl border-2 transition-all
                                    border-[#E2E7FF] bg-white
                                    peer-checked:border-[#BFF138] peer-checked:bg-[#FAF8FF]">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                <div class="flex gap-3 flex-1">
                                    <div class="w-5 h-5 rounded-full border-2 border-[#E2E7FF] flex items-center justify-center shrink-0 mt-1
                                                peer-checked:border-[#1A1743] peer-checked:bg-[#1A1743]">
                                        <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                                            <span class="px-2.5 py-0.5 bg-[#EAEDFF] rounded-full text-xs font-bold text-[#1A1743]" x-text="addr.label"></span>
                                            <template x-if="addr.isDefault">
                                                <span class="px-2.5 py-0.5 bg-[#C2F43B] rounded-full text-[10px] font-bold text-[#4E6700] uppercase">Utama</span>
                                            </template>
                                        </div>
                                        <h3 class="text-base font-bold mb-1" x-text="addr.address"></h3>
                                        <p class="text-xs text-[#47464E] mb-2" x-text="addr.detail"></p>
                                        <div class="flex items-center gap-4 text-xs text-[#47464E]">
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
                                <button class="px-3 py-1.5 bg-[#F2F3FF] hover:bg-[#EAEDFF] text-[#1A1743] rounded-full text-xs font-bold transition shrink-0">
                                    Ubah Alamat
                                </button>
                            </div>
                        </div>
                    </label>
                </template>
            </div>

            <!-- Info tambahan -->
            <div class="mt-5 flex items-center justify-between px-4 py-3 bg-[#F2F3FF] rounded-2xl">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm text-[#47464E]">Punya lokasi kost, apartemen, atau vila lain di Malang?</p>
                </div>
                <button class="text-sm font-bold text-[#1A1743] hover:underline">Tambahkan →</button>
            </div>
        </div>

        <!-- ================= SECTION 2: METODE PENJEMPUTAN ================= -->
        <div class="bg-white rounded-[32px] p-6 md:p-8 shadow-sm border border-[#E2E7FF]/60 mb-6">
            <div class="flex gap-4 items-start mb-6">
                <div class="w-12 h-12 bg-[#EAEDFF] rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold">Metode Penjemputan</h2>
                    <p class="text-sm text-[#47464E]">Pilih cara penyerahan pakaian kotor Anda ke Rodeo</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Opsi 1: Dijemput Kurir -->
                <label class="cursor-pointer">
                    <input type="radio" name="pickup" value="courier" class="peer sr-only" x-model="pickupMethod">
                    <div class="p-5 rounded-2xl border-2 h-full transition-all
                                border-[#E2E7FF] bg-white
                                peer-checked:border-[#BFF138] peer-checked:bg-[#FAF8FF]">
                        <div class="flex justify-between items-start mb-3">
                            <div class="w-10 h-10 bg-[#BFF138] rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            </div>
                            <div class="flex gap-2">
                                <span class="px-2.5 py-0.5 bg-[#1A1743] text-white rounded-full text-[10px] font-bold">Populer</span>
                                <span class="px-2.5 py-0.5 bg-[#C2F43B] text-[#4E6700] rounded-full text-[10px] font-bold">Gratis</span>
                            </div>
                        </div>
                        <h3 class="text-base font-bold mb-1">Dijemput Kurir Rodeo</h3>
                        <p class="text-xs text-[#47464E] mb-4">Estimasi tiba 20-30 menit. Kurir kami membawa laundry bag steril khusus anti-kontaminasi langsung ke pintu Anda.</p>
                        <div class="flex justify-between items-center pt-3 border-t border-[#E2E7FF]/60 text-xs">
                            <span class="flex items-center gap-1 text-[#4E6700] font-bold">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                Respon Cepat
                            </span>
                            <span class="font-bold text-[#1A1743]">Rp 0 (Radius &lt; 5km)</span>
                        </div>
                    </div>
                </label>

                <!-- Opsi 2: Antar Sendiri -->
                <label class="cursor-pointer">
                    <input type="radio" name="pickup" value="self" class="peer sr-only" x-model="pickupMethod">
                    <div class="p-5 rounded-2xl border-2 h-full transition-all
                                border-[#E2E7FF] bg-white
                                peer-checked:border-[#BFF138] peer-checked:bg-[#FAF8FF]">
                        <div class="flex justify-between items-start mb-3">
                            <div class="w-10 h-10 bg-[#EAEDFF] rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <span class="px-2.5 py-0.5 bg-[#EAEDFF] text-[#1A1743] rounded-full text-[10px] font-bold">Drop & Go</span>
                        </div>
                        <h3 class="text-base font-bold mb-1">Antar Sendiri ke Outlet</h3>
                        <p class="text-xs text-[#47464E] mb-4">Bawa ke Outlet Hub Soekarno-Hatta (07.00 - 21.00 WIB). Tanpa antre, cukup scan QR order di loker kasir express.</p>
                        <div class="flex justify-between items-center pt-3 border-t border-[#E2E7FF]/60 text-xs">
                            <span class="text-[#47464E]">Lokasi Outlet Utama</span>
                            <span class="font-bold text-[#4E6700]">Gratis Ongkir</span>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <!-- ================= SECTION 3: METODE PENGANTARAN ================= -->
        <div class="bg-white rounded-[32px] p-6 md:p-8 shadow-sm border border-[#E2E7FF]/60 mb-6">
            <div class="flex gap-4 items-start mb-6">
                <div class="w-12 h-12 bg-[#EAEDFF] rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold">Metode Pengantaran</h2>
                    <p class="text-sm text-[#47464E]">Bagaimana cucian yang telah bersih & wangi dikembalikan?</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Opsi 1: Diantar Kurir -->
                <label class="cursor-pointer">
                    <input type="radio" name="delivery" value="courier" class="peer sr-only" x-model="deliveryMethod">
                    <div class="p-5 rounded-2xl border-2 h-full transition-all
                                border-[#E2E7FF] bg-white
                                peer-checked:border-[#BFF138] peer-checked:bg-[#FAF8FF]">
                        <div class="flex justify-between items-start mb-3">
                            <div class="w-10 h-10 bg-[#BFF138] rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"/></svg>
                            </div>
                            <span class="px-2.5 py-0.5 bg-[#1A1743] text-white rounded-full text-[10px] font-bold">Direkomendasikan</span>
                        </div>
                        <h3 class="text-base font-bold mb-1">Diantar Kurir Rodeo</h3>
                        <p class="text-xs text-[#47464E] mb-4">Pakaian diantar rapi, wangi, berstandar boutique dalam garment bag waterproof steril langsung ke alamat utama.</p>
                        <div class="flex justify-between items-center pt-3 border-t border-[#E2E7FF]/60 text-xs">
                            <span class="font-bold text-[#4E6700]">Garansi Sampai Rapi</span>
                            <span class="font-bold text-[#1A1743]">Gratis Ongkir</span>
                        </div>
                    </div>
                </label>

                <!-- Opsi 2: Ambil Sendiri -->
                <label class="cursor-pointer">
                    <input type="radio" name="delivery" value="locker" class="peer sr-only" x-model="deliveryMethod">
                    <div class="p-5 rounded-2xl border-2 h-full transition-all
                                border-[#E2E7FF] bg-white
                                peer-checked:border-[#BFF138] peer-checked:bg-[#FAF8FF]">
                        <div class="flex justify-between items-start mb-3">
                            <div class="w-10 h-10 bg-[#EAEDFF] rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <span class="px-2.5 py-0.5 bg-[#EAEDFF] text-[#1A1743] rounded-full text-[10px] font-bold">24 Jam</span>
                        </div>
                        <h3 class="text-base font-bold mb-1">Ambil Mandiri di Smart Locker</h3>
                        <p class="text-xs text-[#47464E] mb-4">Ambil kapan pun di Smart Locker Outlet Soekarno-Hatta menggunakan kode OTP SMS/WhatsApp tanpa antre.</p>
                        <div class="flex justify-between items-center pt-3 border-t border-[#E2E7FF]/60 text-xs">
                            <span class="text-[#47464E]">Akses Fleksibel 24/7</span>
                            <span class="font-bold text-[#4E6700]">Bebas Biaya</span>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <!-- ================= SECTION 4: CATATAN UNTUK KURIR ================= -->
        <div class="bg-white rounded-[32px] p-6 md:p-8 shadow-sm border border-[#E2E7FF]/60 mb-16">
            <div class="flex gap-4 items-start mb-4">
                <div class="w-12 h-12 bg-[#EAEDFF] rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold">Catatan untuk Kurir <span class="text-sm font-normal text-[#47464E]">(Opsional)</span></h2>
                    <p class="text-sm text-[#47464E]">Beri panduan patokan rumah atau instruksi serah terima cucian</p>
                </div>
            </div>

            <div class="relative">
                <textarea x-model="courierNotes"
                          maxlength="200"
                          rows="4"
                          placeholder="Titip ke pos satpam cluster depan ruko, rumah pagar hitam No. 45, hubungi WA jika sudah sampai."
                          class="w-full px-4 py-3 rounded-2xl border-2 border-[#E2E7FF] 
                                 bg-[#FAF8FF] text-sm text-[#1A1743]
                                 placeholder:text-[#9895C8]
                                 focus:outline-none focus:border-[#BFF138] focus:bg-white
                                 transition-all resize-none"></textarea>
                <div class="absolute bottom-4 right-4 text-xs font-semibold text-[#47464E]">
                    <span x-text="courierNotes.length"></span> / 200
                </div>
            </div>

            <!-- Quick chips -->
            <div class="mt-4 flex items-center gap-2 flex-wrap">
                <span class="text-xs font-semibold text-[#47464E]">Instruksi Cepat:</span>
                <template x-for="chip in quickNotes" :key="chip">
                    <button @click="addQuickNote(chip)"
                            class="px-3 py-1.5 bg-[#F2F3FF] hover:bg-[#EAEDFF] text-[#1A1743] rounded-full text-xs font-semibold transition">
                        <span x-text="chip"></span>
                    </button>
                </template>
            </div>
        </div>
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

    <!-- ================= STICKY BOTTOM BAR ================= -->
    <div class="fixed bottom-0 left-0 right-0 z-40 bg-white shadow-[0_-12px_32px_-8px_rgba(26,23,67,0.12)] border-t border-gray-100">
        <div class="max-w-[1440px] mx-auto px-6 py-4 flex items-center justify-between gap-4">

            <div class="flex items-center gap-4 min-w-0">
                <div class="relative w-12 h-12 bg-[#1A1743] rounded-xl flex items-center justify-center shrink-0">
                    <div class="w-5 h-6 bg-[#BFF138]"></div>
                    <div class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-[#BFF138] text-[#131B2E] rounded-full text-xs font-extrabold flex items-center justify-center border-2 border-white"
                         x-text="totalItems"></div>
                </div>
                <div class="min-w-0">
                    <div class="text-sm font-bold" x-text="totalItems + ' Layanan Dipilih'"></div>
                    <div class="text-xs text-[#47464E] flex items-center gap-2 mt-0.5">
                        <span class="inline-flex items-center gap-1">
                            <span class="w-3 h-3 bg-[#1A1743] rounded-sm"></span> Outlet Suhat (3,2 km)
                        </span>
                    </div>
                    <div class="text-xs text-[#47464E] truncate mt-0.5">
                        <template x-if="kiloan > 0">
                            <span x-text="'Cuci Kiloan (' + kiloan + 'kg)'"></span>
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

            <div class="flex items-center gap-6 shrink-0">
                <button class="hidden md:flex items-center gap-1 text-xs text-[#47464E] hover:text-[#1A1743] font-semibold">
                    Lihat Rincian Pesanan <span>⌃</span>
                </button>
                <div class="text-right">
                    <div class="text-xs text-[#47464E]">Total</div>
                    <div class="text-2xl font-extrabold" x-text="formatRp(subtotal)"></div>
                </div>
                <a href="{{ route('pembayaran') }}" class="px-6 md:px-8 py-3.5 bg-[#BFF138] text-[#131B2E] font-bold rounded-full
                        transition-all duration-300 ease-out
                        hover:shadow-[0_15px_35px_rgba(204,255,70,0.65)]
                        hover:-translate-y-1
                        hover:brightness-110
                        flex items-center gap-2">
                    Lanjut ke Pembayaran
                    <span>→</span>
                </a>
                </a>
            </div>
        </div>
    </div>

</body>
</html>