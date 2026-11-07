<x-admin-layout title="Point of Sale (POS) / Kasir">
    <div x-data="posApp()" class="space-y-6">

        <!-- ================= PAGE HEADER ================= -->
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-[#1E1B4B] tracking-tight">Point of Sale (POS) / Kasir</h1>
            <p class="text-sm text-[#64748B] mt-1">Input transaksi cepat, timbang cucian digital, dan cetak nota kasir.</p>
        </div>

        @if(session('recent_order_id'))
            <!-- Banner Transaksi Terbaru Siap Cetak -->
            <div class="p-4 rounded-2xl bg-indigo-50 border border-indigo-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-extrabold text-[#1E1B4B]">Transaksi Terakhir: #RD-{{ str_pad(session('recent_order_id'), 4, '0', STR_PAD_LEFT) }}</div>
                        <div class="text-xs text-slate-500">Transaksi baru saja disimpan. Anda dapat langsung mencetak struk thermal resminya.</div>
                    </div>
                </div>
                <a href="{{ route('admin.orders.receipt', session('recent_order_id')) }}" target="_blank"
                   class="px-4 py-2 bg-[#1E1B4B] hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 shrink-0">
                    <svg class="w-4 h-4 text-[#BFF108]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Cetak Struk Resmi #RD-{{ str_pad(session('recent_order_id'), 4, '0', STR_PAD_LEFT) }}</span>
                </a>
            </div>
        @endif

        <!-- ================= MAIN CONTENT 2-COLUMNS ================= -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- LEFT COLUMN: SEARCH + CUSTOMER + CATALOG (col-span-7 or 8) -->
            <div class="lg:col-span-7 xl:col-span-8 space-y-6">

                <!-- 1. CUSTOMER TYPE SELECTOR (WALK-IN LANGSUNG VS MEMBER AKUN) -->
                <div class="bg-white rounded-3xl p-5 border border-slate-300/80 shadow-[0_10px_30px_-5px_rgba(26,23,67,0.06),0_4px_12px_rgba(0,0,0,0.03)] space-y-4">
                    <!-- Tab Toggle Switcher -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-2xl w-fit">
                            <button type="button"
                                    @click="customerType = 'walkin'"
                                    :class="customerType === 'walkin' ? 'bg-white text-[#1E1B4B] shadow-xs font-extrabold' : 'text-slate-500 hover:text-[#1E1B4B] font-medium'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs transition flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span>Pelanggan Walk-In (Langsung)</span>
                            </button>
                            <button type="button"
                                    @click="customerType = 'member'"
                                    :class="customerType === 'member' ? 'bg-white text-[#1E1B4B] shadow-xs font-extrabold' : 'text-slate-500 hover:text-[#1E1B4B] font-medium'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs transition flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                                <span>Pilih Member Rodeo</span>
                            </button>
                        </div>

                        <div class="text-[11px] text-slate-500 flex items-center gap-1.5 font-medium">
                            <span class="inline-block w-2 h-2 rounded-full" :class="customerType === 'walkin' ? 'bg-emerald-500 ring-2 ring-emerald-200' : 'bg-indigo-500 ring-2 ring-indigo-200'"></span>
                            <span x-text="customerType === 'walkin' ? 'Cukup nama saja, tanpa perlu daftar akun' : 'Cari akun member untuk poin & riwayat'"></span>
                        </div>
                    </div>

                    <!-- MODE 1: WALKIN (CUKUP INPUT NAMA & HP OPSIONAL) -->
                    <div x-show="customerType === 'walkin'" class="space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Pelanggan Walk-In:</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <input type="text"
                                           x-model="walkinName"
                                           placeholder="Contoh: Pak Budi / Mbak Rina (Opsional)"
                                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300/80 rounded-2xl text-sm text-[#1E1B4B] placeholder-slate-400 focus:outline-none focus:bg-white focus:border-[#1E1B4B] focus:ring-2 focus:ring-[#1E1B4B]/10 transition">
                                </div>
                                <p class="text-[10px] text-slate-400 mt-1">*Jika kosong, otomatis tercatat "Pelanggan Walk-in".</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">No. WhatsApp (Opsional):</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                    </div>
                                    <input type="text"
                                           x-model="walkinPhone"
                                           placeholder="08xxxxxxxxxx (untuk kirim struk WA)"
                                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300/80 rounded-2xl text-sm text-[#1E1B4B] placeholder-slate-400 focus:outline-none focus:bg-white focus:border-[#1E1B4B] focus:ring-2 focus:ring-[#1E1B4B]/10 transition">
                                </div>
                                <p class="text-[10px] text-slate-400 mt-1">*Diisi jika pelanggan ingin dikirimkan nota via WA.</p>
                            </div>
                        </div>
                    </div>

                    <!-- MODE 2: MEMBER RODEO (SEARCH AUTOCOMPLETE) -->
                    <div x-show="customerType === 'member'" class="space-y-3">
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input type="text"
                                       x-ref="customerInput"
                                       x-model="customerQuery"
                                       @focus="customerDropdown = true"
                                       placeholder="Ketik ID pelanggan (#RD-...) atau nama pelanggan yang punya akun..."
                                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300/80 rounded-2xl text-sm text-[#1E1B4B] placeholder-slate-400 focus:outline-none focus:bg-white focus:border-[#1E1B4B] focus:ring-2 focus:ring-[#1E1B4B]/10 transition">

                                <!-- Autocomplete Dropdown -->
                                <div x-show="customerDropdown && filteredCustomers.length > 0"
                                     @click.away="customerDropdown = false"
                                     class="absolute z-30 left-0 right-0 mt-2 bg-white border border-slate-300/90 rounded-2xl shadow-xl max-h-60 overflow-y-auto divide-y divide-slate-100">
                                    <template x-for="c in filteredCustomers" :key="c.id">
                                        <div @click="selectCustomer(c)"
                                             class="p-3 hover:bg-slate-50 cursor-pointer flex items-center justify-between transition">
                                            <div>
                                                <div class="text-sm font-bold text-[#1E1B4B]">
                                                    <span class="text-xs text-[#64748B] font-mono mr-1.5" x-text="'#RD-' + String(c.id).padStart(4, '0')"></span>
                                                    <span x-text="c.name"></span>
                                                </div>
                                                <div class="text-xs text-[#64748B]" x-text="(c.phone || '-') + ' • ' + (c.city || 'Malang')"></div>
                                            </div>
                                            <span class="text-xs text-[#BFF108] bg-[#1E1B4B] px-2.5 py-1 rounded-full font-bold">Pilih</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Selected Customer Pill / Card -->
                        <template x-if="selectedCustomer">
                            <div class="bg-indigo-50/80 border border-indigo-200/90 rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-sm">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-11 h-11 rounded-full bg-[#1E1B4B] text-white font-extrabold flex items-center justify-center text-sm shadow">
                                        <span x-text="getInitials(selectedCustomer.name)"></span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-[#1E1B4B]" x-text="selectedCustomer.name"></div>
                                        <div class="text-xs text-[#64748B]" x-text="(selectedCustomer.phone || '-') + ' • ' + (selectedCustomer.address || 'Alamat Outlet')"></div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 self-end sm:self-auto">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#BFF108] text-[#1E1B4B] text-xs font-bold rounded-full shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                        </svg>
                                        <span>Member Aktif</span>
                                    </span>
                                    <button type="button" @click="clearCustomer()" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-full hover:bg-white transition" title="Batal pilih member">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- 2. SERVICE CATALOG GRID -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($services as $srv)
                        @php
                            $sName = strtolower($srv->name);
                            $unit = 'pcs';
                            $duration = '24 Jam';
                            $tag = 'Layanan Reguler';
                            if (str_contains($sName, 'kiloan') || str_contains($sName, 'kering')) {
                                $unit = 'kg';
                                $duration = '24 Jam';
                                $tag = 'Tanpa setrika uap';
                            } elseif (str_contains($sName, 'setrika')) {
                                $unit = 'kg';
                                $duration = '12 Jam';
                                $tag = 'Termasuk hanger lipat';
                            } elseif (str_contains($sName, 'dry') || str_contains($sName, 'jas') || str_contains($sName, 'blazer')) {
                                $unit = 'pcs';
                                $duration = '2 Hari Kerja';
                                $tag = 'Free Dust Cover';
                            } elseif (str_contains($sName, 'sepatu') || str_contains($sName, 'sneakers')) {
                                $unit = 'pasang';
                                $duration = '2-3 Hari';
                                $tag = 'Anti-Bacterial UV Box';
                            } elseif (str_contains($sName, 'bed') || str_contains($sName, 'cover')) {
                                $unit = 'pcs';
                                $duration = '2 Hari';
                                $tag = 'Kemasan Vakum Kedap';
                            }
                        @endphp
                        <div class="group bg-white hover:bg-[#1E1B4B] rounded-3xl p-5 border border-slate-300/80 hover:border-[#1E1B4B] shadow-[0_10px_30px_-5px_rgba(26,23,67,0.06),0_4px_12px_rgba(0,0,0,0.03)] hover:shadow-xl hover:shadow-[#1E1B4B]/25 flex flex-col justify-between relative overflow-hidden transition-all duration-300 hover:-translate-y-1 cursor-pointer"
                             @click="addToCart({{ $srv->id }}, '{{ addslashes($srv->name) }}', {{ (float)$srv->price }}, '{{ $unit }}')">
                            
                            <!-- Subtle glow bubble on bottom-right corner -->
                            <div class="absolute -right-6 -bottom-6 w-32 h-32 rounded-full bg-white/0 group-hover:bg-white/5 pointer-events-none transition-all duration-300"></div>

                            <div class="relative z-10">
                                <!-- Top row: Icon + Title & Price -->
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100/80 group-hover:border-white/10 flex items-center justify-center text-[#1E1B4B] group-hover:bg-white/10 group-hover:text-[#BFF108] transition-colors duration-300">
                                            @if(str_contains($sName, 'sepatu'))
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                            @elseif(str_contains($sName, 'setrika'))
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                            @else
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/><path d="M12 3v3m0 12v3M3 12h3m12 0h3"/></svg>
                                            @endif
                                        </div>
                                        <div>
                                            <h3 class="text-base font-bold text-[#1E1B4B] group-hover:text-white transition-colors duration-300">{{ $srv->name }}</h3>
                                            <span class="text-xs text-slate-500 group-hover:text-slate-300 font-medium transition-colors duration-300">⏱ {{ $duration }}</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-base font-extrabold text-[#1E1B4B] group-hover:text-[#BFF108] transition-colors duration-300">Rp {{ number_format($srv->price, 0, ',', '.') }}</div>
                                        <div class="text-[11px] text-slate-500 group-hover:text-slate-300 font-medium transition-colors duration-300">/{{ $unit }}</div>
                                    </div>
                                </div>

                                <p class="text-xs text-[#64748B] group-hover:text-slate-300 mt-3 line-clamp-2 transition-colors duration-300">{{ $srv->description ?? 'Pencucian higienis dengan pewangi premium Rodeo.' }}</p>
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-200/80 group-hover:border-white/10 flex items-center justify-between relative z-10 transition-colors duration-300">
                                <span class="text-[11px] font-semibold text-[#1E1B4B]/70 group-hover:text-slate-300 transition-colors duration-300">{{ $tag }}</span>
                                <button type="button"
                                        @click.stop="addToCart({{ $srv->id }}, '{{ addslashes($srv->name) }}', {{ (float)$srv->price }}, '{{ $unit }}')"
                                        class="px-4 py-1.5 rounded-full bg-slate-100 group-hover:bg-[#BFF108] border border-slate-200/80 group-hover:border-[#BFF108] text-[#1E1B4B] text-xs font-bold transition-all duration-300 flex items-center gap-1.5 shadow-sm">
                                    <span>+ Tambah</span>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 p-8 text-center bg-white rounded-3xl border border-dashed border-slate-300 text-slate-400">
                            Belum ada master layanan. Tambahkan di menu Kelola Layanan.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- RIGHT COLUMN: ORDER SUMMARY & CHECKOUT (col-span-5 or 4) -->
            <div class="lg:col-span-5 xl:col-span-4 sticky top-28">
                <div class="bg-white rounded-3xl p-6 border border-slate-300/80 shadow-[0_10px_30px_-5px_rgba(26,23,67,0.08),0_4px_12px_rgba(0,0,0,0.04)] space-y-5">

                    <!-- Header with Invoice Number -->
                    <div class="text-center pb-4 border-b border-slate-200/80">
                        <span class="inline-block px-3 py-1 bg-indigo-50 border border-indigo-200/70 text-[#1E1B4B] text-[11px] font-extrabold tracking-wider uppercase rounded-full">
                            NOTA BARU #{{ $invoiceNumber }}
                        </span>
                        <h2 class="text-xl font-extrabold text-[#1E1B4B] mt-2 tracking-tight">Ringkasan Pesanan</h2>
                        <div class="text-xs text-slate-500 font-medium mt-0.5">{{ \Carbon\Carbon::now()->isoFormat('D MMM Y, HH:mm') }} WIB</div>
                    </div>

                    <!-- Cart Item List -->
                    <div class="space-y-3.5 max-h-72 overflow-y-auto pr-1">
                        <template x-if="cart.length === 0">
                            <div class="p-8 text-center text-xs text-slate-400 border border-dashed border-slate-200 rounded-2xl bg-slate-50/50">
                                <svg class="w-8 h-8 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                <span>Belum ada layanan dipilih.<br>Klik "+ Tambah" pada katalog di samping.</span>
                            </div>
                        </template>

                        <template x-for="(item, idx) in cart" :key="item.id">
                            <div class="p-4 bg-slate-50/90 hover:bg-slate-100/70 rounded-2xl border border-slate-200/90 shadow-2xs transition">
                                <!-- Top Row: Name and Subtotal & Trash -->
                                <div class="flex items-start justify-between gap-2 mb-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="text-sm font-extrabold text-[#1E1B4B] truncate" x-text="item.name"></div>
                                        <div class="text-xs text-slate-500 font-semibold mt-0.5" x-text="'@ Rp ' + formatRupiah(item.price) + ' / ' + item.unit"></div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-black text-[#1E1B4B]" x-text="'Rp ' + formatRupiah(item.qty * item.price)"></span>
                                        <button type="button" @click="removeFromCart(idx)" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition cursor-pointer" title="Hapus layanan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Stepper Input Box (Lebar, Ramah Sentuh, Tombol +/- Nyaman) -->
                                <div class="flex items-center justify-between gap-3 bg-white p-2.5 rounded-xl border border-slate-200/90 shadow-2xs">
                                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider pl-1">
                                        <span x-text="item.unit === 'kg' ? 'Berat Cucian:' : 'Jumlah Item:'"></span>
                                    </span>
                                    <div class="flex items-center gap-1.5">
                                        <!-- Tombol Kurangi (-) -->
                                        <button type="button" 
                                                @click="stepDown(item, idx)"
                                                class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 active:scale-95 text-[#1E1B4B] font-extrabold text-base flex items-center justify-center transition shadow-2xs cursor-pointer select-none"
                                                title="Kurangi">
                                            −
                                        </button>

                                        <!-- Input Angka Langsung (Besar & Jelas) -->
                                        <div class="relative">
                                            <input type="number"
                                                   x-model.number="item.qty"
                                                   step="0.1"
                                                   min="0.1"
                                                   class="w-20 px-2 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-sm font-black text-[#1E1B4B] text-center focus:outline-none focus:bg-white focus:border-[#1E1B4B] focus:ring-2 focus:ring-[#1E1B4B]/20">
                                        </div>

                                        <!-- Tombol Tambah (+) -->
                                        <button type="button" 
                                                @click="stepUp(item)"
                                                class="w-8 h-8 rounded-lg bg-[#1E1B4B] hover:bg-[#2A2663] active:scale-95 text-white font-extrabold text-base flex items-center justify-center transition shadow-xs cursor-pointer select-none"
                                                title="Tambah">
                                            +
                                        </button>

                                        <!-- Satuan Unit Badge -->
                                        <span class="text-xs font-bold text-[#1E1B4B] bg-slate-100 px-2.5 py-1.5 rounded-lg shrink-0" x-text="item.unit"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Subtotal & Ongkir -->
                    <div class="pt-3 border-t border-slate-200/80 space-y-2 text-xs">
                        <div class="flex items-center justify-between text-slate-600 font-medium">
                            <span x-text="'Subtotal (' + cart.length + ' Item)'"></span>
                            <span class="font-bold text-[#1E1B4B]" x-text="'Rp ' + formatRupiah(totalCart)"></span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600 font-medium">
                            <span>Ongkir Antar-Jemput</span>
                            <span class="px-2 py-0.5 bg-[#BFF108] text-[#1E1B4B] font-extrabold rounded-full text-[10px] shadow-sm">GRATIS</span>
                        </div>
                    </div>

                    <!-- Total Tagihan Box -->
                    <div class="bg-[#1E1B4B] p-4 rounded-2xl flex items-center justify-between shadow-md">
                        <span class="text-xs font-bold text-slate-200">Total Tagihan:</span>
                        <span class="text-xl font-extrabold text-[#BFF108]" x-text="'Rp ' + formatRupiah(totalCart)"></span>
                    </div>

                    <!-- Payment Method Picker (2x2 grid) -->
                    <div class="space-y-2">
                        <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">METODE PEMBAYARAN</label>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <button type="button"
                                    @click="paymentMethod = 'qris'"
                                    :class="paymentMethod === 'qris' ? 'bg-[#1E1B4B] text-white shadow-sm border border-[#1E1B4B]' : 'bg-slate-100 text-[#1E1B4B] border border-slate-200/80 hover:bg-slate-200/80'"
                                    class="p-2.5 rounded-2xl font-bold flex items-center justify-center gap-1.5 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                <span>QRIS / E-Wallet</span>
                            </button>

                            <button type="button"
                                    @click="paymentMethod = 'cash'"
                                    :class="paymentMethod === 'cash' ? 'bg-[#1E1B4B] text-white shadow-sm border border-[#1E1B4B]' : 'bg-slate-100 text-[#1E1B4B] border border-slate-200/80 hover:bg-slate-200/80'"
                                    class="p-2.5 rounded-2xl font-bold flex items-center justify-center gap-1.5 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>Tunai / Cash</span>
                            </button>

                            <button type="button"
                                    @click="paymentMethod = 'transfer'"
                                    :class="paymentMethod === 'transfer' ? 'bg-[#1E1B4B] text-white shadow-sm border border-[#1E1B4B]' : 'bg-slate-100 text-[#1E1B4B] border border-slate-200/80 hover:bg-slate-200/80'"
                                    class="p-2.5 rounded-2xl font-bold flex items-center justify-center gap-1.5 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                                <span>Transfer Bank</span>
                            </button>

                            <button type="button"
                                    @click="paymentMethod = 'cod'"
                                    :class="paymentMethod === 'cod' ? 'bg-[#1E1B4B] text-white shadow-sm border border-[#1E1B4B]' : 'bg-slate-100 text-[#1E1B4B] border border-slate-200/80 hover:bg-slate-200/80'"
                                    class="p-2.5 rounded-2xl font-bold flex items-center justify-center gap-1.5 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1"/></svg>
                                <span>Bayar Saat Antar</span>
                            </button>
                        </div>
                    </div>

                    <!-- Cash Input & Change calculation -->
                    <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200/80 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-600 font-medium">Uang Tunai Diterima:</span>
                            <div class="flex items-center gap-1">
                                <span class="font-bold text-[#1E1B4B]">Rp</span>
                                <input type="number"
                                       x-model.number="cashReceived"
                                       class="w-24 px-2 py-1 bg-white border border-slate-300/80 rounded-lg text-xs font-bold text-[#1E1B4B] text-right focus:outline-none focus:border-[#1E1B4B] focus:ring-1 focus:ring-[#1E1B4B]">
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-200/80">
                            <span class="text-slate-600 font-medium">Kembalian Kasir:</span>
                            <span class="text-sm font-extrabold text-emerald-600" x-text="'Rp ' + formatRupiah(changeAmount)"></span>
                        </div>
                    </div>

                    <!-- FORM SUBMISSION -->
                    <form method="POST" action="{{ route('admin.pos.order') }}" @submit.prevent="submitOrder()">
                        @csrf
                        <input type="hidden" name="customer_id" :value="customerType === 'member' && selectedCustomer ? selectedCustomer.id : ''">
                        <input type="hidden" name="customer_name" :value="customerType === 'walkin' ? (walkinName.trim() || 'Pelanggan Walk-in') : (selectedCustomer ? selectedCustomer.name : 'Pelanggan Walk-in')">
                        <input type="hidden" name="customer_phone" :value="customerType === 'walkin' ? walkinPhone.trim() : (selectedCustomer ? selectedCustomer.phone : '')">
                        <input type="hidden" name="payment_method" :value="paymentMethod">
                        <input type="hidden" name="cash_received" :value="cashReceived">

                        <!-- JSON Items payload -->
                        <template x-for="(it, i) in cart" :key="i">
                            <div>
                                <input type="hidden" :name="'items[' + i + '][id]'" :value="it.id">
                                <input type="hidden" :name="'items[' + i + '][qty]'" :value="it.qty">
                            </div>
                        </template>

                        <!-- Helper Checklist Persyaratan Transaksi -->
                        <div x-show="cart.length === 0 || (customerType === 'member' && !selectedCustomer)"
                             class="p-3 bg-amber-50/90 border border-amber-200/90 rounded-2xl text-xs space-y-1.5 transition">
                            <div class="font-bold text-amber-900 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Status Transaksi Kasir:</span>
                            </div>
                            <div class="flex items-center gap-2 pl-5 text-[11px]">
                                <span class="text-emerald-700 font-bold" x-show="customerType === 'walkin'">
                                    ✅ Pelanggan: <span class="font-semibold" x-text="walkinName.trim() ? walkinName.trim() : 'Pelanggan Walk-in (Otomatis)'"></span>
                                </span>
                                <span :class="selectedCustomer ? 'text-emerald-700 font-bold' : 'text-amber-800 font-medium'" x-show="customerType === 'member'">
                                    <span x-text="selectedCustomer ? '✅' : '⏳'"></span>
                                    1. Member: <span class="font-semibold" x-text="selectedCustomer ? selectedCustomer.name : 'Pilih member di atas'"></span>
                                </span>
                            </div>
                            <div class="flex items-center gap-2 pl-5 text-[11px]">
                                <span :class="cart.length > 0 ? 'text-emerald-700 font-bold' : 'text-amber-800 font-medium'">
                                    <span x-text="cart.length > 0 ? '✅' : '⏳'"></span>
                                    <span x-text="customerType === 'member' ? '2. Layanan:' : 'Layanan:'"></span>
                                    <span class="font-semibold" x-text="cart.length > 0 ? cart.length + ' item cucian dipilih' : 'Pilih layanan di samping (+ Tambah)'"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="space-y-2.5">
                            <button type="button"
                                    @click="submitOrder()"
                                    :class="cart.length === 0 ? 'bg-[#BFF108]/80 hover:bg-[#BFF108]' : 'bg-[#BFF108] hover:scale-[1.01] active:scale-[0.99] shadow-md'"
                                    class="w-full py-3.5 px-4 rounded-2xl text-[#1E1B4B] font-extrabold text-sm transition flex items-center justify-center gap-2 cursor-pointer shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                <span>Simpan Struk</span>
                            </button>

                            <button type="button"
                                    @click="sendWhatsAppReceipt()"
                                    class="w-full py-3 px-4 rounded-2xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-800 font-bold text-xs transition flex items-center justify-center gap-2 cursor-pointer">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <span>Kirim Struk ke WA Cust</span>
                            </button>
                        </div>
                    </form>

                    <!-- Cetak Struk Action Button -->
                    <div class="pt-3 border-t border-slate-200/80">
                        <button type="button"
                                @click="printReceipt()"
                                class="w-full py-2.5 px-4 rounded-2xl bg-slate-100 hover:bg-slate-200/90 text-slate-700 text-xs font-bold transition flex items-center justify-center gap-2 cursor-pointer shadow-2xs">
                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            <span>Cetak Struk</span>
                        </button>
                    </div>

                </div>
            </div>

        </div>

        <!-- ================= THERMAL RECEIPT MODAL PREVIEW ================= -->
        <div x-show="showReceiptModal"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
             style="display: none;">
            <div @click.away="showReceiptModal = false"
                 class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 relative print:shadow-none print:border-none print:w-full print:p-0">
                
                <!-- Receipt Paper (Thermal Style) -->
                <div id="thermal-receipt" class="font-mono text-xs text-slate-800 space-y-3 bg-white p-2">
                    <div class="text-center space-y-1 pb-3 border-b border-dashed border-slate-400">
                        <h3 class="font-extrabold text-base tracking-tight text-[#1E1B4B]">RODEO LAUNDRY</h3>
                        <p class="text-[11px] text-slate-500">Gg. Rodeo, Sumberejo, Kota Batu</p>
                        <p class="text-[11px] text-slate-500">WA: 0813-5805-5257</p>
                    </div>

                    <div class="text-[11px] space-y-1 py-1 border-b border-dashed border-slate-400">
                        <div class="flex justify-between">
                            <span class="text-slate-500">No. Nota:</span>
                            <span class="font-bold">#{{ $invoiceNumber }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Waktu:</span>
                            <span>{{ \Carbon\Carbon::now()->isoFormat('D MMM Y, HH:mm') }} WIB</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Pelanggan:</span>
                            <span class="font-bold" x-text="activeCustomerName"></span>
                        </div>
                        <div class="flex justify-between" x-show="activeCustomerPhone !== '-'">
                            <span class="text-slate-500">Telepon:</span>
                            <span x-text="activeCustomerPhone"></span>
                        </div>
                    </div>

                    <!-- Items List -->
                    <div class="py-2 space-y-2 border-b border-dashed border-slate-400">
                        <template x-for="(it, idx) in cart" :key="idx">
                            <div class="space-y-0.5">
                                <div class="font-bold text-slate-800" x-text="it.name"></div>
                                <div class="flex justify-between text-[11px] text-slate-600">
                                    <span x-text="it.qty + ' ' + it.unit + ' x Rp ' + formatRupiah(it.price)"></span>
                                    <span class="font-bold" x-text="'Rp ' + formatRupiah(it.qty * it.price)"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Totals & Payment -->
                    <div class="space-y-1.5 pt-1 text-[11px]">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Subtotal:</span>
                            <span class="font-bold" x-text="'Rp ' + formatRupiah(totalCart)"></span>
                        </div>
                        <div class="flex justify-between text-emerald-700">
                            <span>Antar-Jemput:</span>
                            <span class="font-bold">GRATIS</span>
                        </div>
                        <div class="flex justify-between text-xs pt-1.5 border-t border-dashed border-slate-400 font-extrabold text-[#1E1B4B]">
                            <span>TOTAL TAGIHAN:</span>
                            <span x-text="'Rp ' + formatRupiah(totalCart)"></span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Metode Bayar:</span>
                            <span class="uppercase font-semibold" x-text="paymentMethod"></span>
                        </div>
                        <div class="flex justify-between text-slate-600" x-show="paymentMethod === 'cash'">
                            <span>Uang Tunai:</span>
                            <span x-text="'Rp ' + formatRupiah(cashReceived)"></span>
                        </div>
                        <div class="flex justify-between text-slate-600" x-show="paymentMethod === 'cash'">
                            <span>Kembalian:</span>
                            <span class="font-bold text-emerald-700" x-text="'Rp ' + formatRupiah(changeAmount)"></span>
                        </div>
                    </div>

                    <div class="text-center text-[10px] text-slate-500 pt-3 border-t border-dashed border-slate-400 space-y-0.5">
                        <p>Simpan nota ini sebagai bukti transaksi laundry.</p>
                        <p class="font-semibold text-slate-700">Terima kasih atas kepercayaan Anda!</p>
                    </div>
                </div>

                <!-- Modal Action Buttons (Hidden on Print) -->
                <div class="mt-5 flex items-center gap-2 print:hidden">
                    <button type="button"
                            @click="triggerPrintThermal()"
                            class="flex-1 py-2.5 px-4 rounded-xl bg-[#1E1B4B] text-white font-bold text-xs hover:bg-slate-800 transition flex items-center justify-center gap-2 cursor-pointer shadow-md">
                        <svg class="w-4 h-4 text-[#BFF108]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span>Cetak ke Printer</span>
                    </button>
                    <button type="button"
                            @click="showReceiptModal = false"
                            class="py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- Print Media Styling Khusus Kertas Thermal -->
    <style>
        @media print {
            body * {
                visibility: hidden !important;
            }
            #thermal-receipt, #thermal-receipt * {
                visibility: visible !important;
            }
            #thermal-receipt {
                position: fixed !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                max-width: 80mm !important;
                margin: 0 auto !important;
                padding: 12px !important;
                background: white !important;
                color: black !important;
            }
        }
    </style>

    <!-- Pos Controller Javascript via Alpine -->
    <script>
        function posApp() {
            const rawCustomers = @json($customers);
            const rawRecentOrderId = @json(session('recent_order_id'));

            return {
                customers: rawCustomers,
                customerType: 'walkin',
                walkinName: '',
                walkinPhone: '',
                customerQuery: '',
                customerDropdown: false,
                selectedCustomer: null,
                cart: [],
                paymentMethod: 'cash',
                cashReceived: 0,
                showReceiptModal: false,
                recentOrderId: rawRecentOrderId,

                get activeCustomerName() {
                    if (this.customerType === 'member') {
                        return this.selectedCustomer ? this.selectedCustomer.name : 'Tamu Member';
                    }
                    return this.walkinName.trim() ? this.walkinName.trim() : 'Pelanggan Walk-in';
                },

                get activeCustomerPhone() {
                    if (this.customerType === 'member') {
                        return this.selectedCustomer ? (this.selectedCustomer.phone || '-') : '-';
                    }
                    return this.walkinPhone.trim() ? this.walkinPhone.trim() : '-';
                },

                get filteredCustomers() {
                    if (!this.customerQuery) return this.customers.slice(0, 6);
                    const raw = this.customerQuery.toLowerCase().trim();
                    const cleanId = raw.replace('#rd-', '').replace('#', '').trim();
                    return this.customers.filter(c => {
                        const idStr = String(c.id);
                        const paddedId = String(c.id).padStart(4, '0');
                        const matchId = (cleanId !== '' && (idStr.includes(cleanId) || paddedId.includes(cleanId)));
                        const matchName = c.name && c.name.toLowerCase().includes(raw);
                        const matchPhone = c.phone && c.phone.includes(raw);
                        return matchId || matchName || matchPhone;
                    }).slice(0, 6);
                },

                selectCustomer(c) {
                    this.selectedCustomer = c;
                    this.customerQuery = '';
                    this.customerDropdown = false;
                },

                clearCustomer() {
                    this.selectedCustomer = null;
                },

                getInitials(name) {
                    if (!name) return 'RP';
                    const parts = name.trim().split(' ');
                    if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
                    return (parts[0][0] + parts[1][0]).toUpperCase();
                },

                addToCart(id, name, price, unit) {
                    const existing = this.cart.find(it => it.id === id);
                    if (existing) {
                        existing.qty = Math.round((existing.qty + (unit === 'kg' ? 1.0 : 1)) * 10) / 10;
                    } else {
                        this.cart.push({
                            id: id,
                            name: name,
                            price: Number(price),
                            qty: unit === 'kg' ? 1.0 : 1,
                            unit: unit
                        });
                    }
                    if (this.cashReceived === 0 || this.cashReceived < this.totalCart) {
                        this.cashReceived = this.totalCart;
                    }
                },

                removeFromCart(idx) {
                    this.cart.splice(idx, 1);
                },

                stepUp(item) {
                    const step = item.unit === 'kg' ? 0.1 : 1;
                    item.qty = Math.round((Number(item.qty || 0) + step) * 10) / 10;
                    if (this.cashReceived === 0 || this.cashReceived < this.totalCart) {
                        this.cashReceived = this.totalCart;
                    }
                },

                stepDown(item, idx) {
                    const step = item.unit === 'kg' ? 0.1 : 1;
                    const newQty = Math.round((Number(item.qty || 0) - step) * 10) / 10;
                    if (newQty <= 0) {
                        if (confirm('Hapus ' + item.name + ' dari pesanan?')) {
                            this.removeFromCart(idx);
                        }
                    } else {
                        item.qty = newQty;
                    }
                },

                get totalCart() {
                    return this.cart.reduce((sum, it) => sum + (it.qty * it.price), 0);
                },

                get changeAmount() {
                    const change = this.cashReceived - this.totalCart;
                    return change > 0 ? change : 0;
                },

                formatRupiah(number) {
                    return Math.round(number).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                },

                printReceipt() {
                    if (this.cart.length > 0) {
                        this.showReceiptModal = true;
                    } else if (this.recentOrderId) {
                        window.open('/admin/orders/' + this.recentOrderId + '/receipt', '_blank');
                    } else {
                        alert('⚠️ Keranjang belanja masih kosong! Silakan klik "+ Tambah" pada layanan cucian di sebelah kiri untuk mencetak struk nota kasir.');
                    }
                },

                triggerPrintThermal() {
                    window.print();
                },

                sendWhatsAppReceipt() {
                    if (this.customerType === 'member' && !this.selectedCustomer) {
                        alert('⚠️ Anda berada di mode Member Rodeo. Silakan pilih akun member di kolom pencarian atau ganti ke tab "Pelanggan Walk-In".');
                        if (this.$refs.customerInput) {
                            this.$refs.customerInput.focus();
                        }
                        return;
                    }
                    if (this.cart.length === 0) {
                        alert('⚠️ Keranjang cucian masih kosong! Silakan pilih minimal 1 layanan cucian di sebelah kiri.');
                        return;
                    }

                    let rawPhone = '';
                    if (this.customerType === 'member') {
                        rawPhone = this.selectedCustomer.phone ? this.selectedCustomer.phone.replace(/[^0-9]/g, '') : '';
                    } else {
                        rawPhone = this.walkinPhone ? this.walkinPhone.replace(/[^0-9]/g, '') : '';
                    }

                    if (!rawPhone || rawPhone.length < 8) {
                        alert('⚠️ Silakan masukkan nomor WhatsApp pelanggan di kolom No. WhatsApp untuk mengirim nota via WA!');
                        return;
                    }

                    const intlPhone = rawPhone.startsWith('0') ? '62' + rawPhone.substring(1) : (rawPhone.startsWith('62') ? rawPhone : '62' + rawPhone);
                    
                    let text = `*NOTA TRANSAKSI - RODEO LAUNDRY*\n`;
                    text += `No. Nota: #{{ $invoiceNumber }}\n`;
                    text += `Pelanggan: ${this.activeCustomerName}\n`;
                    text += `Metode Pembayaran: ${this.paymentMethod.toUpperCase()}\n`;
                    text += `--------------------------------\n`;
                    this.cart.forEach(it => {
                        text += `• ${it.name} (${it.qty} ${it.unit}) : Rp ${this.formatRupiah(it.qty * it.price)}\n`;
                    });
                    text += `--------------------------------\n`;
                    text += `*TOTAL TAGIHAN: Rp ${this.formatRupiah(this.totalCart)}*\n\n`;
                    text += `Terima kasih telah mempercayakan pakaian Anda kepada Rodeo Laundry!\n`;
                    text += `📍 Gg. Rodeo, Sumberejo, Kota Batu | WA: +62 813-5805-5257`;

                    const url = `https://wa.me/${intlPhone}?text=${encodeURIComponent(text)}`;
                    window.open(url, '_blank');
                },

                submitOrder() {
                    if (this.customerType === 'member' && !this.selectedCustomer) {
                        alert('⚠️ Anda memilih mode Member Rodeo. Harap pilih akun member di kolom pencarian atau ganti ke tab "Pelanggan Walk-In" jika pelanggan belum punya akun.');
                        if (this.$refs.customerInput) {
                            this.$refs.customerInput.focus();
                        }
                        return;
                    }
                    if (this.cart.length === 0) {
                        alert('⚠️ Keranjang cucian masih kosong! Silakan klik "+ Tambah" pada katalog layanan sebelah kiri.');
                        return;
                    }
                    document.querySelector('form[action="{{ route('admin.pos.order') }}"]').submit();
                }
            };
        }
    </script>
</x-admin-layout>
