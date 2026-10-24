<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Profil - Rodeo Laundry</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<!-- TAMBAHKAN editingAddress: null DI SINI -->
<body class="bg-[#FAF8FF] text-[#1A1743] font-sans antialiased pb-12" x-data="{ showAddForm: false, editingAddress: null }">

    <!-- HEADER -->
    <header class="fixed top-0 w-full h-16 md:h-20 bg-[#FAF8FF]/85 backdrop-blur-md shadow-sm z-50 border-b border-[#E2E7FF]/60">
        <div class="max-w-[1440px] mx-auto h-full px-4 md:px-6 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <div class="w-9 h-9 md:w-10 md:h-10 bg-[#2F2D59] rounded-xl flex items-center justify-center shadow-lg shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#bff108ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h3"/><path d="M17 6h.01"/><rect width="18" height="20" x="3" y="2" rx="2"/><circle cx="12" cy="13" r="5"/><path d="M12 18a2.5 2.5 0 0 0 0-5 2.5 2.5 0 0 1 0-5"/></svg>
                </div>
                <div>
                    <div class="text-base md:text-lg font-semibold leading-none">
                        Rodeo Laundry<span class="text-[#bff108ff]">.</span>
                    </div>
                    <div class="text-[9px] md:text-[10px] font-bold uppercase tracking-wider text-[#47464E] mt-1">Malang</div>
                </div>
            </a>
            <a href="{{ url('/') }}" class="text-sm font-bold text-[#1A1743] hover:underline hidden sm:block">Kembali ke Beranda</a>
        </div>
    </header>

    <main class="pt-24 md:pt-32 max-w-4xl mx-auto px-4 md:px-6">
        
        <!-- Judul Halaman -->
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl font-extrabold mb-2">Pengaturan Profil</h1>
            <p class="text-sm text-[#47464E]">Perbarui informasi data diri dan kelola daftar alamat pengiriman Anda.</p>
        </div>

        <!-- Notifikasi Sukses -->
        @if(session('success'))
            <div x-data="{ show: true }" 
                x-init="setTimeout(() => show = false, 3000)" 
                x-show="show"
                x-transition:leave="transition ease-in duration-50"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="mb-6 p-4 bg-[#BFF138]/30 border border-[#BFF138] rounded-2xl text-sm font-bold text-[#4E6700] flex items-center justify-between">
                <div>
                    <i class="fa-solid fa-circle-check mr-1"></i> {{ session('success') }}
                </div>

                <button @click="show = false" class="text-[#4E6700] hover:text-black transition">
                    <i class="fa-solid">x</i>
                </button>
            </div>
        @endif

        <!-- ================= 1. DATA DIRI ================= -->
        <div class="bg-white rounded-[24px] md:rounded-[32px] p-6 md:p-8 shadow-sm border border-[#E2E7FF]/60 mb-6">
            <h2 class="text-lg font-bold mb-4 flex items-center gap-2">
                <i class="fa-solid fa-user text-[#1A1743]"></i> Data Diri
            </h2>
            <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-[#1A1743] mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $customer->name) }}" required
                               class="w-full px-4 py-3 rounded-xl bg-[#FAF8FF] border-2 border-[#E2E7FF] focus:outline-none focus:border-[#BFF138] focus:bg-white transition-all text-sm">
                        @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#1A1743] mb-2">Nomor WhatsApp</label>
                        <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" required
                               class="w-full px-4 py-3 rounded-xl bg-[#FAF8FF] border-2 border-[#E2E7FF] focus:outline-none focus:border-[#BFF138] focus:bg-white transition-all text-sm">
                        @error('phone') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="flex justify-end pt-2">
                    <button type="submit" class="px-6 py-2.5 bg-[#1A1743] hover:bg-[#2F2D59] text-white text-sm font-bold rounded-xl transition-all shadow-md">
                        Simpan Data Diri
                    </button>
                </div>
            </form>
        </div>

        <!-- ================= 2. DAFTAR ALAMAT ================= -->
        <div class="bg-white rounded-[24px] md:rounded-[32px] p-6 md:p-8 shadow-sm border border-[#E2E7FF]/60">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div>
                    <h2 class="text-lg font-bold flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-[#1A1743]"></i> Daftar Alamat
                    </h2>
                    <p class="text-xs text-[#47464E] mt-1">Alamat ini akan digunakan saat Anda melakukan pemesanan.</p>
                </div>
                <button @click="showAddForm = !showAddForm" class="px-4 py-2 bg-[#BFF138] text-[#131B2E] text-sm font-bold rounded-full hover:bg-[#a8d832] transition shadow-sm flex items-center gap-2">
                    <i class="fa-solid" :class="showAddForm ? 'fa-minus' : 'fa-plus'"></i> 
                    <span x-text="showAddForm ? 'Tutup Form' : 'Tambah Alamat'"></span>
                </button>
            </div>

            <!-- Form Tambah Alamat -->
            <div x-show="showAddForm" x-transition class="mb-8 p-5 bg-[#FAF8FF] border border-[#E2E7FF] rounded-2xl">
                <form action="{{ route('addresses.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold mb-1">Label Alamat (Rumah/Kantor/Kos)</label>
                            <input type="text" name="label" required placeholder="Misal: Rumah" class="w-full px-3 py-2 rounded-lg border border-[#E2E7FF] text-sm focus:border-[#BFF138] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-1">Nama Penerima</label>
                            <input type="text" name="recipient_name" required placeholder="Nama Lengkap" class="w-full px-3 py-2 rounded-lg border border-[#E2E7FF] text-sm focus:border-[#BFF138] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-1">No HP Penerima</label>
                            <input type="text" name="phone" required placeholder="08xxxx" class="w-full px-3 py-2 rounded-lg border border-[#E2E7FF] text-sm focus:border-[#BFF138] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-1">Kota/Kecamatan</label>
                            <input type="text" name="city" required placeholder="Misal: Lowokwaru" class="w-full px-3 py-2 rounded-lg border border-[#E2E7FF] text-sm focus:border-[#BFF138] outline-none">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold mb-1">Alamat Lengkap</label>
                            <textarea name="address" required rows="2" placeholder="Jalan, No. Rumah, Patokan" class="w-full px-3 py-2 rounded-lg border border-[#E2E7FF] text-sm focus:border-[#BFF138] outline-none resize-none"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showAddForm = false" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-bold rounded-lg hover:bg-gray-300 transition">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-[#1A1743] text-white text-sm font-bold rounded-lg hover:bg-[#2F2D59] transition shadow-md">Simpan Alamat</button>
                    </div>
                </form>
            </div>

            <!-- List Alamat Tersimpan -->
            <div class="space-y-3">
                @forelse($addresses as $addr)
                    <div class="p-4 md:p-5 border border-[#E2E7FF] rounded-2xl flex flex-col sm:flex-row justify-between items-start gap-4 hover:border-[#BFF138] transition-colors bg-white">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                <!-- 1. LABEL ALAMAT (Tinggi disamakan) -->
                                <span class="inline-flex items-center px-2.5 py-1 bg-[#EAEDFF] text-[#1A1743] text-xs font-bold rounded-md leading-none">
                                    {{ $addr->label }}
                                </span>
                                
                                <!-- 2. BADGE UTAMA / TOMBOL JADIKAN UTAMA -->
                                @if($addr->is_default) 
                                    <!-- Kalau Utama, tampilkan badge hijau -->
                                    <span class="inline-flex items-center px-2.5 py-1 bg-[#C2F43B] text-[#4E6700] text-[10px] font-extrabold uppercase tracking-wide rounded-md leading-none">
                                        Utama
                                    </span>
                                @else
                                    <!-- Kalau bukan, tampilkan tombol abu-abu mungil -->
                                    <form action="{{ route('addresses.set-default', $addr->id) }}" method="POST" class="flex items-center m-0 p-0 leading-none">
                                        @csrf 
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center px-2.5 py-1 bg-[#F3F4F6] hover:bg-[#E5E7EB] text-[#4B5563] border border-[#E5E7EB] hover:border-[#D1D5DB] rounded-md text-[10px] font-bold transition-all leading-none">
                                            Jadikan Utama
                                        </button>
                                    </form> 
                                @endif
                            </div>
                            <p class="font-bold text-sm text-[#1A1743]">{{ $addr->recipient_name }} <span class="text-[#47464E] font-normal">({{ $addr->phone }})</span></p>
                            <p class="text-xs text-[#47464E] mt-1 leading-relaxed">{{ $addr->address }}, {{ $addr->city }}</p>
                        </div>
                        
                        <!-- Kumpulan Tombol Aksi -->
                        <div class="flex flex-col gap-2 shrink-0 items-end mt-2 sm:mt-0 w-full sm:w-auto">
                            
                            <!-- Edit dan Hapus -->
                            <div class="flex flex-row gap-2 w-full sm:w-auto">
                                <button type="button" @click='editingAddress = @json($addr)' class="flex-1 sm:flex-none px-3 py-1.5 text-xs font-bold text-[#1A1743] hover:text-white bg-[#F2F3FF] hover:bg-[#1A1743] rounded-lg transition">
                                    <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
                                </button>

                                <form action="{{ route('addresses.destroy', $addr->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus alamat ini?')" class="flex-1 sm:flex-none">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-full px-3 py-1.5 text-xs font-bold text-red-500 hover:text-white bg-red-50 hover:bg-red-500 rounded-lg transition">
                                        <i class="fa-solid fa-trash-can mr-1"></i> Hapus
                                    </button>
                                </form>
                            </div>
                            
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 bg-[#FAF8FF] border border-dashed border-[#E2E7FF] rounded-2xl">
                        <i class="fa-solid fa-map-location-dot text-3xl text-[#9895C8] mb-3"></i>
                        <p class="text-sm text-[#47464E]">Belum ada alamat tersimpan.<br>Silakan klik tombol "Tambah Alamat" di atas.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </main>

    <!-- ================= MODAL POPUP EDIT ALAMAT ================= -->
    <div x-show="editingAddress" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        
        <!-- TAMBAHKAN TEMPLATE X-IF INI -->
        <template x-if="editingAddress">
            <div @click.outside="editingAddress = null" class="bg-white rounded-[24px] p-6 md:p-8 max-w-lg w-full shadow-2xl max-h-[90vh] overflow-y-auto">
                
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-lg font-bold text-[#1A1743]">Edit Alamat</h3>
                    <button @click="editingAddress = null" class="text-gray-400 hover:text-gray-700">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <!-- Form Action diubah jadi lebih simpel, tanpa tanda tanya (?) -->
                <form :action="'/addresses/' + editingAddress.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold mb-1">Label Alamat</label>
                            <input type="text" name="label" x-model="editingAddress.label" required class="w-full px-3 py-2 rounded-lg border border-[#E2E7FF] text-sm focus:border-[#BFF138] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-1">Nama Penerima</label>
                            <input type="text" name="recipient_name" x-model="editingAddress.recipient_name" required class="w-full px-3 py-2 rounded-lg border border-[#E2E7FF] text-sm focus:border-[#BFF138] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-1">No HP Penerima</label>
                            <input type="text" name="phone" x-model="editingAddress.phone" required class="w-full px-3 py-2 rounded-lg border border-[#E2E7FF] text-sm focus:border-[#BFF138] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-1">Kota/Kecamatan</label>
                            <input type="text" name="city" x-model="editingAddress.city" required class="w-full px-3 py-2 rounded-lg border border-[#E2E7FF] text-sm focus:border-[#BFF138] outline-none">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold mb-1">Alamat Lengkap</label>
                            <textarea name="address" x-model="editingAddress.address" required rows="3" class="w-full px-3 py-2 rounded-lg border border-[#E2E7FF] text-sm focus:border-[#BFF138] outline-none resize-none"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="editingAddress = null" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-bold rounded-lg hover:bg-gray-300 transition">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-[#1A1743] text-white text-sm font-bold rounded-lg hover:bg-[#2F2D59] transition shadow-md">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </template>
    </div>

</body>
</html>