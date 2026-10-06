<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - Rodeo Laundry Malang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
</head>
<body class="bg-[#FAF8FF] text-[#1A1743] font-sans antialiased"
      x-data="{
        orders: [
            {
                id: 'RDL-2510-003',
                date: '6 Oktober 2025, 14:32',
                status: 'processing',
                statusLabel: 'Sedang Dicuci',
                items: [
                    { name: 'Cuci Kiloan', qty: 3, unit: 'kg', price: 45000 },
                    { name: 'Cuci Sepatu Premium', qty: 1, unit: 'psg', price: 25000 },
                ],
                total: 70000,
                address: 'Jl. Soekarno Hatta No. 45, Lowokwaru',
                canCancel: true,
                canEditAddress: true,
            },
            {
                id: 'RDL-2509-018',
                date: '28 September 2025, 10:15',
                status: 'completed',
                statusLabel: 'Selesai',
                items: [
                    { name: 'Cuci Kiloan', qty: 5, unit: 'kg', price: 75000 },
                ],
                total: 75000,
                address: 'Gedung Coworking Lantai 2, Jl. Ijen No. 12, Klojen',
                canCancel: false,
                canEditAddress: false,
            },
            {
                id: 'RDL-2509-005',
                date: '15 September 2025, 09:45',
                status: 'cancelled',
                statusLabel: 'Dibatalkan',
                items: [
                    { name: 'Bed Cover & Selimut King', qty: 2, unit: 'pcs', price: 70000 },
                ],
                total: 70000,
                address: 'Jl. Soekarno Hatta No. 45, Lowokwaru',
                canCancel: false,
                canEditAddress: false,
            },
        ],
        get hasOrders() { return this.orders.length > 0; },
        formatRp(n) { return 'Rp ' + n.toLocaleString('id-ID'); },
        statusColor(status) {
            return {
                'pending':    { bg: 'bg-[#EAEDFF]', text: 'text-[#1A1743]', dot: 'bg-[#1A1743]' },
                'processing': { bg: 'bg-[#EAEDFF]', text: 'text-[#1A1743]', dot: 'bg-[#1A1743]' },
                'ready':      { bg: 'bg-[#C2F43B]', text: 'text-[#4E6700]', dot: 'bg-[#4E6700]' },
                'completed':  { bg: 'bg-[#C2F43B]', text: 'text-[#4E6700]', dot: 'bg-[#4E6700]' },
                'cancelled':  { bg: 'bg-red-100',   text: 'text-red-700',   dot: 'bg-red-500' },
            }[status];
        },
        stepIndex(status) {
            return { 'pending': 0, 'processing': 1, 'ready': 2, 'completed': 3, 'cancelled': -1 }[status];
        }
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
                <a href="{{ route('pemesanan') }}" class="px-4 py-1.5 text-sm font-semibold text-[#47464E] hover:text-[#1A1743] rounded-full">Layanan</a>
                <a href="{{ url('/orders') }}" class="px-4 py-1.5 bg-[#EAEDFF] rounded-full text-sm font-bold">Pesanan</a>
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

        <!-- ================= HERO ================= -->
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
            <div>
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-[#C2F43B]/50 text-[#4E6700] text-xs font-bold uppercase rounded-full mb-3">
                    <span class="w-2 h-2 bg-[#4E6700] rounded-full"></span> Riwayat & Pelacakan
                </span>
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold leading-tight mb-2">Pesanan Saya</h1>
                <p class="text-[#47464E] max-w-lg">Pantau status cucian Anda secara real-time, mulai dari penjemputan sampai kembali ke lemari.</p>
            </div>
            <a href="{{ route('pemesanan') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-[#1A1743] text-white font-bold rounded-full self-start md:self-end
                      transition-all duration-300 ease-out
                      hover:shadow-[0_15px_35px_-8px_rgba(26,23,67,0.5)]
                      hover:-translate-y-1">
                + Pesanan Baru
            </a>
        </div>

        <!-- ================= LIST PESANAN ================= -->
        <div class="space-y-6">

            <template x-if="!hasOrders">
                <div class="text-center py-20 bg-white rounded-[32px] border border-dashed border-[#E2E7FF]">
                    <div class="w-16 h-16 bg-[#F2F3FF] rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-[#9895C8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold mb-1">Belum Ada Pesanan</h3>
                    <p class="text-sm text-[#47464E] mb-5">Yuk mulai laundry pertama Anda sekarang!</p>
                    <a href="{{ route('pemesanan') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#BFF138] text-[#131B2E] font-bold rounded-full
                              transition-all duration-300 hover:shadow-[0_15px_35px_rgba(204,255,70,0.5)]">
                        Mulai Pesan Layanan
                        <span>→</span>
                    </a>
                </div>
            </template>

            <template x-for="order in orders" :key="order.id">
                <div class="bg-white rounded-[28px] border border-[#E2E7FF]/60 overflow-hidden
                            transition-all duration-300 hover:shadow-[0_15px_40px_-12px_rgba(26,23,67,0.15)]">

                    <!-- Header Kartu -->
                    <div class="px-6 md:px-8 pt-6 pb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3 border-b border-[#E2E7FF]/60">
                        <div class="flex items-center gap-3 flex-wrap">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span class="font-bold text-[#1A1743]" x-text="'#' + order.id"></span>
                            </div>
                            <span class="text-xs text-[#47464E]" x-text="order.date"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1.5"
                                  :class="statusColor(order.status).bg + ' ' + statusColor(order.status).text">
                                <span class="w-1.5 h-1.5 rounded-full" :class="statusColor(order.status).dot"></span>
                                <span x-text="order.statusLabel"></span>
                            </span>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <template x-if="order.status !== 'cancelled'">
                        <div class="px-6 md:px-8 py-5 bg-[#FAF8FF]">
                            <div class="flex items-center justify-between relative">
                                <div class="absolute top-3 left-3 right-3 h-0.5 bg-[#E2E7FF]"></div>
                                <div class="absolute top-3 left-3 h-0.5 bg-[#BFF138] transition-all duration-500"
                                     :style="'width: calc(' + (stepIndex(order.status) / 3) * 100 + '% - ' + (stepIndex(order.status) === 0 ? '0px' : '24px') + ')'"></div>

                                <div class="flex flex-col items-center gap-2 z-10" style="min-width: 60px;">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center bg-[#BFF138]">
                                        <svg class="w-3 h-3 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <span class="text-[10px] md:text-xs font-bold text-[#1A1743]">Diterima</span>
                                </div>

                                <div class="flex flex-col items-center gap-2 z-10" style="min-width: 60px;">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center transition-colors"
                                         :class="stepIndex(order.status) >= 1 ? 'bg-[#BFF138]' : 'bg-white border-2 border-[#E2E7FF]'">
                                        <svg x-show="stepIndex(order.status) >= 1" class="w-3 h-3 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        <span x-show="stepIndex(order.status) < 1" class="w-1.5 h-1.5 bg-[#E2E7FF] rounded-full"></span>
                                    </div>
                                    <span class="text-[10px] md:text-xs font-bold" :class="stepIndex(order.status) >= 1 ? 'text-[#1A1743]' : 'text-[#9895C8]'">Dicuci</span>
                                </div>

                                <div class="flex flex-col items-center gap-2 z-10" style="min-width: 60px;">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center transition-colors"
                                         :class="stepIndex(order.status) >= 2 ? 'bg-[#BFF138]' : 'bg-white border-2 border-[#E2E7FF]'">
                                        <svg x-show="stepIndex(order.status) >= 2" class="w-3 h-3 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        <span x-show="stepIndex(order.status) < 2" class="w-1.5 h-1.5 bg-[#E2E7FF] rounded-full"></span>
                                    </div>
                                    <span class="text-[10px] md:text-xs font-bold" :class="stepIndex(order.status) >= 2 ? 'text-[#1A1743]' : 'text-[#9895C8]'">Diantar</span>
                                </div>

                                <div class="flex flex-col items-center gap-2 z-10" style="min-width: 60px;">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center transition-colors"
                                         :class="stepIndex(order.status) >= 3 ? 'bg-[#BFF138]' : 'bg-white border-2 border-[#E2E7FF]'">
                                        <svg x-show="stepIndex(order.status) >= 3" class="w-3 h-3 text-[#1A1743]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        <span x-show="stepIndex(order.status) < 3" class="w-1.5 h-1.5 bg-[#E2E7FF] rounded-full"></span>
                                    </div>
                                    <span class="text-[10px] md:text-xs font-bold" :class="stepIndex(order.status) >= 3 ? 'text-[#1A1743]' : 'text-[#9895C8]'">Selesai</span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Info Dibatalkan -->
                    <template x-if="order.status === 'cancelled'">
                        <div class="px-6 md:px-8 py-4 bg-red-50 border-b border-red-100">
                            <p class="text-xs text-red-700 font-semibold">Pesanan ini telah dibatalkan.</p>
                        </div>
                    </template>

                    <!-- Body Kartu -->
                    <div class="px-6 md:px-8 py-5 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-[#47464E] mb-3">Layanan Dipesan</h4>
                            <div class="space-y-2">
                                <template x-for="item in order.items" :key="item.name">
                                    <div class="flex justify-between items-start text-sm">
                                        <div class="flex-1 min-w-0 pr-3">
                                            <p class="font-bold text-[#1A1743] truncate" x-text="item.name"></p>
                                            <p class="text-xs text-[#47464E]" x-text="item.qty + ' ' + item.unit"></p>
                                        </div>
                                        <span class="font-semibold text-[#1A1743] shrink-0" x-text="formatRp(item.price)"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-[#47464E] mb-3">Alamat</h4>
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-[#1A1743] mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p class="text-sm text-[#47464E]" x-text="order.address"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Kartu -->
                    <div class="px-6 md:px-8 py-4 bg-[#FAF8FF] border-t border-[#E2E7FF]/60 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <span class="text-xs text-[#47464E]">Total Pembayaran</span>
                            <div class="text-xl font-extrabold text-[#1A1743]" x-text="formatRp(order.total)"></div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <template x-if="order.canEditAddress">
                                <button class="px-4 py-2 bg-white border border-[#E2E7FF] hover:border-[#1A1743] text-[#1A1743] rounded-full text-xs font-bold transition flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Ubah Alamat
                                </button>
                            </template>

                            <template x-if="order.canCancel">
                                <button class="px-4 py-2 bg-white border border-red-200 hover:border-red-400 hover:bg-red-50 text-red-600 rounded-full text-xs font-bold transition flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Batalkan Pesanan
                                </button>
                            </template>

                            <a href="#" class="px-4 py-2 bg-[#1A1743] hover:bg-[#2F2D59] text-white rounded-full text-xs font-bold transition flex items-center gap-1.5">
                                Lihat Detail
                                <span>→</span>
                            </a>
                        </div>
                    </div>
                </div>
            </template>
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