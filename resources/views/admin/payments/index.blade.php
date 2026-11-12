<x-admin-layout title="Kelola Pembayaran">
    <div x-data="paymentManager()" class="space-y-6">

        <!-- ================= PAGE HEADER & CTA ================= -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-[#1E1B4B] tracking-tight">Kelola Pembayaran</h1>
                <p class="text-sm text-[#64748B] mt-1">Daftar transaksi, penagihan, dan status pembayaran laundry di Rodeo Laundry</p>
            </div>
            <button type="button"
                    @click="openAddModal()"
                    class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-[#1E1B4B] hover:bg-[#2A2663] text-white text-sm font-bold shadow-md hover:shadow-lg transition">
                <svg class="w-4 h-4 text-[#BFF108]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Catat Pembayaran</span>
            </button>
        </div>

        <!-- ================= SEARCH & FILTER CONTROLS ================= -->
        <div class="bg-white rounded-3xl p-5 border border-slate-300/80 shadow-[0_10px_30px_-5px_rgba(26,23,67,0.06),0_4px_12px_rgba(0,0,0,0.03)]">
            <form method="GET" action="{{ route('admin.payments.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
                <!-- Search text -->
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari nota, nama pelanggan..."
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300/80 rounded-2xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-[#1E1B4B] focus:ring-2 focus:ring-[#1E1B4B]/10 transition">
                </div>

                <!-- Filter Status -->
                <div class="relative min-w-[180px]">
                    <select name="status"
                            onchange="this.form.submit()"
                            class="w-full py-2.5 pl-4 pr-10 bg-slate-50 border border-slate-300/80 rounded-2xl text-xs font-bold text-slate-800 focus:outline-none focus:bg-white focus:border-[#1E1B4B] transition appearance-none cursor-pointer">
                        <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>Semua Status</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Lunas</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Belum Lunas</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>

                <!-- Sort Order -->
                <div class="relative min-w-[160px]">
                    <select name="sort"
                            onchange="this.form.submit()"
                            class="w-full py-2.5 pl-4 pr-10 bg-slate-50 border border-slate-300/80 rounded-2xl text-xs font-bold text-slate-800 focus:outline-none focus:bg-white focus:border-[#1E1B4B] transition appearance-none cursor-pointer">
                        <option value="latest" {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>Urutan: Terkini</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Urutan: Terlama</option>
                        <option value="amount_desc" {{ request('sort') == 'amount_desc' ? 'selected' : '' }}>Nominal Tertinggi</option>
                        <option value="amount_asc" {{ request('sort') == 'amount_asc' ? 'selected' : '' }}>Nominal Terendah</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                        </svg>
                    </div>
                </div>
            </form>
        </div>

        <!-- ================= DATA TABLE ================= -->
        <div class="bg-white rounded-3xl border border-slate-300/80 shadow-[0_10px_30px_-5px_rgba(26,23,67,0.06),0_4px_12px_rgba(0,0,0,0.03)] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#1E1B4B] text-white text-[11px] font-extrabold tracking-wider uppercase">
                            <th class="py-4 px-6 rounded-tl-2xl w-24">ID</th>
                            <th class="py-4 px-6">Nama Pelanggan</th>
                            <th class="py-4 px-6">Jenis Layanan</th>
                            <th class="py-4 px-6">Berat Cucian</th>
                            <th class="py-4 px-6">Harga</th>
                            <th class="py-4 px-6">Status</th>
                            <th class="py-4 px-6 text-center rounded-tr-2xl w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/80 text-xs">
                        @forelse($payments as $item)
                            @php
                                $customerName = $item->order->customer->name ?? 'Pelanggan Walk-in';
                                $items = $item->order->items ?? collect();
                                $serviceName = $items->count() > 1 
                                    ? ($items->first()->service->name ?? 'Layanan') . ' (+' . ($items->count() - 1) . ' item)'
                                    : ($items->first()->service->name ?? 'Paket Reguler');
                                $weight = $items->sum('quantity');
                                $unit = $items->first() && $items->first()->service 
                                    ? \App\Models\ServiceType::resolveUnit($items->first()->service->name) 
                                    : 'kg';
                                $isPaid = $item->status === 'completed';
                            @endphp
                            <tr class="hover:bg-indigo-50/40 transition">
                                <td class="py-4 px-6 font-extrabold text-[#1E1B4B] text-sm">
                                    #RD-{{ str_pad($item->order_id ?? $item->id, 4, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-extrabold text-[#1E1B4B] text-sm">{{ $customerName }}</div>
                                </td>
                                <td class="py-4 px-6 text-[#1E1B4B] font-medium">
                                    {{ $serviceName }}
                                </td>
                                <td class="py-4 px-6 text-[#64748B]">
                                    {{ $weight + 0 }} {{ $unit }}
                                </td>
                                <td class="py-4 px-6">
                                    <span class="inline-block px-3.5 py-1.5 bg-[#EEF2FF] text-[#1E1B4B] font-extrabold rounded-full text-xs">
                                        Rp {{ number_format($item->amount, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    @if($isPaid)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#E8FAD6] text-[#22C55E] text-xs font-bold rounded-full">
                                            <span class="w-2 h-2 rounded-full bg-[#22C55E]"></span>
                                            <span>Lunas</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#FEE2E2] text-[#EF4444] text-xs font-bold rounded-full">
                                            <span class="w-2 h-2 rounded-full bg-[#EF4444]"></span>
                                            <span>Belum Lunas</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Cetak Struk Button -->
                                        <button type="button"
                                                @click="window.open('/admin/orders/' + {{ $item->order_id }} + '/receipt', '_blank', 'width=450,height=650')"
                                                class="w-8 h-8 rounded-full bg-[#E0E7FF] hover:bg-[#4338CA] text-[#4338CA] hover:text-white flex items-center justify-center transition shadow-sm"
                                                title="Cetak Struk">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                            </svg>
                                        </button>

                                        <!-- Edit Button -->
                                        <button type="button"
                                                @click="openEditModal({{ json_encode($item) }})"
                                                class="w-8 h-8 rounded-full bg-[#EEF0FA] hover:bg-[#1E1B4B] text-[#1E1B4B] hover:text-white flex items-center justify-center transition shadow-sm"
                                                title="Ubah Pembayaran">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </button>

                                        <!-- Delete Button -->
                                        <form method="POST" action="{{ route('admin.payments.destroy', $item->id) }}" onsubmit="return confirm('Hapus catatan pembayaran Nota #{{ $item->order_id }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="w-8 h-8 rounded-full bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white flex items-center justify-center transition shadow-sm"
                                                    title="Hapus Pembayaran">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-[#94A3B8]">
                                    Belum ada transaksi pembayaran ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination -->
            <div class="p-5 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-600">
                <div>
                    Menampilkan <span class="font-bold text-[#1E1B4B]">{{ $payments->count() }}</span> dari <span class="font-bold text-[#1E1B4B]">{{ $totalPayments }}</span> transaksi pembayaran aktif
                </div>
                <div>
                    {{ $payments->links() }}
                </div>
            </div>
        </div>

        <!-- ================= MODAL CATAT PEMBAYARAN ================= -->
        <div x-show="showModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-black/40 backdrop-blur-sm flex items-center justify-center p-4"
             style="display: none;">

            <div @click.away="showModal = false"
                 class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl border border-slate-300/80 space-y-5 relative">

                <!-- Header Modal -->
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-[#1E1B4B] text-white flex items-center justify-center shadow">
                            <svg class="w-6 h-6 text-[#BFF108]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-extrabold text-[#1E1B4B]" x-text="isEdit ? 'Ubah Pembayaran' : 'Catat Pembayaran'"></h2>
                            <p class="text-xs text-[#64748B] mt-0.5">Rekam penerimaan pembayaran atau pelunasan tagihan nota cucian pelanggan.</p>
                        </div>
                    </div>
                    <button type="button" @click="showModal = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition">
                        ✕
                    </button>
                </div>

                <!-- Form -->
                <form :action="isEdit ? '{{ url('admin/payments') }}/' + editId : '{{ route('admin.payments.store') }}'" method="POST" class="space-y-4">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <input type="hidden" name="method" :value="formData.method">
                    <input type="hidden" name="status" :value="formData.status">

                    <!-- Row 1: Nomor Nota & Pelanggan -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold text-[#1E1B4B]">Nomor Nota / ID</label>
                                <span class="text-[10px] text-emerald-600 font-bold bg-[#E8FAD6] px-1.5 py-0.2 rounded-full">Otomatis</span>
                            </div>
                            <template x-if="!isEdit">
                                <select name="order_id"
                                        x-model="formData.order_id"
                                        @change="onOrderSelected($event.target.value)"
                                        required
                                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300/80 rounded-2xl text-xs font-extrabold text-[#1E1B4B] focus:outline-none focus:bg-white focus:border-[#1E1B4B]">
                                    <option value="">Pilih Nota Cucian</option>
                                    @foreach($unpaidOrders as $uo)
                                        <option value="{{ $uo->id }}" data-customer="{{ $uo->customer->name ?? '' }}" data-amount="{{ $uo->total_price }}">
                                            #RD-{{ str_pad($uo->id, 4, '0', STR_PAD_LEFT) }} ({{ $uo->customer->name ?? 'Umum' }} - Rp {{ number_format($uo->total_price, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </template>
                            <template x-if="isEdit">
                                <input type="text" :value="'#RD-' + String(formData.order_id || '').padStart(4, '0')" readonly class="w-full px-3.5 py-2.5 bg-slate-100 border border-slate-300/80 rounded-2xl text-xs font-extrabold text-[#1E1B4B]">
                            </template>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-[#1E1B4B]">Nama Pelanggan</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <input type="text"
                                       x-model="formData.customerName"
                                       readonly
                                       placeholder="Nama Pelanggan"
                                       class="w-full pl-10 pr-8 py-2.5 bg-slate-100 border border-slate-300/80 rounded-2xl text-xs font-bold text-[#1E1B4B]">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-emerald-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Rincian Layanan & Penimbangan Berat Fisik Outlet -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-[#1E1B4B] flex items-center gap-1.5">
                                <i class="fa-solid fa-scale-balanced text-indigo-600"></i>
                                <span>Rincian Layanan & Penimbangan Berat</span>
                            </label>
                            <template x-if="formData.items && formData.items.length > 0">
                                <span class="text-[10px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200/80 px-2 py-0.5 rounded-full flex items-center gap-1">
                                    <span x-text="formData.items.length + ' Item'"></span> &bull; Total <span x-text="totalWeight() + ' kg'"></span>
                                </span>
                            </template>
                        </div>

                        <!-- Jika ada rincian item layanan -->
                        <template x-if="formData.items && formData.items.length > 0">
                            <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                                <template x-for="(item, idx) in formData.items" :key="item.id || idx">
                                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:border-indigo-300 transition">
                                        <!-- Item Info & Tarif -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="font-extrabold text-xs text-[#1E1B4B] truncate" x-text="item.name"></span>
                                                <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded-md bg-slate-200 text-slate-700" x-text="item.unit"></span>
                                            </div>
                                            <div class="text-[11px] text-[#64748B] mt-0.5">
                                                Tarif: <span class="font-bold text-[#1E1B4B]" x-text="'Rp ' + formatRupiah(item.price) + ' / ' + item.unit"></span>
                                            </div>
                                        </div>

                                        <!-- Hidden Input ID & Service ID untuk pengiriman ke controller -->
                                        <input type="hidden" :name="'items[' + idx + '][id]'" :value="item.id || ''">
                                        <input type="hidden" :name="'items[' + idx + '][service_id]'" :value="item.service_id">

                                        <!-- Input Timbangan, Subtotal & Hapus Item -->
                                        <div class="flex items-center justify-between sm:justify-end gap-2.5 shrink-0">
                                            <div class="flex items-center">
                                                <input type="number"
                                                       :name="'items[' + idx + '][quantity]'"
                                                       x-model.number="item.quantity"
                                                       @input="recalculateTotals()"
                                                       step="0.1"
                                                       min="0.1"
                                                       required
                                                       class="w-20 px-2 py-1.5 bg-white border border-slate-300 rounded-l-xl text-xs font-extrabold text-center text-[#1E1B4B] focus:outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                                                <span class="px-2.5 py-1.5 bg-slate-100 border border-l-0 border-slate-300 rounded-r-xl text-[11px] font-extrabold text-slate-600" x-text="item.unit"></span>
                                            </div>
                                            <div class="text-right min-w-[76px]">
                                                <div class="text-[9px] uppercase font-bold text-slate-400">Subtotal</div>
                                                <div class="text-xs font-extrabold text-[#1E1B4B]" x-text="'Rp ' + formatRupiah((item.quantity || 0) * (item.price || 0))"></div>
                                            </div>
                                            <button type="button"
                                                    x-show="formData.items.length > 1"
                                                    @click="removeServiceItem(idx)"
                                                    title="Hapus layanan ini"
                                                    class="w-7 h-7 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white flex items-center justify-center transition text-xs font-bold shrink-0">
                                                ✕
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- Fallback jika nota belum ada rincian item -->
                        <template x-if="!formData.items || formData.items.length === 0">
                            <div class="p-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-500 text-center">
                                Tidak ada rincian item terpisah pada nota ini. Tambahkan layanan di bawah atau langsung edit Total Tagihan.
                            </div>
                        </template>

                        <!-- Opsi Tambah Layanan Susulan (Misal pelanggan bawa barang tambahan di toko) -->
                        <div class="pt-1 flex items-center gap-2">
                            <select x-model="newServiceId"
                                    class="flex-1 px-3 py-2 bg-slate-50 border border-dashed border-indigo-300 rounded-2xl text-xs font-bold text-[#1E1B4B] focus:outline-none focus:bg-white focus:border-[#1E1B4B]">
                                <option value="">+ Pilih layanan tambahan (misal: Gorden, Sepatu, dll)...</option>
                                <template x-for="svc in allServices" :key="svc.id">
                                    <option :value="svc.id" x-text="svc.name + ' — Rp ' + formatRupiah(svc.price) + ' / ' + (svc.unit || 'kg')"></option>
                                </template>
                            </select>
                            <button type="button"
                                    @click="addServiceItem()"
                                    :disabled="!newServiceId"
                                    :class="newServiceId ? 'bg-[#1E1B4B] hover:bg-[#2A2663] text-white cursor-pointer' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                                    class="px-3.5 py-2 rounded-2xl text-xs font-extrabold transition shrink-0 flex items-center gap-1">
                                <span>+ Tambah</span>
                            </button>
                        </div>
                    </div>

                    <!-- Row 3: Total Tagihan & Status Pills -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 items-end">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-[#1E1B4B]">Total Harga / Tagihan</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-xs font-extrabold text-[#1E1B4B]">Rp</span>
                                <input type="number"
                                       name="amount"
                                       x-model.number="formData.amount"
                                       required
                                       class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-300/80 rounded-2xl text-sm font-extrabold text-slate-800 focus:outline-none focus:bg-white focus:border-[#1E1B4B] focus:ring-2 focus:ring-[#1E1B4B]/10 transition">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-[#1E1B4B]">Status Pembayaran</label>
                            <div class="flex items-center gap-1.5">
                                <button type="button"
                                        @click="formData.status = 'completed'"
                                        :class="formData.status === 'completed' ? 'bg-[#E8FAD6] text-[#22C55E] border-emerald-300' : 'bg-slate-50 text-slate-600 border-slate-200'"
                                        class="flex-1 py-2 px-2 rounded-2xl text-xs font-bold border transition flex items-center justify-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-[#22C55E]"></span>
                                    <span>Lunas</span>
                                </button>
                                <button type="button"
                                        @click="formData.status = 'pending'"
                                        :class="formData.status === 'pending' ? 'bg-[#FEE2E2] text-[#EF4444] border-red-300' : 'bg-[#F8F9FD] text-[#64748B] border-[#E9ECF4]'"
                                        class="flex-1 py-2 px-2 rounded-2xl text-xs font-bold border transition flex items-center justify-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-[#EF4444]"></span>
                                    <span>Belum</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Row 4: Metode Pembayaran Cards (3 cards) -->
                    <div class="space-y-2">
                        <label class="text-[11px] font-extrabold uppercase tracking-wider text-[#94A3B8]">METODE PEMBAYARAN DIPILIH</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button"
                                    @click="formData.method = 'e-wallet'"
                                    :class="formData.method === 'e-wallet' ? 'bg-[#1E1B4B] text-white shadow-md' : 'bg-[#F8F9FD] text-[#1E1B4B] border border-[#E9ECF4]'"
                                    class="p-2.5 rounded-2xl text-center transition flex flex-col items-center justify-center">
                                <svg class="w-5 h-5 mb-1" :class="formData.method === 'e-wallet' ? 'text-[#BFF108]' : 'text-[#1E1B4B]'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                <span class="text-xs font-bold">QRIS Dinamis</span>
                                <span class="text-[9px] opacity-75">BCA/GoPay</span>
                            </button>

                            <button type="button"
                                    @click="formData.method = 'cash'"
                                    :class="formData.method === 'cash' ? 'bg-[#1E1B4B] text-white shadow-md' : 'bg-[#F8F9FD] text-[#1E1B4B] border border-[#E9ECF4]'"
                                    class="p-2.5 rounded-2xl text-center transition flex flex-col items-center justify-center">
                                <svg class="w-5 h-5 mb-1" :class="formData.method === 'cash' ? 'text-[#BFF108]' : 'text-[#1E1B4B]'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span class="text-xs font-bold">Tunai / Cash</span>
                                <span class="text-[9px] opacity-75">Di Kasir</span>
                            </button>

                            <button type="button"
                                    @click="formData.method = 'transfer'"
                                    :class="formData.method === 'transfer' ? 'bg-[#1E1B4B] text-white shadow-md' : 'bg-[#F8F9FD] text-[#1E1B4B] border border-[#E9ECF4]'"
                                    class="p-2.5 rounded-2xl text-center transition flex flex-col items-center justify-center">
                                <svg class="w-5 h-5 mb-1" :class="formData.method === 'transfer' ? 'text-[#BFF108]' : 'text-[#1E1B4B]'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                                <span class="text-xs font-bold">Transfer / EDC</span>
                                <span class="text-[9px] opacity-75">Bank/Debit</span>
                            </button>
                        </div>
                    </div>

                    <!-- Row 5: Uang Diterima & Kembalian -->
                    <div class="bg-[#F8F9FD] border border-[#EEF0FA] rounded-2xl p-4 flex items-center justify-between">
                        <div>
                            <div class="text-[11px] font-bold text-[#64748B]">Nominal Uang Diterima</div>
                            <div class="flex items-center gap-1 mt-1">
                                <span class="text-xs font-bold text-[#1E1B4B]">Rp</span>
                                <input type="number"
                                       x-model.number="cashReceived"
                                       placeholder="50000"
                                       class="w-28 px-2 py-1 bg-white border border-[#E2E6F2] rounded-xl text-xs font-bold text-[#1E1B4B] focus:outline-none">
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-[#94A3B8]">KEMBALIAN KASIR</div>
                            <div class="text-xl font-extrabold text-[#22C55E]" x-text="'Rp ' + formatRupiah(calculateChange())"></div>
                            <div class="text-[10px] text-[#64748B]">Kembalian siap diserahkan</div>
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="pt-3 border-t border-[#F0F2F8] flex items-center justify-between">
                        <button type="button" @click="showModal = false" class="px-4 py-2.5 rounded-2xl text-xs font-bold text-[#64748B] hover:bg-[#F1F3FA] transition">
                            Batal
                        </button>
                        <div class="flex items-center gap-2">
                            <button type="submit" name="submit_action" value="save" class="px-4 py-2.5 rounded-2xl bg-[#F1F3FA] hover:bg-[#E2E6F2] text-[#1E1B4B] text-xs font-bold transition">
                                Simpan Tanpa Cetak
                            </button>
                            <button type="submit" name="submit_action" value="print" class="px-5 py-2.5 rounded-2xl bg-[#1E1B4B] hover:bg-[#2A2663] text-white text-xs font-bold shadow transition flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-[#BFF108]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                <span>Simpan & Cetak Bukti</span>
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>

    @if(session('print_receipt_order_id'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                window.open('/admin/orders/{{ session('print_receipt_order_id') }}/receipt', '_blank', 'width=450,height=650');
            });
        </script>
    @endif

    <script>
        function paymentManager() {
            return {
                showModal: false,
                isEdit: false,
                editId: null,
                cashReceived: 50000,
                newServiceId: '',
                unpaidOrders: @json($unpaidOrders),
                allServices: @json($allServices),
                formData: {
                    order_id: '',
                    customerName: '',
                    items: [],
                    amount: 0,
                    method: 'cash',
                    status: 'completed'
                },

                openAddModal() {
                    this.isEdit = false;
                    this.editId = null;
                    this.cashReceived = 0;
                    this.newServiceId = '';
                    this.formData = {
                        order_id: '',
                        customerName: 'Pilih Nota Terlebih Dahulu',
                        items: [],
                        amount: 0,
                        method: 'cash',
                        status: 'completed'
                    };
                    this.showModal = true;
                },

                openEditModal(item) {
                    this.isEdit = true;
                    this.editId = item.id;
                    this.newServiceId = '';
                    this.cashReceived = Number(item.amount) || 0;

                    const rawItems = (item.order && item.order.items) ? item.order.items : [];
                    const orderItems = rawItems.map(it => {
                        const unitName = it.unit || (it.service && it.service.unit) || (it.service && it.service.name && it.service.name.toLowerCase().includes('kiloan') ? 'kg' : 'pcs');
                        return {
                            id: it.id,
                            service_id: it.service_id,
                            name: (it.service && it.service.name) ? it.service.name : 'Layanan Laundry',
                            price: Number(it.price) || (it.service ? Number(it.service.price) : 0),
                            quantity: Number(it.quantity) || 1,
                            unit: unitName
                        };
                    });

                    this.formData = {
                        order_id: item.order_id,
                        customerName: item.order && item.order.customer ? item.order.customer.name : 'Pelanggan Walk-in',
                        items: orderItems,
                        amount: Number(item.amount) || 0,
                        method: item.method || 'cash',
                        status: item.status || 'completed'
                    };
                    this.showModal = true;
                },

                onOrderSelected(orderId) {
                    const selected = this.unpaidOrders.find(o => String(o.id) === String(orderId));
                    if (selected) {
                        this.formData.customerName = (selected.customer && selected.customer.name) ? selected.customer.name : 'Pelanggan Rodeo';
                        const rawItems = selected.items || [];
                        this.formData.items = rawItems.map(it => {
                            const unitName = it.unit || (it.service && it.service.unit) || (it.service && it.service.name && it.service.name.toLowerCase().includes('kiloan') ? 'kg' : 'pcs');
                            return {
                                id: it.id,
                                service_id: it.service_id,
                                name: (it.service && it.service.name) ? it.service.name : 'Layanan Laundry',
                                price: Number(it.price) || (it.service ? Number(it.service.price) : 0),
                                quantity: Number(it.quantity) || 1,
                                unit: unitName
                            };
                        });
                        this.recalculateTotals();
                    }
                },

                recalculateTotals() {
                    if (this.formData.items && this.formData.items.length > 0) {
                        let sum = 0;
                        this.formData.items.forEach(it => {
                            const q = Number(it.quantity) || 0;
                            const p = Number(it.price) || 0;
                            sum += (q * p);
                        });
                        this.formData.amount = Math.round(sum);
                        if (this.formData.method === 'cash') {
                            if (!this.cashReceived || this.cashReceived < this.formData.amount) {
                                this.cashReceived = this.formData.amount;
                            }
                        }
                    }
                },

                addServiceItem() {
                    if (!this.newServiceId) return;
                    const svc = this.allServices.find(s => String(s.id) === String(this.newServiceId));
                    if (!svc) return;

                    const existing = this.formData.items.find(it => String(it.service_id) === String(svc.id));
                    if (existing) {
                        existing.quantity = Math.round(((Number(existing.quantity) || 0) + 1) * 100) / 100;
                    } else {
                        const unitName = svc.unit || (svc.name && svc.name.toLowerCase().includes('kiloan') ? 'kg' : 'pcs');
                        this.formData.items.push({
                            id: '',
                            service_id: svc.id,
                            name: svc.name,
                            price: Number(svc.price) || 0,
                            quantity: 1,
                            unit: unitName
                        });
                    }
                    this.newServiceId = '';
                    this.recalculateTotals();
                },

                removeServiceItem(idx) {
                    if (this.formData.items.length <= 1) return;
                    this.formData.items.splice(idx, 1);
                    this.recalculateTotals();
                },

                totalWeight() {
                    if (!this.formData.items || this.formData.items.length === 0) return 0;
                    const w = this.formData.items.reduce((acc, it) => acc + (Number(it.quantity) || 0), 0);
                    return Math.round(w * 100) / 100;
                },

                calculateChange() {
                    const c = (this.cashReceived || 0) - (this.formData.amount || 0);
                    return c > 0 ? c : 0;
                },

                formatRupiah(number) {
                    return Math.round(number || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }
            };
        }
    </script>
</x-admin-layout>
