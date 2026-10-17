<x-admin-layout title="Kelola Pengiriman">
    <div x-data="deliveryManager()" class="space-y-6">

        <!-- ================= PAGE HEADER & CTA ================= -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-[#1E1B4B] tracking-tight">Kelola Pengiriman</h1>
                <p class="text-sm text-[#64748B] mt-1">Daftar cucian bersih siap antar yang belum dikirim ke alamat pelanggan</p>
            </div>
            <button type="button"
                    @click="openAddModal()"
                    class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-[#1E1B4B] hover:bg-[#2A2663] text-white text-sm font-bold shadow-md hover:shadow-lg transition">
                <svg class="w-4 h-4 text-[#BFF108]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Jadwalkan Antar Baru</span>
            </button>
        </div>

        <!-- ================= SEARCH & FILTER CONTROLS ================= -->
        <div class="bg-white rounded-3xl p-4 border border-[#E9ECF4] shadow-sm">
            <form method="GET" action="{{ route('admin.deliveries.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
                <!-- Search text -->
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#94A3B8]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari nota, pelanggan, alamat pengantaran..."
                           class="w-full pl-10 pr-4 py-2.5 bg-[#F8F9FD] border border-[#E9ECF4] rounded-2xl text-sm text-[#1E1B4B] focus:outline-none focus:border-[#1E1B4B] transition">
                </div>

                <!-- Filter Status Antar -->
                <div class="relative min-w-[180px]">
                    <select name="status"
                            onchange="this.form.submit()"
                            class="w-full py-2.5 pl-4 pr-10 bg-[#F8F9FD] border border-[#E9ECF4] rounded-2xl text-xs font-bold text-[#1E1B4B] focus:outline-none focus:border-[#1E1B4B] transition appearance-none cursor-pointer">
                        <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>Semua Status Antar</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Belum di Antar</option>
                        <option value="on-way" {{ request('status') == 'on-way' ? 'selected' : '' }}>Sedang di Antar</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai Terantar</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-[#94A3B8]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>

                <!-- Sort Order -->
                <div class="relative min-w-[160px]">
                    <select name="sort"
                            onchange="this.form.submit()"
                            class="w-full py-2.5 pl-4 pr-10 bg-[#F8F9FD] border border-[#E9ECF4] rounded-2xl text-xs font-bold text-[#1E1B4B] focus:outline-none focus:border-[#1E1B4B] transition appearance-none cursor-pointer">
                        <option value="latest" {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>Urutan: Terkini</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Urutan: Terlama</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-[#94A3B8]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                        </svg>
                    </div>
                </div>
            </form>
        </div>

        <!-- ================= DATA TABLE ================= -->
        <div class="bg-white rounded-3xl border border-[#E9ECF4] shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#565476] text-white text-[11px] font-extrabold tracking-wider uppercase">
                            <th class="py-4 px-6 rounded-tl-2xl w-24">ID Nota</th>
                            <th class="py-4 px-6">Pelanggan & Kontak</th>
                            <th class="py-4 px-6">Rincian Cucian</th>
                            <th class="py-4 px-6">Alamat Pengantaran</th>
                            <th class="py-4 px-6">Status Antar</th>
                            <th class="py-4 px-6 text-center rounded-tr-2xl w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F0F2F8] text-xs">
                        @forelse($deliveries as $item)
                            @php
                                $cust = $item->order->customer ?? null;
                                $custName = $cust->name ?? 'Pelanggan Rodeo';
                                $custPhone = $cust->phone ?? '0857-9912-4418';
                                $items = $item->order->items ?? collect();
                                $serviceName = $items->first()->service->name ?? 'Bedcover King + 8 kg Kiloan';
                                $status = $item->status ?? 'scheduled';
                            @endphp
                            <tr class="hover:bg-[#F8F9FD]/80 transition">
                                <td class="py-4 px-6 font-extrabold text-[#1E1B4B] text-sm">
                                    #RD-{{ 8900 + ($item->order_id ?? $item->id) }}
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-[#BFF108]"></span>
                                        <span class="font-extrabold text-[#1E1B4B] text-sm">{{ $custName }}</span>
                                    </div>
                                    <div class="text-[11px] text-[#64748B] mt-0.5">{{ $custPhone }}</div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-bold text-[#1E1B4B]">{{ $serviceName }}</div>
                                    <div class="text-[11px] text-[#64748B] mt-0.5">2 Item • Bungkus Vacuum Higenis</div>
                                </td>
                                <td class="py-4 px-6 text-[#1E1B4B] max-w-xs">
                                    <div class="truncate font-medium">{{ $item->address }}</div>
                                    <div class="text-[11px] text-[#94A3B8]">Malang Raya</div>
                                </td>
                                <td class="py-4 px-6">
                                    @if($status === 'scheduled')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#FEF3C7] text-[#D97706] text-xs font-bold rounded-full">
                                            <span class="w-2 h-2 rounded-full bg-[#D97706]"></span>
                                            <span>Belum di Antar</span>
                                        </span>
                                    @elseif($status === 'on-way')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#DBEAFE] text-[#2563EB] text-xs font-bold rounded-full">
                                            <span class="w-2 h-2 rounded-full bg-[#2563EB]"></span>
                                            <span>Sedang di Antar</span>
                                        </span>
                                    @elseif($status === 'completed')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#E8FAD6] text-[#22C55E] text-xs font-bold rounded-full">
                                            <span class="w-2 h-2 rounded-full bg-[#22C55E]"></span>
                                            <span>Selesai</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 text-gray-700 text-xs font-bold rounded-full">
                                            <span>{{ ucfirst($status) }}</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Quick Status Dispatch Button -->
                                        @if($status === 'scheduled')
                                            <form method="POST" action="{{ route('admin.deliveries.updateStatus', $item->id) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="on-way">
                                                <button type="submit"
                                                        title="Kirim Driver Sekarang"
                                                        class="w-8 h-8 rounded-full bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white flex items-center justify-center transition shadow-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @elseif($status === 'on-way')
                                            <form method="POST" action="{{ route('admin.deliveries.updateStatus', $item->id) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit"
                                                        title="Tandai Selesai Diterima"
                                                        class="w-8 h-8 rounded-full bg-emerald-50 hover:bg-emerald-600 text-emerald-600 hover:text-white flex items-center justify-center transition shadow-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Edit Button -->
                                        <button type="button"
                                                @click="openEditModal({{ json_encode($item) }})"
                                                class="w-8 h-8 rounded-full bg-[#EEF0FA] hover:bg-[#1E1B4B] text-[#1E1B4B] hover:text-white flex items-center justify-center transition shadow-sm"
                                                title="Edit Jadwal">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </button>

                                        <!-- Delete Button -->
                                        <form method="POST" action="{{ route('admin.deliveries.destroy', $item->id) }}" onsubmit="return confirm('Hapus antrian pengantaran Nota #{{ $item->order_id }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="w-8 h-8 rounded-full bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white flex items-center justify-center transition shadow-sm"
                                                    title="Hapus Antrian">
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
                                <td colspan="6" class="py-12 text-center text-[#94A3B8]">
                                    Belum ada antrian pengantaran cucian saat ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination -->
            <div class="p-5 border-t border-[#F0F2F8] flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-[#64748B]">
                <div>
                    Menampilkan <span class="font-bold text-[#1E1B4B]">{{ $deliveries->count() }}</span> dari <span class="font-bold text-[#1E1B4B]">{{ $totalDeliveries }}</span> cucian siap antar
                </div>
                <div>
                    {{ $deliveries->links() }}
                </div>
            </div>
        </div>

        <!-- ================= MODAL JADWALKAN ANTAR BARU ================= -->
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
                 class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl border border-[#E9ECF4] space-y-6 relative">

                <!-- Header Modal -->
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-[#1E1B4B] text-[#BFF108] flex items-center justify-center shadow">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-xl font-extrabold text-[#1E1B4B]" x-text="isEdit ? 'Ubah Rincian Pengantaran' : 'Jadwalkan Antar Baru'"></h2>
                                <span class="px-2 py-0.5 bg-[#BFF108] text-[#1E1B4B] text-[10px] font-extrabold tracking-wider rounded-md">EKSPEDISI LOKAL</span>
                            </div>
                            <p class="text-xs text-[#64748B] mt-0.5">Atur jadwal kurir dan rute pengantaran cucian bersih siap antar ke rumah pelanggan di Malang Raya.</p>
                        </div>
                    </div>
                    <button type="button" @click="showModal = false" class="w-8 h-8 rounded-full bg-[#F1F3FA] hover:bg-[#E2E6F2] text-[#64748B] flex items-center justify-center transition">
                        ✕
                    </button>
                </div>

                <!-- Form -->
                <form :action="isEdit ? '{{ url('admin/deliveries') }}/' + editId : '{{ route('admin.deliveries.store') }}'" method="POST" class="space-y-4">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <!-- Row 1: ID Nota & Customer Picker -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-[#1E1B4B]">ID Nota Cucian</label>
                            <template x-if="!isEdit">
                                <select name="order_id"
                                        x-model="formData.order_id"
                                        @change="onOrderSelected($event.target.value)"
                                        required
                                        class="w-full px-3.5 py-2.5 bg-[#F8F9FD] border border-[#E9ECF4] rounded-2xl text-xs font-extrabold text-[#1E1B4B] focus:outline-none">
                                    <option value="">Pilih Nota Siap Antar</option>
                                    @foreach($readyOrders as $ro)
                                        <option value="{{ $ro->id }}"
                                                data-customer="{{ $ro->customer->name ?? '' }}"
                                                data-phone="{{ $ro->customer->phone ?? '' }}"
                                                data-address="{{ $ro->customer->address ?? '' }}">
                                            #RD-{{ 8900 + $ro->id }} - {{ $ro->customer->name ?? 'Umum' }}
                                        </option>
                                    @endforeach
                                </select>
                            </template>
                            <template x-if="isEdit">
                                <input type="text" :value="'#RD-' + (8900 + Number(formData.order_id))" readonly class="w-full px-3.5 py-2.5 bg-[#F1F3FA] border border-[#E9ECF4] rounded-2xl text-xs font-extrabold text-[#1E1B4B]">
                            </template>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-[#1E1B4B]">Nama Pelanggan & WhatsApp</label>
                            <input type="text"
                                   x-model="formData.customerInfo"
                                   readonly
                                   class="w-full px-3.5 py-2.5 bg-[#F1F3FA] border border-[#E9ECF4] rounded-2xl text-xs font-bold text-[#1E1B4B]">
                        </div>
                    </div>

                    <!-- Row 2: Rincian Paket & Alamat -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-[#1E1B4B]">Rincian Cucian (Detail Paket)</label>
                            <div class="p-3 bg-[#F8F9FD] border border-[#E9ECF4] rounded-2xl text-xs text-[#1E1B4B] font-medium min-h-[72px]">
                                Bedcover King + 8 kg Kiloan (2 Item, Bungkus Vacuum Higenis, Wangi Ocean Fresh)
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-[#1E1B4B]">Alamat Lengkap Pengantaran <span class="text-rose-500">*</span></label>
                            <textarea name="address"
                                      x-model="formData.address"
                                      required
                                      rows="3"
                                      placeholder="Permata Jingga Blok CC-09, Tunggulwulung, Lowokwaru, Kota Malang"
                                      class="w-full p-3 bg-[#F8F9FD] border border-[#E9ECF4] rounded-2xl text-xs text-[#1E1B4B] focus:outline-none"></textarea>
                        </div>
                    </div>

                    <!-- Row 3: Driver, Slot Waktu & Status -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-[#1E1B4B]">Pilih Driver / Kurir <span class="text-rose-500">*</span></label>
                            <select name="driver"
                                    class="w-full px-3 py-2.5 bg-[#F8F9FD] border border-[#E9ECF4] rounded-2xl text-xs text-[#1E1B4B] focus:outline-none">
                                <option value="Bagus Ananta">Bagus Ananta (Vario N 4821 AC)</option>
                                <option value="Rian Santoso">Rian Santoso (Scoopy N 3190 BB)</option>
                                <option value="Hendra Wijaya">Hendra Wijaya (PCX N 5542 AB)</option>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-[#1E1B4B]">Jadwal & Slot Waktu</label>
                            <select name="time_slot"
                                    class="w-full px-3 py-2.5 bg-[#F8F9FD] border border-[#E9ECF4] rounded-2xl text-xs text-[#1E1B4B] focus:outline-none">
                                <option value="13:00 - 15:00">Kloter Siang (13:00 - 15:00)</option>
                                <option value="09:00 - 11:00">Kloter Pagi (09:00 - 11:00)</option>
                                <option value="16:00 - 18:00">Kloter Sore (16:00 - 18:00)</option>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-[#1E1B4B]">Status Antar Awal</label>
                            <select name="status"
                                    x-model="formData.status"
                                    class="w-full px-3 py-2.5 bg-[#F8F9FD] border border-[#E9ECF4] rounded-2xl text-xs text-[#1E1B4B] focus:outline-none">
                                <option value="scheduled">Belum di Antar</option>
                                <option value="on-way">Sedang di Antar</option>
                                <option value="completed">Selesai</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 4: Catatan Driver -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-[#1E1B4B]">Catatan Khusus Pengantaran</label>
                        <input type="text"
                               name="notes"
                               x-model="formData.notes"
                               placeholder="Titip di pos security perumahan jika rumah kosong, infokan via WA."
                               class="w-full px-3.5 py-2.5 bg-[#F8F9FD] border border-[#E9ECF4] rounded-2xl text-xs text-[#1E1B4B] focus:outline-none">
                    </div>

                    <!-- Notice Checkbox -->
                    <div class="flex items-center gap-2 text-xs text-[#1E1B4B] font-semibold pt-1">
                        <div class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span>Notifikasi WhatsApp otomatis terkirim ke customer</span>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="pt-3 border-t border-[#F0F2F8] flex items-center justify-end gap-2">
                        <button type="button" @click="showModal = false" class="px-5 py-2.5 rounded-2xl text-xs font-bold text-[#64748B] hover:bg-[#F1F3FA] transition">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-2xl bg-[#1E1B4B] hover:bg-[#2A2663] text-white text-xs font-bold shadow transition flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#BFF108]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            <span x-text="isEdit ? 'Perbarui Jadwal' : 'Konfirmasi & Jadwalkan Antar'"></span>
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>

    <script>
        function deliveryManager() {
            return {
                showModal: false,
                isEdit: false,
                editId: null,
                formData: {
                    order_id: '',
                    customerInfo: 'Rahmat Hidayatullah (0857-9912-4418)',
                    address: 'Permata Jingga Blok CC-09, Tunggulwulung, Lowokwaru, Kota Malang',
                    status: 'scheduled',
                    notes: ''
                },

                openAddModal() {
                    this.isEdit = false;
                    this.editId = null;
                    this.formData = {
                        order_id: '',
                        customerInfo: 'Pilih Nota Cucian Siap Antar',
                        address: '',
                        status: 'scheduled',
                        notes: ''
                    };
                    this.showModal = true;
                },

                openEditModal(item) {
                    this.isEdit = true;
                    this.editId = item.id;
                    const cust = item.order && item.order.customer ? item.order.customer : null;
                    this.formData = {
                        order_id: item.order_id,
                        customerInfo: (cust ? cust.name : 'Pelanggan') + ' (' + (cust ? cust.phone : '-') + ')',
                        address: item.address || '',
                        status: item.status || 'scheduled',
                        notes: ''
                    };
                    this.showModal = true;
                },

                onOrderSelected(orderId) {
                    const select = document.querySelector('select[name="order_id"]');
                    const opt = select.options[select.selectedIndex];
                    if (opt && opt.value) {
                        const name = opt.getAttribute('data-customer') || 'Pelanggan';
                        const phone = opt.getAttribute('data-phone') || '-';
                        this.formData.customerInfo = name + ' (' + phone + ')';
                        this.formData.address = opt.getAttribute('data-address') || 'Kota Malang';
                    }
                }
            };
        }
    </script>
</x-admin-layout>
