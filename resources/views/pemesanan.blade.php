<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Layanan - Rodeo Laundry Malang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.14.1/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

    <!-- Data dynamic dari Laravel ditaruh di sini biar nggak mecah atribut x-data -->
    <script>
        window.pemesananData = {
            services: @json($services),
            preselectId: @json($preselectId),
            preselectName: @json($preselectName),
        };
    </script>
</head>
<body class="bg-[#FAF8FF] text-[#1A1743] font-sans antialiased pb-36"
      x-data="{
        ...window.pemesananData,

        kiloan: $persist(2).as('cart_kiloan'),
        notes: $persist('').as('cart_notes'),
        serviceQty: $persist({}).as('cart_service_qty'),
        hargaKiloan: 15000,

        init() {
            if (!this.preselectId) return;
            if (this.preselectName && this.preselectName.toLowerCase().includes('kiloan')) {
                if (this.kiloan === 0) this.kiloan = 2;
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
        addKiloan() { this.kiloan = 2; },
        getService(id) { return this.services.find(s => s.id === id); },

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
                <p class="text-[#47464E] max-w-lg">Pilih paket cucian dengan jaminan higienis 1 drum 1 pelanggan serta antar-jemput tepat waktu.</p>
            </div>

            <div class="flex items-center gap-3 bg-white p-2 rounded-full shadow-sm border border-[#E2E7FF]/60">
                <div class="flex items-center gap-2 px-4 py-2 bg-[#1A1743] text-white rounded-full">
                    <div class="w-4 h-4 bg-[#BFF138] rounded-full"></div>
                    <span class="text-sm font-bold">Pilih Layanan</span>
                </div>
                <div class="flex items-center gap-2 px-3">
                    <span class="text-xs text-[#47464E]">2</span>
                    <span class="text-sm font-semibold text-[#47464E]">Pengiriman</span>
                </div>
                <div class="flex items-center gap-2 px-3">
                    <span class="text-xs text-[#47464E]">3</span>
                    <span class="text-sm font-semibold text-[#47464E]">Pembayaran</span>
                </div>
            </div>
        </div>

        <!-- ================= SEMUA LAYANAN TERPILIH ================= -->
        <template x-if="hasAnySelected">
            <div class="space-y-3 mb-8">

                <!-- Cuci Kiloan -->
                <template x-if="kiloan > 0">
                    <div class="bg-white rounded-[32px] p-6 md:p-8 shadow-sm border border-[#E2E7FF]/60">
                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                            <div class="flex gap-4">
                                <div class="w-12 h-12 bg-[#1A1743] rounded-xl flex items-center justify-center shrink-0">
                                    <div class="w-5 h-6 bg-[#BFF138]"></div>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <h2 class="text-xl font-bold">Cuci Kiloan</h2>
                                        <span class="px-2.5 py-0.5 bg-[#C2F43B] rounded-full text-xs font-bold text-[#4E6700]">★ Paling Populer</span>
                                    </div>
                                    <p class="text-sm text-[#47464E] max-w-xl">Pencucian komplit 1 drum 1 pelanggan (tanpa campur), air steril terfilter UV, deterjen ramah serat, dan lipat rapi.</p>
                                </div>
                            </div>

                            <div class="flex flex-col items-end gap-2 shrink-0">
                                <div class="flex items-center gap-3 bg-[#F2F3FF] rounded-full p-1.5">
                                    <button @click="if(kiloan > 1) kiloan--; else kiloan = 0"
                                            class="w-8 h-8 bg-white rounded-full flex items-center justify-center font-bold text-[#1A1743] hover:bg-[#EAEDFF] transition">−</button>
                                    <span class="text-sm font-bold min-w-[40px] text-center" x-text="kiloan + ' kg'"></span>
                                    <button @click="kiloan++" class="w-8 h-8 bg-[#1A1743] text-white rounded-full flex items-center justify-center font-bold hover:bg-[#2F2D59] transition">+</button>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-extrabold">Rp 15.000 <span class="text-sm font-normal text-[#47464E]">/ kg</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Layanan lain yang terpilih -->
                <template x-for="service in selectedServices" :key="'sel-' + service.id">
                    <div class="bg-white rounded-[32px] p-6 md:p-8 shadow-sm border border-[#E2E7FF]/60">
                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                            <div class="flex gap-4">
                                <div class="w-12 h-12 bg-[#1A1743] rounded-xl flex items-center justify-center shrink-0">
                                    <div class="w-4 h-4 bg-[#C2F43B] rounded-full"></div>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <h2 class="text-xl font-bold" x-text="service.name"></h2>
                                        <span class="px-2.5 py-0.5 bg-[#EAEDFF] rounded-full text-xs font-bold text-[#1A1743]" x-text="service.tag"></span>
                                    </div>
                                    <p class="text-sm text-[#47464E] max-w-xl" x-text="service.description"></p>
                                </div>
                            </div>

                            <div class="flex flex-col items-end gap-2 shrink-0">
                                <div class="flex items-center gap-3 bg-[#F2F3FF] rounded-full p-1.5">
                                    <button @click="changeQty(service.id, -1)"
                                            class="w-8 h-8 bg-white rounded-full flex items-center justify-center font-bold text-[#1A1743] hover:bg-[#EAEDFF] transition">−</button>
                                    <span class="text-sm font-bold min-w-[40px] text-center" x-text="getQty(service.id) + ' ' + service.unit"></span>
                                    <button @click="changeQty(service.id, 1)"
                                            class="w-8 h-8 bg-[#1A1743] text-white rounded-full flex items-center justify-center font-bold hover:bg-[#2F2D59] transition">+</button>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-extrabold">
                                        <span x-text="formatRp(service.price)"></span>
                                        <span class="text-sm font-normal text-[#47464E]" x-text="'/ ' + service.unit"></span>
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
            <div class="flex items-center gap-2 mb-4">
                <span class="w-2 h-2 bg-[#1A1743] rounded-full"></span>
                <h3 class="text-lg font-bold">Layanan Lainnya</h3>
                <span class="px-2 py-0.5 bg-[#EAEDFF] rounded-full text-xs font-semibold text-[#47464E]"
                      x-text="(availableServices.length + (kiloan === 0 ? 1 : 0)) + ' Tersedia'"></span>
            </div>

            <template x-if="availableServices.length === 0 && kiloan > 0">
                <div class="text-center py-10 bg-white rounded-[24px] border border-dashed border-[#E2E7FF]">
                    <p class="text-sm text-[#47464E]">Semua layanan sudah kamu pilih! 🎉</p>
                </div>
            </template>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

                <!-- Cuci Kiloan di grid (kalau qty = 0) -->
                <template x-if="kiloan === 0">
                    <div class="bg-white p-6 rounded-[28px] border border-[#E2E7FF]/60 flex flex-col justify-between 
                                hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                        <div>
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-10 h-10 bg-[#1A1743] rounded-xl flex items-center justify-center">
                                    <div class="w-5 h-6 bg-[#BFF138]"></div>
                                </div>
                                <span class="px-2.5 py-0.5 bg-[#C2F43B] rounded-full text-xs font-bold text-[#4E6700]">★ Populer</span>
                            </div>
                            <h3 class="text-lg font-bold mb-1">Cuci Kiloan</h3>
                            <p class="text-sm text-[#47464E] mb-5">Cuci lipat rapi untuk pakaian harian. Bersih, higienis, dan wangi tahan lama.</p>
                        </div>
                        <div class="flex justify-between items-center pt-4 border-t border-[#E2E7FF]/60 gap-3">
                            <div class="text-lg font-extrabold shrink-0">
                                Rp 15.000 <span class="text-xs font-normal text-[#47464E]">/ kg</span>
                            </div>
                            <button @click="addKiloan()"
                                    class="px-4 py-1.5 bg-[#EAEDFF] hover:bg-[#1A1743] hover:text-white text-[#1A1743] rounded-full text-xs font-bold transition shrink-0">
                                + Tambah
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Layanan lain yang belum dipilih -->
                <template x-for="service in availableServices" :key="'avail-' + service.id">
                    <div class="bg-white p-6 rounded-[28px] border border-[#E2E7FF]/60 flex flex-col justify-between 
                                hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                        <div>
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-10 h-10 bg-[#1A1743] rounded-xl flex items-center justify-center">
                                    <div class="w-4 h-4 bg-[#C2F43B] rounded-full"></div>
                                </div>
                                <span class="px-2.5 py-0.5 bg-[#EAEDFF] rounded-full text-xs font-bold text-[#1A1743]" x-text="service.tag"></span>
                            </div>
                            <h3 class="text-lg font-bold mb-1" x-text="service.name"></h3>
                            <p class="text-sm text-[#47464E] mb-5" x-text="service.description"></p>
                        </div>
                        <div class="flex justify-between items-center pt-4 border-t border-[#E2E7FF]/60 gap-3">
                            <div class="text-lg font-extrabold shrink-0">
                                <span x-text="formatRp(service.price)"></span>
                                <span class="text-xs font-normal text-[#47464E]" x-text="'/ ' + service.unit"></span>
                            </div>
                            <button @click="addService(service.id)"
                                    class="px-4 py-1.5 bg-[#EAEDFF] hover:bg-[#1A1743] hover:text-white text-[#1A1743] rounded-full text-xs font-bold transition shrink-0">
                                + Tambah
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- ================= SECTION: CATATAN ================= -->
        <div class="mb-16">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-2 h-2 bg-[#1A1743] rounded-full"></span>
                <h3 class="text-lg font-bold">Catatan Tambahan</h3>
                <span class="text-xs text-[#47464E]">(Opsional)</span>
            </div>

            <div class="bg-white rounded-[24px] p-5 md:p-6 border border-[#E2E7FF]/60">
                <label for="notes" class="block text-sm font-semibold mb-2">
                    Ada pesan khusus untuk kami?
                </label>
                <textarea id="notes"
                          x-model="notes"
                          maxlength="500"
                          rows="4"
                          placeholder="Contoh: Pisahkan kemeja putih dengan yang berwarna, jangan pakai pengering panas untuk sweater, tolong wangi lavender ya..."
                          class="w-full px-4 py-3 rounded-2xl border-2 border-[#E2E7FF] 
                                 bg-[#FAF8FF] text-sm text-[#1A1743]
                                 placeholder:text-[#9895C8]
                                 focus:outline-none focus:border-[#BFF138] focus:bg-white
                                 transition-all resize-none"></textarea>
                <div class="flex justify-between items-center mt-3">
                    <span class="text-xs text-[#47464E]">Catatan akan langsung diteruskan ke tim laundry kami.</span>
                    <span class="text-xs font-semibold text-[#47464E]">
                        <span x-text="notes.length"></span> / 500
                    </span>
                </div>
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
                            <span x-text="' · ' + s.name + ' (' + getQty(s.id) + ' ' + s.unit + ')'"></span>
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
                <a href="{{ route('pengiriman') }}" class="px-6 md:px-8 py-3.5 bg-[#BFF138] text-[#131B2E] font-bold rounded-full
                        transition-all duration-300 ease-out
                        hover:shadow-[0_15px_35px_rgba(204,255,70,0.65)]
                        hover:-translate-y-1
                        hover:brightness-110
                        flex items-center gap-2">
                    Lanjut ke Jadwal & Pengiriman
                    <span>→</span>
                </a>
            </div>
        </div>
    </div>

</body>
</html>