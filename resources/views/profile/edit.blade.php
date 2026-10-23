<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Profil - Rodeo Laundry</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FAF8FF] text-[#1A1743] font-sans antialiased pb-12">

    <!-- HEADER (Sama seperti halaman lain) -->
    <header class="fixed top-0 w-full h-16 md:h-20 bg-[#FAF8FF]/85 backdrop-blur-md shadow-sm z-50 border-b border-[#E2E7FF]/60">
        <div class="max-w-[1440px] mx-auto h-full px-4 md:px-6 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <div class="w-9 h-9 md:w-10 md:h-10 bg-[#2F2D59] flex items-center justify-center shadow-lg rounded-xl">
                    <div class="w-4 h-5 bg-[#BFF138]"></div>
                </div>
                <div class="text-base md:text-lg font-semibold leading-none">
                    Rodeo Laundry<span class="text-[#A7D717]">.</span>
                </div>
            </a>
            <a href="{{ url('/') }}" class="text-sm font-bold text-[#1A1743] hover:underline">Kembali ke Beranda</a>
        </div>
    </header>

    <main class="pt-24 md:pt-32 max-w-3xl mx-auto px-4 md:px-6">
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl font-extrabold mb-2">Pengaturan Profil</h1>
            <p class="text-sm text-[#47464E]">Perbarui informasi pribadi dan alamat penjemputan Anda.</p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-[#BFF138]/30 border border-[#BFF138] rounded-2xl text-sm font-bold text-[#4E6700]">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-[24px] md:rounded-[32px] p-6 md:p-10 shadow-sm border border-[#E2E7FF]/60">
            <form action="{{ route('profile.update') }}" method="POST" class="space-y-5">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-sm font-bold text-[#1A1743] mb-2">Nama Lengkap Penerima</label>
                    <input type="text" name="name" value="{{ old('name', $customer->name) }}" required
                           class="w-full px-4 py-3 rounded-xl bg-[#FAF8FF] border-2 border-[#E2E7FF] focus:outline-none focus:border-[#BFF138] focus:bg-white transition-all text-sm">
                    @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#1A1743] mb-2">Nomor WhatsApp / HP</label>
                    <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" required placeholder="08xxxxxxxxxx"
                           class="w-full px-4 py-3 rounded-xl bg-[#FAF8FF] border-2 border-[#E2E7FF] focus:outline-none focus:border-[#BFF138] focus:bg-white transition-all text-sm">
                    @error('phone') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#1A1743] mb-2">Alamat Lengkap (Jalan, No. Rumah, Patokan)</label>
                    <textarea name="address" rows="3" required placeholder="Contoh: Jl. Soekarno Hatta No. 45, Depan Ruko Niaga"
                              class="w-full px-4 py-3 rounded-xl bg-[#FAF8FF] border-2 border-[#E2E7FF] focus:outline-none focus:border-[#BFF138] focus:bg-white transition-all text-sm resize-none">{{ old('address', $customer->address) }}</textarea>
                    @error('address') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#1A1743] mb-2">Kota / Kecamatan</label>
                    <input type="text" name="city" value="{{ old('city', $customer->city) }}" required placeholder="Contoh: Lowokwaru, Kota Malang"
                           class="w-full px-4 py-3 rounded-xl bg-[#FAF8FF] border-2 border-[#E2E7FF] focus:outline-none focus:border-[#BFF138] focus:bg-white transition-all text-sm">
                    @error('city') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="px-6 py-3 bg-[#1A1743] hover:bg-[#2F2D59] text-white font-bold rounded-xl transition-all shadow-md flex items-center gap-2">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>