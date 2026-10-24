<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Rodeo Laundry</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#FAF8FF] font-sans antialiased text-[#1A1743] min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-10">

    <!-- ================= CENTERED CARD CONTAINER ================= -->
    <div class="w-full max-w-5xl bg-white rounded-[32px] md:rounded-[40px] shadow-2xl border border-[#E2E7FF]/70 overflow-hidden grid grid-cols-1 lg:grid-cols-2">

        <!-- ================= KIRI: GAMBAR & BENEFIT CARD ================= -->
        <div class="relative min-h-[380px] lg:min-h-[660px] bg-[#1A1743] overflow-hidden flex flex-col justify-end p-6 md:p-8 order-2 lg:order-1">
            <img src="{{ asset('images/hero-laundry.jpg') }}"
                 alt="Rodeo Laundry Interior" 
                 class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-[#1A1743]/70 via-black/10 to-transparent"></div>

            <!-- Floating Benefit Card -->
            <div class="relative bg-white/95 backdrop-blur-md rounded-2xl md:rounded-[24px] p-5 md:p-6 shadow-xl border border-white/60">
                <div class="flex items-center justify-between gap-3 mb-2.5">
                    <div class="flex items-center gap-1.5 text-emerald-700 text-xs md:text-sm font-black">
                        <div class="flex text-emerald-600 text-xs gap-0.5">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <span class="text-[#1A1743] ml-1 font-extrabold text-xs">4.9/5.0</span>
                    </div>
                    <span class="px-2.5 py-1 bg-[#BFF108] text-[#131B2E] font-extrabold text-[11px] rounded-full">
                        Gratis Ongkir
                    </span>
                </div>
                <p class="text-xs md:text-sm text-[#3A3852] font-medium leading-relaxed">
                    “Daftar sekarang dan nikmati layanan cuci higienis 1 drum 1 pelanggan dengan penjemputan dan pengantaran express ke rumah Anda.”
                </p>
            </div>
        </div>

        <!-- ================= KANAN: FORM REGISTER ================= -->
        <div class="p-8 sm:p-10 md:p-12 lg:p-14 flex flex-col justify-center bg-white order-1 lg:order-2" 
             x-data="{ showPassword: false, showConfirm: false }">

            <!-- Brand Logo Centered -->
            <div class="flex items-center justify-center gap-2 mb-4">
                <div class="w-8 h-8 bg-[#2F2D59] rounded-xl flex items-center justify-center shadow-sm shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#bff108ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h3"/><path d="M17 6h.01"/><rect width="18" height="20" x="3" y="2" rx="2"/><circle cx="12" cy="13" r="5"/><path d="M12 18a2.5 2.5 0 0 0 0-5 2.5 2.5 0 0 1 0-5"/></svg>
                </div>
                <span class="text-lg font-extrabold tracking-tight text-[#1A1743]">Rodeo<span class="text-[#8FB300]">.</span>Laundry</span>
            </div>

            <!-- Header Titles -->
            <h1 class="text-2xl md:text-3xl font-black text-[#1A1743] text-center mb-2 tracking-tight">Buat Akun Baru</h1>
            <p class="text-xs text-[#717082] text-center max-w-xs mx-auto mb-5 leading-relaxed">
                Daftar sekarang untuk kemudahan pemesanan laundry higienis di Kota Malang.
            </p>

            <!-- Error Alerts -->
            @if($errors->any())
                <div class="mb-4 p-3.5 bg-red-50 border border-red-200 rounded-xl text-xs text-red-600">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-3.5">
                @csrf

                <!-- Username -->
                <div>
                    <label for="username" class="block text-xs font-bold text-[#1A1743] mb-1.5">Username</label>
                    <input id="username" 
                           type="text" 
                           name="username" 
                           value="{{ old('username') }}" 
                           required 
                           autofocus 
                           autocomplete="username"
                           placeholder="Username Anda"
                           class="w-full px-4 py-2.5 rounded-xl bg-[#F2F3FF] border border-transparent 
                                  text-sm text-[#1A1743] placeholder-[#9895C8]
                                  focus:outline-none focus:border-[#1A1743] focus:bg-white
                                  transition">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-bold text-[#1A1743] mb-1.5">Alamat Email</label>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autocomplete="email"
                           placeholder="nama@email.com"
                           class="w-full px-4 py-2.5 rounded-xl bg-[#F2F3FF] border border-transparent 
                                  text-sm text-[#1A1743] placeholder-[#9895C8]
                                  focus:outline-none focus:border-[#1A1743] focus:bg-white
                                  transition">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-bold text-[#1A1743] mb-1.5">Kata Sandi</label>
                    <div class="relative">
                        <input id="password" 
                               :type="showPassword ? 'text' : 'password'" 
                               name="password" 
                               required 
                               autocomplete="new-password"
                               placeholder="Minimal 8 karakter"
                               class="w-full px-4 py-2.5 pr-11 rounded-xl bg-[#F2F3FF] border border-transparent 
                                      text-sm text-[#1A1743] placeholder-[#9895C8]
                                      focus:outline-none focus:border-[#1A1743] focus:bg-white
                                      transition">
                        <button type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[#717082] hover:text-[#1A1743] transition">
                            <i :class="showPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'" class="text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Konfirmasi Password -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-[#1A1743] mb-1.5">Konfirmasi Kata Sandi</label>
                    <div class="relative">
                        <input id="password_confirmation" 
                               :type="showConfirm ? 'text' : 'password'" 
                               name="password_confirmation" 
                               required 
                               autocomplete="new-password"
                               placeholder="Ulangi kata sandi"
                               class="w-full px-4 py-2.5 pr-11 rounded-xl bg-[#F2F3FF] border border-transparent 
                                      text-sm text-[#1A1743] placeholder-[#9895C8]
                                      focus:outline-none focus:border-[#1A1743] focus:bg-white
                                      transition">
                        <button type="button"
                                @click="showConfirm = !showConfirm"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[#717082] hover:text-[#1A1743] transition">
                            <i :class="showConfirm ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'" class="text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full mt-2 py-3.5 bg-[#28264E] hover:bg-[#1E1C3C] text-white font-bold rounded-full
                               flex items-center justify-center gap-2 text-sm shadow-sm transition hover:shadow-md">
                    <span>Daftar Akun</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-4 text-center">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-[#E2E7FF]"></div></div>
                <span class="relative bg-white px-3 text-[10px] uppercase font-bold tracking-wider text-[#9895C8]">
                    ATAU DAFTAR DENGAN
                </span>
            </div>

            <!-- Google Button -->
            <button type="button"
                    class="w-full py-2.5 bg-[#F2F3FF] hover:bg-[#EAEDFF] text-[#1A1743] font-bold text-xs rounded-full flex items-center justify-center gap-2.5 transition">
                <svg class="w-4 h-4" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span>Akun Google</span>
            </button>

            <!-- Login Link -->
            <p class="text-center text-xs text-[#717082] mt-5">
                Sudah memiliki akun?
                <a href="{{ route('login') }}" class="font-extrabold text-[#1A1743] underline hover:text-[#8FB300] transition ml-0.5">
                    Masuk
                </a>
            </p>
        </div>
    </div>

</body>
</html>