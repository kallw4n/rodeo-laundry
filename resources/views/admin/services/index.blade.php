<x-admin-layout title="Kelola Layanan">
    <div x-data="serviceManager()" class="space-y-6">

        <!-- ================= PAGE HEADER & CTA ================= -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-[#1E1B4B] tracking-tight">Kelola Layanan</h1>
                <p class="text-sm text-[#64748B] mt-1">Daftar layanan dan tarif laundry di Rodeo Laundry Malang</p>
            </div>
            <button type="button"
                    @click="openAddModal()"
                    class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-[#1E1B4B] hover:bg-[#2A2663] text-white text-sm font-bold shadow-md hover:shadow-lg transition">
                <svg class="w-4 h-4 text-[#BFF108]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Layanan</span>
            </button>
        </div>

        <!-- ================= SEARCH & FILTER CONTROLS ================= -->
        <div class="bg-white rounded-3xl p-4 border border-[#E9ECF4] shadow-sm">
            <form method="GET" action="{{ route('admin.services.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
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
                           placeholder="Cari nama layanan, deskripsi tarif..."
                           class="w-full pl-10 pr-4 py-2.5 bg-[#F8F9FD] border border-[#E9ECF4] rounded-2xl text-sm text-[#1E1B4B] focus:outline-none focus:border-[#1E1B4B] focus:ring-1 focus:ring-[#1E1B4B] transition">
                </div>

                <!-- Filter Kategori -->
                <div class="relative min-w-[180px]">
                    <select name="category"
                            onchange="this.form.submit()"
                            class="w-full py-2.5 pl-4 pr-10 bg-[#F8F9FD] border border-[#E9ECF4] rounded-2xl text-xs font-bold text-[#1E1B4B] focus:outline-none focus:border-[#1E1B4B] transition appearance-none cursor-pointer">
                        <option value="all" {{ request('category') == 'all' || !request('category') ? 'selected' : '' }}>Semua Layanan</option>
                        <option value="Kiloan" {{ request('category') == 'Kiloan' ? 'selected' : '' }}>Kiloan</option>
                        <option value="Satuan" {{ request('category') == 'Satuan' ? 'selected' : '' }}>Satuan</option>
                        <option value="Sepatu" {{ request('category') == 'Sepatu' ? 'selected' : '' }}>Sepatu</option>
                        <option value="Dry Clean" {{ request('category') == 'Dry Clean' ? 'selected' : '' }}>Dry Clean</option>
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
                        <option value="id_asc" {{ request('sort') == 'id_asc' || !request('sort') ? 'selected' : '' }}>ID Terkecil</option>
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
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
                            <th class="py-4 px-6 rounded-tl-2xl w-16">ID</th>
                            <th class="py-4 px-6">Nama Layanan / Item</th>
                            <th class="py-4 px-6">Harga</th>
                            <th class="py-4 px-6">Deskripsi</th>
                            <th class="py-4 px-6 text-center rounded-tr-2xl w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F0F2F8] text-xs">
                        @forelse($services as $item)
                            <tr class="hover:bg-[#F8F9FD]/80 transition">
                                <td class="py-4 px-6 font-extrabold text-[#1E1B4B] text-sm">
                                    {{ $item->id }}
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-[#BFF108] ring-4 ring-[#BFF108]/20 flex-shrink-0"></span>
                                        <span class="font-extrabold text-[#1E1B4B] text-sm">{{ $item->name }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="inline-block px-3.5 py-1.5 bg-[#F1F3FA] text-[#1E1B4B] font-extrabold rounded-full text-xs">
                                        Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-[#64748B] max-w-md">
                                    {{ $item->description ?? 'Layanan standar Rodeo Laundry.' }}
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Edit Button -->
                                        <button type="button"
                                                @click="openEditModal({{ json_encode($item) }})"
                                                class="w-8 h-8 rounded-full bg-[#EEF0FA] hover:bg-[#1E1B4B] text-[#1E1B4B] hover:text-white flex items-center justify-center transition shadow-sm"
                                                title="Edit Layanan">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </button>

                                        <!-- Delete Button -->
                                        <form method="POST" action="{{ route('admin.services.destroy', $item->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan {{ addslashes($item->name) }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="w-8 h-8 rounded-full bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white flex items-center justify-center transition shadow-sm"
                                                    title="Hapus Layanan">
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
                                <td colspan="5" class="py-12 text-center text-[#94A3B8]">
                                    Belum ada data layanan ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination -->
            <div class="p-5 border-t border-[#F0F2F8] flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-[#64748B]">
                <div>
                    Menampilkan <span class="font-bold text-[#1E1B4B]">{{ $services->count() }}</span> dari <span class="font-bold text-[#1E1B4B]">{{ $totalServices }}</span> layanan aktif
                </div>
                <div>
                    {{ $services->links() }}
                </div>
            </div>
        </div>

        <!-- ================= MODAL TAMBAH / EDIT LAYANAN ================= -->
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
                 class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-[#E9ECF4] space-y-6 relative">

                <!-- Header Modal -->
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-[#BFF108] text-[#1E1B4B] flex items-center justify-center shadow">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-xl font-extrabold text-[#1E1B4B]" x-text="isEdit ? 'Edit Layanan' : 'Tambah Layanan Baru'"></h2>
                                <span class="px-2 py-0.5 bg-[#1E1B4B] text-white text-[10px] font-extrabold tracking-wider rounded-md">POS READY</span>
                            </div>
                            <p class="text-xs text-[#64748B] mt-0.5">Tambahkan paket cuci atau item baru ke dalam daftar katalog & POS kasir.</p>
                        </div>
                    </div>
                    <button type="button" @click="showModal = false" class="w-8 h-8 rounded-full bg-[#F1F3FA] hover:bg-[#E2E6F2] text-[#64748B] flex items-center justify-center transition">
                        ✕
                    </button>
                </div>

                <!-- Top Row: ID Layanan & Category Pills -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pt-1">
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-[#94A3B8]">ID LAYANAN</div>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-sm font-extrabold text-[#1E1B4B]" x-text="'# ID: ' + formId"></span>
                            <span class="px-2 py-0.5 bg-[#F1F3FA] text-[#64748B] rounded-full text-[10px] font-bold">AUTO</span>
                        </div>
                    </div>

                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-[#94A3B8] mb-1 sm:text-right">KATEGORI LAYANAN</div>
                        <div class="flex flex-wrap items-center gap-1.5">
                            <template x-for="cat in ['Kiloan', 'Satuan', 'Sepatu / Tas', 'Karpet']" :key="cat">
                                <button type="button"
                                        @click="selectedCategory = cat"
                                        :class="selectedCategory === cat ? 'bg-[#1E1B4B] text-white' : 'bg-[#F1F3FA] text-[#64748B] hover:bg-[#E2E6F2]'"
                                        class="px-2.5 py-1 rounded-full text-xs font-bold transition"
                                        x-text="cat"></button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form :action="isEdit ? '{{ url('admin/services') }}/' + editId : '{{ route('admin.services.store') }}'" method="POST" class="space-y-4">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <!-- Nama Layanan -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-[#1E1B4B]">Nama Layanan / Item</label>
                            <span class="text-[11px] text-rose-500 font-semibold">*Wajib diisi</span>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#94A3B8]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/><path d="M12 3v3m0 12v3M3 12h3m12 0h3"/></svg>
                            </div>
                            <input type="text"
                                   name="name"
                                   x-model="formData.name"
                                   required
                                   placeholder="Contoh: Cuci Kiloan Super Wangi"
                                   class="w-full pl-10 pr-3.5 py-2.5 bg-[#F8F9FD] border border-[#E9ECF4] rounded-2xl text-xs text-[#1E1B4B] focus:outline-none focus:border-[#1E1B4B]">
                        </div>
                    </div>

                    <!-- Row: Harga & Satuan -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-[#1E1B4B]">Harga Layanan (IDR)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-xs font-extrabold text-[#1E1B4B]">Rp</span>
                                <input type="number"
                                       name="price"
                                       x-model="formData.price"
                                       required
                                       placeholder="18000"
                                       class="w-full pl-10 pr-3.5 py-2.5 bg-[#F8F9FD] border border-[#E9ECF4] rounded-2xl text-xs font-extrabold text-[#1E1B4B] focus:outline-none focus:border-[#1E1B4B]">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-[#1E1B4B]">Unit Satuan Tarif</label>
                            <div class="flex items-center gap-1.5 pt-0.5">
                                <template x-for="unit in ['per Kg', 'per Pcs', 'per Meter']" :key="unit">
                                    <button type="button"
                                            @click="selectedUnit = unit"
                                            :class="selectedUnit === unit ? 'bg-[#1E1B4B] text-white' : 'bg-[#F1F3FA] text-[#64748B] hover:bg-[#E2E6F2]'"
                                            class="flex-1 py-2 rounded-2xl text-xs font-bold transition text-center"
                                            x-text="unit"></button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-[#1E1B4B]">Deskripsi Layanan</label>
                            <span class="text-[11px] text-[#94A3B8]">Maks 150 karakter</span>
                        </div>
                        <textarea name="description"
                                  x-model="formData.description"
                                  rows="3"
                                  maxlength="150"
                                  placeholder="Pencucian premium dengan deterjen anti-bakteri, softener aroma floral segar, dan setrika uap presisi. Selesai 2 hari kerja."
                                  class="w-full p-3.5 bg-[#F8F9FD] border border-[#E9ECF4] rounded-2xl text-xs text-[#1E1B4B] focus:outline-none focus:border-[#1E1B4B]"></textarea>
                    </div>

                    <!-- Active Toggle Box -->
                    <div class="bg-[#F8F9FD] border border-[#EEF0FA] rounded-2xl p-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#E8FAD6] text-[#22C55E] flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <div class="text-xs font-extrabold text-[#1E1B4B]">Status Layanan Aktif</div>
                                <div class="text-[11px] text-[#64748B]">Langsung sinkron & tampil pada mesin POS Kasir cabang Malang</div>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#BFF108]"></div>
                        </label>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="pt-3 border-t border-[#F0F2F8] flex items-center justify-end gap-2">
                        <button type="button" @click="showModal = false" class="px-5 py-2.5 rounded-2xl text-xs font-bold text-[#64748B] hover:bg-[#F1F3FA] transition">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-2xl bg-[#1E1B4B] hover:bg-[#2A2663] text-white text-xs font-bold shadow transition flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#BFF108]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span x-text="isEdit ? 'Perbarui Layanan' : 'Simpan Layanan'"></span>
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>

    <script>
        function serviceManager() {
            return {
                showModal: false,
                isEdit: false,
                editId: null,
                formId: '{{ $nextServiceId }}',
                selectedCategory: 'Kiloan',
                selectedUnit: 'per Kg',
                formData: {
                    name: '',
                    price: '',
                    description: ''
                },

                openAddModal() {
                    this.isEdit = false;
                    this.editId = null;
                    this.formId = '{{ $nextServiceId }}';
                    this.selectedCategory = 'Kiloan';
                    this.selectedUnit = 'per Kg';
                    this.formData = {
                        name: '',
                        price: '',
                        description: ''
                    };
                    this.showModal = true;
                },

                openEditModal(item) {
                    this.isEdit = true;
                    this.editId = item.id;
                    this.formId = item.id;
                    this.formData = {
                        name: item.name || '',
                        price: item.price || '',
                        description: item.description || ''
                    };
                    this.showModal = true;
                }
            };
        }
    </script>
</x-admin-layout>
