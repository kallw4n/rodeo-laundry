<x-admin-layout title="Point of Sale (POS) / Kasir">
    <div x-data="posApp()" class="space-y-6">

        <!-- ================= PAGE HEADER ================= -->
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-[#1E1B4B] tracking-tight">Point of Sale (POS) / Kasir</h1>
            <p class="text-sm text-[#64748B] mt-1">Input transaksi cepat, timbang cucian digital, dan cetak nota kasir.</p>
        </div>

        <!-- ================= MAIN CONTENT 2-COLUMNS ================= -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- LEFT COLUMN: SEARCH + CUSTOMER + CATALOG (col-span-7 or 8) -->
            <div class="lg:col-span-7 xl:col-span-8 space-y-6">

                <!-- 1. CUSTOMER SEARCH & SELECTOR -->
                <div class="bg-white rounded-3xl p-5 border border-[#E9ECF4] shadow-sm space-y-4">
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <!-- Search Box with autocomplete -->
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#94A3B8]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text"
                                   x-model="customerQuery"
                                   @focus="customerDropdown = true"
                                   placeholder="Ketik nama atau no HP pelanggan..."
                                   class="w-full pl-10 pr-4 py-2.5 bg-[#F8F9FD] border border-[#E9ECF4] rounded-2xl text-sm text-[#1E1B4B] focus:outline-none focus:border-[#1E1B4B] focus:ring-1 focus:ring-[#1E1B4B] transition">

                            <!-- Autocomplete Dropdown -->
                            <div x-show="customerDropdown && filteredCustomers.length > 0"
                                 @click.away="customerDropdown = false"
                                 class="absolute z-30 left-0 right-0 mt-2 bg-white border border-[#E9ECF4] rounded-2xl shadow-xl max-h-60 overflow-y-auto">
                                <template x-for="c in filteredCustomers" :key="c.id">
                                    <div @click="selectCustomer(c)"
                                         class="p-3 hover:bg-[#F8F9FD] cursor-pointer border-b border-[#F0F2F8] last:border-0 flex items-center justify-between transition">
                                        <div>
                                            <div class="text-sm font-bold text-[#1E1B4B]" x-text="c.name"></div>
                                            <div class="text-xs text-[#64748B]" x-text="(c.phone || '-') + ' • ' + (c.city || 'Malang')"></div>
                                        </div>
                                        <span class="text-xs text-[#BFF108] bg-[#1E1B4B] px-2 py-0.5 rounded-full font-bold">Pilih</span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Scan Card Button -->
                        <button type="button" @click="scanCardModal = true" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-[#F1F3FA] hover:bg-[#E2E6F2] text-[#1E1B4B] text-xs font-bold rounded-2xl transition">
                            <svg class="w-4 h-4 text-[#1E1B4B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                            <span>Scan Kartu</span>
                        </button>
                    </div>

                    <!-- Selected Customer Pill / Card -->
                    <template x-if="selectedCustomer">
                        <div class="bg-[#F2EFF8]/60 border border-[#E4DFF0] rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
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
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#BFF108] text-[#1E1B4B] text-xs font-bold rounded-full">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                    </svg>
                                    <span>Gratis Antar-Jemput Aktif</span>
                                </span>
                                <button type="button" @click="clearCustomer()" class="p-1.5 text-[#94A3B8] hover:text-red-500 rounded-full hover:bg-white transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
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
                        <div class="bg-white rounded-3xl p-5 border border-[#E9ECF4] shadow-sm flex flex-col justify-between hover:shadow-md transition group">
                            <div>
                                <!-- Top row: Icon + Title & Price -->
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-2xl bg-[#EEF0FA] flex items-center justify-center text-[#1E1B4B] group-hover:bg-[#1E1B4B] group-hover:text-[#BFF108] transition">
                                            @if(str_contains($sName, 'sepatu'))
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                            @elseif(str_contains($sName, 'setrika'))
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                            @else
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/><path d="M12 3v3m0 12v3M3 12h3m12 0h3"/></svg>
                                            @endif
                                        </div>
                                        <div>
                                            <h3 class="text-base font-bold text-[#1E1B4B]">{{ $srv->name }}</h3>
                                            <span class="text-xs text-[#94A3B8]">⏱ {{ $duration }}</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-base font-extrabold text-[#1E1B4B]">Rp {{ number_format($srv->price, 0, ',', '.') }}</div>
                                        <div class="text-[11px] text-[#64748B]">/{{ $unit }}</div>
                                    </div>
                                </div>

                                <p class="text-xs text-[#64748B] mt-3 line-clamp-2">{{ $srv->description ?? 'Pencucian higienis dengan pewangi premium Rodeo.' }}</p>
                            </div>

                            <div class="mt-4 pt-3 border-t border-[#F0F2F8] flex items-center justify-between">
                                <span class="text-[11px] font-semibold text-[#1E1B4B]/70">{{ $tag }}</span>
                                <button type="button"
                                        @click="addToCart({{ $srv->id }}, '{{ addslashes($srv->name) }}', {{ (float)$srv->price }}, '{{ $unit }}')"
                                        class="px-4 py-1.5 rounded-full bg-[#F1F3FA] hover:bg-[#1E1B4B] text-[#1E1B4B] hover:text-[#BFF108] text-xs font-bold transition flex items-center gap-1.5">
                                    <span>+ Tambah</span>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 p-8 text-center bg-white rounded-3xl border border-dashed border-[#E9ECF4] text-[#94A3B8]">
                            Belum ada master layanan. Tambahkan di menu Kelola Layanan.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- RIGHT COLUMN: ORDER SUMMARY & CHECKOUT (col-span-5 or 4) -->
            <div class="lg:col-span-5 xl:col-span-4 sticky top-28">
                <div class="bg-white rounded-3xl p-6 border border-[#E9ECF4] shadow-lg space-y-5">

                    <!-- Header with Invoice Number -->
                    <div class="text-center pb-4 border-b border-[#F0F2F8]">
                        <span class="inline-block px-3 py-1 bg-[#EEF0FA] text-[#1E1B4B] text-[11px] font-extrabold tracking-wider uppercase rounded-full">
                            NOTA BARU #{{ $invoiceNumber }}
                        </span>
                        <h2 class="text-xl font-extrabold text-[#1E1B4B] mt-2 tracking-tight">Ringkasan Pesanan</h2>
                        <div class="text-xs text-[#94A3B8] mt-0.5">{{ \Carbon\Carbon::now()->isoFormat('D MMM Y, HH:mm') }} WIB</div>
                    </div>

                    <!-- Cart Item List -->
                    <div class="space-y-3 max-h-56 overflow-y-auto pr-1">
                        <template x-if="cart.length === 0">
                            <div class="p-6 text-center text-xs text-[#94A3B8]">
                                Belum ada item dipilih.<br>Klik "+ Tambah" pada katalog di samping.
                            </div>
                        </template>

                        <template x-for="(item, idx) in cart" :key="item.id">
                            <div class="p-3 bg-[#F8F9FD] rounded-2xl flex items-center justify-between border border-[#EEF0FA]">
                                <div class="flex-1 min-w-0 pr-2">
                                    <div class="text-xs font-bold text-[#1E1B4B] truncate" x-text="item.name"></div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <input type="number"
                                               x-model.number="item.qty"
                                               step="0.1"
                                               min="0.1"
                                               class="w-16 px-2 py-0.5 bg-white border border-[#E2E6F2] rounded-lg text-xs font-bold text-[#1E1B4B] text-center focus:outline-none focus:border-[#1E1B4B]">
                                        <span class="text-[11px] text-[#64748B]" x-text="item.unit + ' x Rp ' + formatRupiah(item.price)"></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-extrabold text-[#1E1B4B]" x-text="'Rp ' + formatRupiah(item.qty * item.price)"></span>
                                    <button type="button" @click="removeFromCart(idx)" class="p-1 text-[#94A3B8] hover:text-red-500 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Subtotal & Ongkir -->
                    <div class="pt-3 border-t border-[#F0F2F8] space-y-2 text-xs">
                        <div class="flex items-center justify-between text-[#64748B]">
                            <span x-text="'Subtotal (' + cart.length + ' Item)'"></span>
                            <span class="font-bold text-[#1E1B4B]" x-text="'Rp ' + formatRupiah(totalCart)"></span>
                        </div>
                        <div class="flex items-center justify-between text-[#64748B]">
                            <span>Ongkir Antar-Jemput</span>
                            <span class="px-2 py-0.5 bg-[#BFF108] text-[#1E1B4B] font-extrabold rounded-full text-[10px]">GRATIS</span>
                        </div>
                    </div>

                    <!-- Total Tagihan Box -->
                    <div class="bg-[#EEF0FA] p-4 rounded-2xl flex items-center justify-between">
                        <span class="text-xs font-bold text-[#1E1B4B]">Total Tagihan:</span>
                        <span class="text-xl font-extrabold text-[#1E1B4B]" x-text="'Rp ' + formatRupiah(totalCart)"></span>
                    </div>

                    <!-- Payment Method Picker (2x2 grid) -->
                    <div class="space-y-2">
                        <label class="text-[11px] font-extrabold uppercase tracking-wider text-[#94A3B8]">METODE PEMBAYARAN</label>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <button type="button"
                                    @click="paymentMethod = 'qris'"
                                    :class="paymentMethod === 'qris' ? 'bg-[#1E1B4B] text-white shadow-sm' : 'bg-[#F1F3FA] text-[#1E1B4B] hover:bg-[#E2E6F2]'"
                                    class="p-2.5 rounded-2xl font-bold flex items-center justify-center gap-1.5 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                <span>QRIS / E-Wallet</span>
                            </button>

                            <button type="button"
                                    @click="paymentMethod = 'cash'"
                                    :class="paymentMethod === 'cash' ? 'bg-[#1E1B4B] text-white shadow-sm' : 'bg-[#F1F3FA] text-[#1E1B4B] hover:bg-[#E2E6F2]'"
                                    class="p-2.5 rounded-2xl font-bold flex items-center justify-center gap-1.5 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>Tunai / Cash</span>
                            </button>

                            <button type="button"
                                    @click="paymentMethod = 'transfer'"
                                    :class="paymentMethod === 'transfer' ? 'bg-[#1E1B4B] text-white shadow-sm' : 'bg-[#F1F3FA] text-[#1E1B4B] hover:bg-[#E2E6F2]'"
                                    class="p-2.5 rounded-2xl font-bold flex items-center justify-center gap-1.5 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                                <span>Transfer Bank</span>
                            </button>

                            <button type="button"
                                    @click="paymentMethod = 'cod'"
                                    :class="paymentMethod === 'cod' ? 'bg-[#1E1B4B] text-white shadow-sm' : 'bg-[#F1F3FA] text-[#1E1B4B] hover:bg-[#E2E6F2]'"
                                    class="p-2.5 rounded-2xl font-bold flex items-center justify-center gap-1.5 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1"/></svg>
                                <span>Bayar Saat Antar</span>
                            </button>
                        </div>
                    </div>

                    <!-- Cash Input & Change calculation -->
                    <div class="bg-[#F8F9FD] p-3.5 rounded-2xl border border-[#EEF0FA] space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-[#64748B]">Uang Tunai Diterima:</span>
                            <div class="flex items-center gap-1">
                                <span class="font-bold text-[#1E1B4B]">Rp</span>
                                <input type="number"
                                       x-model.number="cashReceived"
                                       class="w-24 px-2 py-1 bg-white border border-[#E2E6F2] rounded-lg text-xs font-bold text-[#1E1B4B] text-right focus:outline-none focus:border-[#1E1B4B]">
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-xs pt-1 border-t border-[#E9ECF4]">
                            <span class="text-[#64748B]">Kembalian Kasir:</span>
                            <span class="text-sm font-extrabold text-emerald-600" x-text="'Rp ' + formatRupiah(changeAmount)"></span>
                        </div>
                    </div>

                    <!-- FORM SUBMISSION -->
                    <form method="POST" action="{{ route('admin.pos.order') }}" @submit.prevent="submitOrder()">
                        @csrf
                        <input type="hidden" name="customer_id" :value="selectedCustomer ? selectedCustomer.id : ''">
                        <input type="hidden" name="payment_method" :value="paymentMethod">
                        <input type="hidden" name="cash_received" :value="cashReceived">

                        <!-- JSON Items payload -->
                        <template x-for="(it, i) in cart" :key="i">
                            <div>
                                <input type="hidden" :name="'items[' + i + '][id]'" :value="it.id">
                                <input type="hidden" :name="'items[' + i + '][qty]'" :value="it.qty">
                            </div>
                        </template>

                        <!-- Actions -->
                        <div class="space-y-2.5">
                            <button type="submit"
                                    :disabled="cart.length === 0 || !selectedCustomer"
                                    :class="cart.length === 0 || !selectedCustomer ? 'opacity-50 cursor-not-allowed' : 'hover:scale-[1.01] active:scale-[0.99]'"
                                    class="w-full py-3.5 px-4 rounded-2xl bg-[#BFF108] text-[#1E1B4B] font-extrabold text-sm shadow-md transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                <span>Simpan & Cetak Struk</span>
                            </button>

                            <button type="button"
                                    @click="alert('Draft pesanan berhasil disimpan sementara ke memori kasir!')"
                                    class="w-full py-3 px-4 rounded-2xl bg-[#EEF0FA] hover:bg-[#E2E6F2] text-[#1E1B4B] font-bold text-xs transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span>Simpan Jadi Draft / SPK Cuci</span>
                            </button>
                        </div>
                    </form>

                    <!-- Hardware Status Footer -->
                    <div class="pt-3 border-t border-[#F0F2F8] flex items-center justify-center gap-4 text-[11px] font-semibold text-[#64748B]">
                        <div class="flex items-center gap-1 text-emerald-600">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Printer Kasir Siap</span>
                        </div>
                        <span>•</span>
                        <div class="flex items-center gap-1 text-emerald-600">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <span>WhatsApp Otomatis</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>

    <!-- Pos Controller Javascript via Alpine -->
    <script>
        function posApp() {
            const rawCustomers = @json($customers);

            return {
                customers: rawCustomers,
                customerQuery: '',
                customerDropdown: false,
                selectedCustomer: rawCustomers.length > 0 ? rawCustomers[0] : null,
                cart: [
                    { id: 1, name: 'Cuci Komplit Kiloan', price: 8000, qty: 4.8, unit: 'kg' },
                    { id: 3, name: 'Jas / Blazer Dry Clean', price: 35000, qty: 1, unit: 'pcs' }
                ],
                paymentMethod: 'qris',
                cashReceived: 75000,
                scanCardModal: false,

                get filteredCustomers() {
                    if (!this.customerQuery) return this.customers.slice(0, 5);
                    const q = this.customerQuery.toLowerCase();
                    return this.customers.filter(c => 
                        (c.name && c.name.toLowerCase().includes(q)) || 
                        (c.phone && c.phone.includes(q))
                    ).slice(0, 5);
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
                            price: price,
                            qty: unit === 'kg' ? 2.0 : 1,
                            unit: unit
                        });
                    }
                },

                removeFromCart(idx) {
                    this.cart.splice(idx, 1);
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

                submitOrder() {
                    if (!this.selectedCustomer) {
                        alert('Silakan pilih pelanggan terlebih dahulu!');
                        return;
                    }
                    if (this.cart.length === 0) {
                        alert('Keranjang belanja masih kosong!');
                        return;
                    }
                    document.querySelector('form[action="{{ route('admin.pos.order') }}"]').submit();
                }
            };
        }
    </script>
</x-admin-layout>
