<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Akun - Rodeo Laundry Malang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F2EFF8] min-h-screen flex items-center justify-center p-4 md:p-8 antialiased">

    <!-- Card Wrapper -->
    <div class="w-full max-w-5xl bg-white rounded-[32px] shadow-2xl overflow-hidden flex flex-col md:flex-row border border-[#E7E3F1]">

        <!-- ================= KIRI: GAMBAR & TESTIMONIAL ================= -->
        <div class="relative w-full md:w-1/2 min-h-[380px] md:min-h-[640px] bg-[#1E1B4B] p-6 md:p-8 flex flex-col justify-end overflow-hidden">
            <!-- Background Image -->
            <img src="https://images.unsplash.com/photo-1545173168-9f1947eebb7f?q=80&w=2071&auto=format&fit=crop"
                 alt="Laundry Interior"
                 class="absolute inset-0 w-full h-full object-cover">
            
            <!-- Dark Gradient Overlay for Readability -->
            <div class="absolute inset-0 bg-gradient-to-t from-[#1E1B4B]/80 via-transparent to-black/20"></div>

            <!-- Floating Testimonial Card -->
            <div class="relative z-10 bg-white/95 backdrop-blur-md p-6 rounded-2xl shadow-xl border border-white/60">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-1.5">
                        <div class="flex text-amber-400 text-sm">
                            ★ ★ ★ ★ ★
                        </div>
                        <span class="text-xs font-bold text-[#1E1B4B]">4.9/5.0</span>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-[#EBF0FF] text-[#1E1B4B] text-[11px] font-bold">
                        18.4k+ Pesanan
                    </span>
                </div>
                <p class="text-xs md:text-sm text-[#47464E] leading-relaxed italic">
                    “Layanan cuci & garment care premium terpercaya di Malang. Menjaga kelembutan pakaian dan keharuman tahan lama sejak 2021.”
                </p>
            </div>
        </div>

        <!-- ================= KANAN: FORM LOGIN ================= -->
        <div class="w-full md:w-1/2 p-8 md:p-12 lg:p-14 flex flex-col justify-center bg-white"
             x-data="{ showPassword: false }">

            <!-- Logo -->
            <div class="flex items-center gap-2.5 mb-8">
                <div class="w-9 h-9 rounded-xl bg-[#1E1B4B] flex items-center justify-center shadow-md">
                    <svg class="w-4 h-4 text-[#BFF108]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9"/>
                        <circle cx="12" cy="12" r="3" fill="#BFF108"/>
                        <path d="M12 3v3m0 12v3M3 12h3m12 0h3"/>
                    </svg>
                </div>
                <div class="text-lg font-extrabold text-[#1E1B4B] tracking-tight">
                    Rodeo<span class="text-[#BFF108]">.</span><span class="text-[#64748B] font-semibold text-sm">Laundry</span>
                </div>
            </div>

            <!-- Header Titles -->
            <h1 class="text-2xl md:text-3xl font-extrabold text-[#1E1B4B] tracking-tight mb-2">
                Masuk ke Akun
            </h1>
            <p class="text-xs md:text-sm text-[#64748B] mb-8 leading-relaxed">
                Masukkan email dan kata sandi Anda untuk mengakses portal manajemen Rodeo Laundry.
            </p>

            <!-- Error Alerts -->
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200">
                    <ul class="text-xs text-rose-600 list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('status'))
                <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-bold text-[#1E1B4B] uppercase tracking-wider mb-2">
                        Alamat Email
                    </label>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           placeholder="nama@rodeolaundry.id" 
                           required 
                           autofocus
                           class="w-full px-4 py-3 bg-[#F4F5FA] border border-[#E5E7EB] rounded-2xl text-sm text-[#1E1B4B] placeholder-[#9CA3AF] focus:outline-none focus:bg-white focus:border-[#1E1B4B] focus:ring-2 focus:ring-[#1E1B4B]/10 transition">
                </div>

                <!-- Password Input -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-xs font-bold text-[#1E1B4B] uppercase tracking-wider">
                            Kata Sandi
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs font-semibold text-[#4338CA] hover:underline">
                                Lupa kata sandi?
                            </a>
                        @endif
                    </div>
                    <div class="relative">
                        <input id="password" 
                               :type="showPassword ? 'text' : 'password'" 
                               name="password" 
                               placeholder="••••••••••••" 
                               required 
                               class="w-full pl-4 pr-11 py-3 bg-[#F4F5FA] border border-[#E5E7EB] rounded-2xl text-sm text-[#1E1B4B] placeholder-[#9CA3AF] focus:outline-none focus:bg-white focus:border-[#1E1B4B] focus:ring-2 focus:ring-[#1E1B4B]/10 transition">
                        <button type="button" 
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#9CA3AF] hover:text-[#1E1B4B]">
                            <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-[#1E1B4B] focus:ring-[#1E1B4B] border-gray-300">
                        <span class="text-xs text-[#64748B]">Ingat sesi saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full py-3.5 px-6 rounded-2xl bg-[#1E1B4B] hover:bg-[#2A2566] text-white text-sm font-bold flex items-center justify-center gap-2 shadow-lg shadow-[#1E1B4B]/20 transition-all duration-200">
                    <span>Masuk</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-7">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-[#E5E7EB]"></div>
                </div>
                <div class="relative flex justify-center text-[10px] font-bold uppercase tracking-wider text-[#94A3B8]">
                    <span class="bg-white px-3">Atau Masuk Dengan</span>
                </div>
            </div>

            <!-- Google Login Mock -->
            <button type="button"
                    class="w-full py-3 px-4 bg-[#F8F9FD] hover:bg-[#EEF1FA] border border-[#E5E7EB] rounded-2xl text-xs font-bold text-[#1E1B4B] flex items-center justify-center gap-2.5 transition">
                <svg class="w-4 h-4" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span>Akun Google</span>
            </button>

            <!-- Register Link -->
            <div class="mt-8 text-center text-xs text-[#64748B]">
                Belum memiliki akun? 
                <a href="{{ route('register') }}" class="font-bold text-[#1E1B4B] hover:underline">
                    Daftar
                </a>
            </div>
        </div>
    </div>
</body>
</html>