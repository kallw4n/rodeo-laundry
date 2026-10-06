<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Rodeo Laundry Malang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.14.1/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

    <script>
        window.pembayaranData = {
            services: @json($services),
        };
    </script>
</head>
<body class="bg-[#FAF8FF] text-[#1A1743] font-sans antialiased"
      x-data="{
        ...window.pembayaranData,

        // Cart dari localStorage
        kiloan: $persist(2).as('cart_kiloan'),
        serviceQty: $persist({}).as('cart_service_qty'),
        notes: $persist('').as('cart_notes'),

        // State halaman pembayaran
        summaryOpen: true,
        paymentMethod: 'qris',
        biayaAntarJemput: 6000,
        biayaLayanan: 0,

        hargaKiloan: 15000,
        getService(id) { return this.services.find(s => s.id === id); },
        get selectedServices() {
            return this.services.filter(s => (this.serviceQty[s.id] || 0) > 0);
        },
        get subtotalItem() {
            let t = this.kiloan * this.hargaKiloan;
            this.services.forEach(s => t += s.price * (this.serviceQty[s.id] || 0));
            return t;
        },
        get subtotal() { return this.subtotalItem; },
        get totalBayar() {
            return this.subtotalItem + this.biayaAntarJemput + this.biayaLayanan;
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

    <main class="pt-28 max-w-[1440px] mx-auto px-6 pb-16">

        <!-- ================= HERO + STEPPER ================= -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-8">
            <div>
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-[#C2F43B]/50 text-[#4E6700] text-xs font-bold uppercase rounded-full mb-3">
                    <span class="w-2 h-2 bg-[#4E6700] rounded-full"></span> Layanan On-Demand Malang
                </span>
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold leading-tight mb-3">Pembayaran</h1>
                <p class="text-[#47464E] max-w-lg">Pilih paket cucian & treatment pakaian dengan jaminan higienis 1 drum 1 pelanggan serta antar-jemput tepat waktu.</p>
            </div>

            <!-- Stepper: Step 1 & 2 Done, Step 3 Active -->
            <div class="flex items-center gap-3 bg-white p-2 rounded-full shadow-sm border border-[#E2E7FF]/60">
                <a href="{{ route('pemesanan') }}" class="flex items-center gap-2 px-3 py-2 rounded-full hover:bg-[#F2F3FF] transition">
                    <span class="text-xs text-[#47464E]">1</span>
                    <span class="text-sm font-semibold text-[#47464E]">Pilih Layanan</span>
                </a>
                <div class="w-6 h-px bg-[#E2E7FF]"></div>
                <a href="{{ route('pengiriman') }}" class="flex items-center gap-2 px-3 py-2 rounded-full hover:bg-[#F2F3FF] transition">
                    <span class="text-xs text-[#47464E]">2</span>
                    <span class="text-sm font-semibold text-[#47464E]">Pengiriman</span>
                </a>
                <div class="w-6 h-px bg-[#E2E7FF]"></div>
                <div class="flex items-center gap-2 px-4 py-2 bg-[#1A1743] text-white rounded-full">
                    <div class="w-4 h-4 bg-[#BFF138] rounded-full"></div>
                    <span class="text-sm font-bold">Pembayaran</span>
                </div>
            </div>
        </div>

        <!-- ================= MAIN CARD ================= -->
        <div class="bg-white rounded-[32px] p-6 md:p-10 shadow-sm border border-[#E2E7FF]/60">

            <!-- ====== RINGKASAN PESANAN (Collapsible) ====== -->
            <div class="border border-[#E2E7FF] rounded-3xl overflow-hidden mb-8">
                <button @click="summaryOpen = !summaryOpen"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-[#FAF8FF] transition">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="font-bold text-[#1A1743]">Ringkasan Pesanan</span>
                        <span class="px-2.5 py-0.5 bg-[#EAEDFF] rounded-full text-xs font-bold text-[#1A1743]"
                              x-text="totalItems + ' Layanan'"></span>
                    </div>
                    <svg class="w-5 h-5 text-[#47464E] transition-transform duration-300"
                         :class="summaryOpen ? 'rotate-180' : 'rotate-0'"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="summaryOpen" x-collapse
                     class="border-t border-[#E2E7FF] px-6 py-5">

                    <!-- List Item -->
                    <div class="space-y-4 mb-6">

                        <!-- Cuci Kiloan -->
                        <template x-if="kiloan > 0">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-2">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-[#1A1743]">Cuci Kiloan</span>
                                        <span class="text-sm text-[#47464E]" x-text="'(' + kiloan + ' kg)'"></span>
                                    </div>
                                    <template x-if="notes && notes.trim().length > 0">
                                        <p class="text-xs text-[#47464E] mt-1 pl-4 border-l-2 border-[#BFF138]"
                                           x-text="'↳ ' + notes"></p>
                                    </template>
                                </div>
                                <div class="text-right font-bold text-[#1A1743]"
                                     x-text="formatRp(kiloan * hargaKiloan)"></div>
                            </div>
                        </template>

                        <!-- Layanan lain -->
                        <template x-for="service in selectedServices" :key="'sum-' + service.id">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-2">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-[#1A1743]" x-text="service.name"></span>
                                        <span class="text-sm text-[#47464E]"
                                              x-text="'(' + (serviceQty[service.id] || 0) + ' ' + service.unit + ')'"></span>
                                    </div>
                                </div>
                                <div class="text-right font-bold text-[#1A1743]"
                                     x-text="formatRp(service.price * (serviceQty[service.id] || 0))"></div>
                            </div>
                        </template>

                        <template x-if="!hasAnySelected">
                            <p class="text-sm text-[#47464E] italic">Belum ada layanan yang dipilih.</p>
                        </template>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-dashed border-[#E2E7FF] pt-4">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between text-[#47464E]">
                                <span>Subtotal Item</span>
                                <span x-text="formatRp(subtotalItem)"></span>
                            </div>
                            <div class="flex justify-between text-[#47464E]">
                                <span>Biaya Antar-Jemput (Pickup & Delivery)</span>
                                <span x-text="formatRp(biayaAntarJemput)"></span>
                            </div>
                            <div class="flex justify-between text-[#47464E]">
                                <span>Biaya Layanan</span>
                                <span x-text="formatRp(biayaLayanan)"></span>
                            </div>
                        </div>
                        <div class="border-t border-[#E2E7FF] mt-4 pt-4 flex justify-between items-center">
                            <span class="text-lg font-bold text-[#1A1743]">Total Pembayaran</span>
                            <span class="text-2xl font-extrabold text-[#1A1743]" x-text="formatRp(totalBayar)"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ====== METODE PEMBAYARAN ====== -->
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-[#BFF138] rounded-full"></span>
                    <h2 class="text-lg font-bold">Metode Pembayaran</h2>
                </div>
                <span class="text-xs text-[#47464E]">Diproses Instan &amp; Aman</span>
            </div>

            <div class="space-y-3">

                <!-- QRIS -->
                <label class="cursor-pointer block">
                    <input type="radio" name="payment" value="qris" class="peer sr-only" x-model="paymentMethod">
                    <div class="p-5 rounded-2xl border-2 transition-all
                                border-[#E2E7FF] bg-white
                                peer-checked:border-[#BFF138] peer-checked:bg-[#FAF8FF]">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3 flex-1">
                                <div class="w-5 h-5 rounded-full border-2 border-[#E2E7FF] flex items-center justify-center shrink-0 mt-1
                                            peer-checked:border-[#1A1743] peer-checked:bg-[#1A1743]">
                                    <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 flex-wrap mb-1">
                                        <span class="font-bold text-[#1A1743]">QRIS (Semua E-Wallet &amp; M-Banking)</span>
                                        <span class="px-2 py-0.5 bg-[#C2F43B] rounded-full text-[10px] font-bold text-[#4E6700]">Otomatis</span>
                                    </div>
                                    <p class="text-xs text-[#47464E]">BCA Mobile, GoPay, OVO, ShopeePay, DANA, Livin', LinkAja</p>

                                    <!-- Detail QRIS (muncul saat dipilih) -->
                                    <template x-if="paymentMethod === 'qris'">
                                        <div class="mt-4 p-4 bg-white rounded-xl border border-[#E2E7FF] flex items-start gap-3">
                                            <div class="w-10 h-10 bg-[#1A1743] rounded-lg flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5 text-[#BFF138]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-[#1A1743] mb-0.5">Kode QR Dinamis Otomatis</p>
                                                <p class="text-xs text-[#47464E]">Kode bayar terbit otomatis setelah tombol konfirmasi ditekan.</p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <span class="px-3 py-1 border border-[#E2E7FF] rounded-lg text-xs font-bold text-[#1A1743] shrink-0">QRIS</span>
                        </div>
                    </div>
                </label>

                <!-- Transfer Bank -->
                <label class="cursor-pointer block">
                    <input type="radio" name="payment" value="va" class="peer sr-only" x-model="paymentMethod">
                    <div class="p-5 rounded-2xl border-2 transition-all
                                border-[#E2E7FF] bg-white
                                peer-checked:border-[#BFF138] peer-checked:bg-[#FAF8FF]">
                        <div class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full border-2 border-[#E2E7FF] flex items-center justify-center shrink-0 mt-1
                                        peer-checked:border-[#1A1743] peer-checked:bg-[#1A1743]">
                                <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-[#1A1743] mb-1">Transfer Bank / Virtual Account</p>
                                <p class="text-xs text-[#47464E]">BCA VA, Mandiri, BNI, BRI, Permata Bank</p>
                            </div>
                        </div>
                    </div>
                </label>

                <!-- E-Wallet -->
                <label class="cursor-pointer block">
                    <input type="radio" name="payment" value="ewallet" class="peer sr-only" x-model="paymentMethod">
                    <div class="p-5 rounded-2xl border-2 transition-all
                                border-[#E2E7FF] bg-white
                                peer-checked:border-[#BFF138] peer-checked:bg-[#FAF8FF]">
                        <div class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full border-2 border-[#E2E7FF] flex items-center justify-center shrink-0 mt-1
                                        peer-checked:border-[#1A1743] peer-checked:bg-[#1A1743]">
                                <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-[#1A1743] mb-1">E-Wallet Direct Debit</p>
                                <p class="text-xs text-[#47464E]">Notifikasi langsung ke GoPay, ShopeePay, atau OVO</p>
                            </div>
                        </div>
                    </div>
                </label>

                <!-- COD -->
                <label class="cursor-pointer block">
                    <input type="radio" name="payment" value="cod" class="peer sr-only" x-model="paymentMethod">
                    <div class="p-5 rounded-2xl border-2 transition-all
                                border-[#E2E7FF] bg-white
                                peer-checked:border-[#BFF138] peer-checked:bg-[#FAF8FF]">
                        <div class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full border-2 border-[#E2E7FF] flex items-center justify-center shrink-0 mt-1
                                        peer-checked:border-[#1A1743] peer-checked:bg-[#1A1743]">
                                <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-[#1A1743] mb-1">Bayar di Tempat (COD / Tunai)</p>
                                <p class="text-xs text-[#47464E]">Bayar tunai atau QR saat kurir mengantar pakaian bersih ke rumah</p>
                            </div>
                        </div>
                    </div>
                </label>
            </div>

            <!-- ====== TOMBOL KONFIRMASI & BAYAR ====== -->
            <button class="w-full mt-8 px-8 py-4 bg-[#BFF138] text-[#131B2E] font-bold rounded-2xl
                           flex items-center justify-between
                           transition-all duration-300 ease-out
                           hover:shadow-[0_15px_35px_rgba(204,255,70,0.65)]
                           hover:-translate-y-1
                           hover:brightness-110">
                <span class="text-base">Konfirmasi &amp; Bayar</span>
                <span class="flex items-center gap-2">
                    <span class="text-lg font-extrabold" x-text="formatRp(totalBayar)"></span>
                    <span>→</span>
                </span>
            </button>

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

</body>
</html>