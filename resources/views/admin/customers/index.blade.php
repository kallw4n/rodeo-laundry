<x-admin-layout title="Kelola Pelanggan">
    <div x-data="customerManager()" class="space-y-6">

        <!-- ================= PAGE HEADER & CTA ================= -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-[#1E1B4B] tracking-tight">Kelola Pelanggan</h1>
                <p class="text-sm text-[#64748B] mt-1">Daftar direktori dan data pelanggan aktif Rodeo Laundry Malang.</p>
            </div>
            <button type="button"
                    @click="openAddModal()"
                    class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-[#1E1B4B] hover:bg-[#2A2663] text-white text-sm font-bold shadow-md hover:shadow-lg transition">
                <svg class="w-4 h-4 text-[#BFF108]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Pelanggan</span>
            </button>
        </div>

        <!-- ================= SEARCH & FILTER CONTROLS ================= -->
        <div class="bg-white rounded-3xl p-4 border border-[#E9ECF4] shadow-sm">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
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
                           placeholder="Cari nama, email, no. telepon, atau kota..."
                           class="w-full pl-10 pr-4 py-2.5 bg-[#F8F9FD] border border-[#E9ECF4] rounded-2xl text-sm text-[#1E1B4B] focus:outline-none focus:border-[#1E1B4B] focus:ring-1 focus:ring-[#1E1B4B] transition">
                </div>

                <!-- Filter Kota -->
                <div class="relative min-w-[200px]">
                    <select name="city"
                            onchange="this.form.submit()"
                            class="w-full py-2.5 pl-4 pr-10 bg-[#F8F9FD] border border-[#E9ECF4] rounded-2xl text-xs font-bold text-[#1E1B4B] focus:outline-none focus:border-[#1E1B4B] transition appearance-none cursor-pointer">
                        <option value="all" {{ request('city') == 'all' || !request('city') ? 'selected' : '' }}>Semua Kota (Malang Raya)</option>
                        <option value="Kota Malang" {{ request('city') == 'Kota Malang' ? 'selected' : '' }}>Kota Malang</option>
                        <option value="Kota Batu" {{ request('city') == 'Kota Batu' ? 'selected' : '' }}>Kota Batu</option>
                        <option value="Kab. Malang" {{ request('city') == 'Kab. Malang' ? 'selected' : '' }}>Kab. Malang</option>
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
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
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
                            <th class="py-4 px-6 rounded-tl-2xl">ID</th>
                            <th class="py-4 px-6">Pelanggan</th>
                            <th class="py-4 px-6">Email</th>
                            <th class="py-4 px-6">Telepon</th>
                            <th class="py-4 px-6">Kota</th>
                            <th class="py-4 px-6 text-center rounded-tr-2xl">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F0F2F8] text-xs">
                        @forelse($customers as $item)
                            <tr class="hover:bg-[#F8F9FD]/80 transition">
                                <td class="py-4 px-6">
                                    <div class="font-extrabold text-[#1E1B4B] text-sm">#RD-{{ 8900 + $item->id }}</div>
                                    <div class="text-[11px] text-[#94A3B8]">{{ $item->created_at ? $item->created_at->format('H:i') . ' WIB' : '10:00 WIB' }}</div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-bold text-[#1E1B4B] text-sm">{{ $item->name }}</div>
                                    <div class="text-[11px] text-[#64748B] truncate max-w-xs">{{ $item->address ?? 'Alamat Outlet' }}</div>
                                </td>
                                <td class="py-4 px-6 text-[#64748B]">
                                    {{ $item->email ?? '-' }}
                                </td>
                                <td class="py-4 px-6 font-medium text-[#1E1B4B]">
                                    {{ $item->phone ?? '-' }}
                                </td>
                                <td class="py-4 px-6">
                                    <span class="inline-block px-3 py-1 bg-[#EEF2FF] text-[#4F46E5] font-bold rounded-full text-xs">
                                        {{ $item->city ?? 'Kota Malang' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Edit Button -->
                                        <button type="button"
                                                @click="openEditModal({{ json_encode($item) }})"
                                                class="w-8 h-8 rounded-full bg-[#EEF0FA] hover:bg-[#1E1B4B] text-[#1E1B4B] hover:text-white flex items-center justify-center transition shadow-sm"
                                                title="Edit Pelanggan">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </button>

                                        <!-- Delete Button -->
                                        <form method="POST" action="{{ route('admin.customers.destroy', $item->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pelanggan {{ addslashes($item->name) }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="w-8 h-8 rounded-full bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white flex items-center justify-center transition shadow-sm"
                                                    title="Hapus Pelanggan">
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
                                    Belum ada data pelanggan ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination -->
            <div class="p-5 border-t border-[#F0F2F8] flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-[#64748B]">
                <div>
                    Menampilkan <span class="font-bold text-[#1E1B4B]">{{ $customers->count() }}</span> dari <span class="font-bold text-[#1E1B4B]">{{ $totalCustomers }}</span> pelanggan aktif
                </div>
                <div>
                    {{ $customers->links() }}
                </div>
            </div>
        </div>

        <!-- ================= MODAL TAMBAH / EDIT PELANGGAN ================= -->
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-extrabold text-[#1E1B4B]" x-text="isEdit ? 'Edit Data Pelanggan' : 'Tambah Pelanggan Baru'"></h2>
                            <p class="text-xs text-[#64748B] mt-0.5">Masukkan data identitas pelanggan baru untuk reservasi dan riwayat laundry.</p>
                        </div>
                    </div>
                    <button type="button" @click="showModal = false" class="w-8 h-8 rounded-full bg-[#F1F3FA] hover:bg-[#E2E6F2] text-[#64748B] flex items-center justify-center transition">
                        ✕
                    </button>
                </div>

                <!-- Auto ID Card -->
                <div class="bg-[#F8F9FD] border border-[#EEF0FA] rounded-2xl p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-[#1E1B4B] text-white flex items-center justify-center">
                            <svg class="w-4 h-4 text-[#BFF108]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-wider text-[#94A3B8]">ID PELANGGAN (SISTEM AUTO)</div>
                            <div class="text-base font-extrabold text-[#1E1B4B]" x-text="formCode"></div>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-[#E9ECF4] text-[#1E1B4B] text-xs font-semibold rounded-full shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-[#BFF108]"></span>
                        <span>Dibuat otomatis oleh sistem</span>
                    </span>
                </div>

                <!-- Form -->
                <form :action="isEdit ? '{{ url('admin/customers') }}/' + editId : '{{ route('admin.customers.store') }}'" method="POST" class="space-y-4">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <!-- Row 1: Nama & Email -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-[#1E1B4B]">Nama Lengkap <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#94A3B8]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <input type="text"
                                       name="name"
                                       x-model="formData.name"
                                       required
                                       placeholder="Contoh: Bambang Setyawan"
                                       class="w-full pl-10 pr-3.5 py-2.5 bg-[#F8F9FD] border border-[#E9ECF4] rounded-2xl text-xs text-[#1E1B4B] focus:outline-none focus:border-[#1E1B4B]">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-[#1E1B4B]">Email Pelanggan <span class="text-[#94A3B8] font-normal">(Opsional)</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#94A3B8]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <input type="email"
                                       name="email"
                                       x-model="formData.email"
                                       placeholder="nama@gmail.com"
                                       class="w-full pl-10 pr-3.5 py-2.5 bg-[#F8F9FD] border border-[#E9ECF4] rounded-2xl text-xs text-[#1E1B4B] focus:outline-none focus:border-[#1E1B4B]">
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Telepon & Kota -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-[#1E1B4B]">Nomor Telepon / WhatsApp <span class="text-rose-500">*</span></label>
                            <div class="flex items-center">
                                <span class="px-3 py-2.5 bg-[#EEF0FA] border border-r-0 border-[#E9ECF4] rounded-l-2xl text-xs font-bold text-[#1E1B4B]">+62</span>
                                <input type="text"
                                       name="phone"
                                       x-model="formData.phone"
                                       required
                                       placeholder="878-1123-9081"
                                       class="w-full px-3 py-2.5 bg-[#F8F9FD] border border-[#E9ECF4] rounded-r-2xl text-xs text-[#1E1B4B] focus:outline-none focus:border-[#1E1B4B]">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-[#1E1B4B]">Kota / Wilayah Layanan <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#94A3B8]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <select name="city"
                                        x-model="formData.city"
                                        required
                                        class="w-full pl-10 pr-8 py-2.5 bg-[#F8F9FD] border border-[#E9ECF4] rounded-2xl text-xs text-[#1E1B4B] focus:outline-none focus:border-[#1E1B4B] appearance-none cursor-pointer">
                                    <option value="Kota Malang">Kota Malang</option>
                                    <option value="Kota Batu">Kota Batu</option>
                                    <option value="Kab. Malang">Kab. Malang</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Alamat -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-[#1E1B4B]">Alamat Lengkap / Catatan Pengantaran <span class="text-rose-500">*</span></label>
                            <span class="text-[11px] text-[#94A3B8]">Untuk pickup & delivery</span>
                        </div>
                        <textarea name="address"
                                  x-model="formData.address"
                                  rows="3"
                                  required
                                  placeholder="Nama jalan, nomor rumah, RT/RW, nama kos/perumahan, atau patokan driver..."
                                  class="w-full p-3.5 bg-[#F8F9FD] border border-[#E9ECF4] rounded-2xl text-xs text-[#1E1B4B] focus:outline-none focus:border-[#1E1B4B]"></textarea>
                    </div>

                    <!-- Notice Pill -->
                    <div class="p-3 bg-[#EEF2FF] rounded-2xl flex items-start gap-2.5 text-xs text-[#4F46E5]">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Nota digital dan pelacakan progress cuci otomatis dikirimkan ke nomor WhatsApp pelanggan setelah disimpan.</span>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="pt-3 border-t border-[#F0F2F8] flex items-center justify-between">
                        <div class="flex items-center gap-1.5 text-[11px] text-[#94A3B8]">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span>Data terenkripsi aman</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="showModal = false" class="px-4 py-2.5 rounded-2xl text-xs font-bold text-[#64748B] hover:bg-[#F1F3FA] transition">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-2xl bg-[#1E1B4B] hover:bg-[#2A2663] text-white text-xs font-bold shadow transition flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#BFF108]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span x-text="isEdit ? 'Perbarui Pelanggan' : 'Simpan Pelanggan'"></span>
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>

    <script>
        function customerManager() {
            return {
                showModal: false,
                isEdit: false,
                editId: null,
                formCode: '{{ $nextCustomerCode }}',
                formData: {
                    name: '',
                    email: '',
                    phone: '',
                    city: 'Kota Malang',
                    address: ''
                },

                openAddModal() {
                    this.isEdit = false;
                    this.editId = null;
                    this.formCode = '{{ $nextCustomerCode }}';
                    this.formData = {
                        name: '',
                        email: '',
                        phone: '',
                        city: 'Kota Malang',
                        address: ''
                    };
                    this.showModal = true;
                },

                openEditModal(item) {
                    this.isEdit = true;
                    this.editId = item.id;
                    this.formCode = '#RD-' + (8900 + item.id);
                    this.formData = {
                        name: item.name || '',
                        email: item.email || '',
                        phone: (item.phone || '').replace(/^\+62/, ''),
                        city: item.city || 'Kota Malang',
                        address: item.address || ''
                    };
                    this.showModal = true;
                }
            };
        }
    </script>
</x-admin-layout>
