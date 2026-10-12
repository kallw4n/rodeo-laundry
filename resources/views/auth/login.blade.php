<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Rodeo Laundry</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
</head>
<body class="bg-[#FAF8FF] font-sans antialiased">

    <div class="h-screen flex overflow-hidden">

        <!-- ================= KIRI: GAMBAR (Fixed, Desktop only) ================= -->
        <div class="hidden lg:block lg:w-1/2 p-8 bg-[#1A1743] shrink-0">
            <div class="relative w-full h-full rounded-[40px] overflow-hidden">
                <img src="https://images.unsplash.com/photo-1545173168-9f1947eebb7f?q=80&w=2071&auto=format&fit=crop"
                     alt="Laundry" 
                     class="absolute inset-0 w-full h-full object-cover">
            </div>
        </div>

        <!-- ================= KANAN: FORM LOGIN (Scrollable) ================= -->
        <div class="w-full lg:w-1/2 overflow-y-auto bg-[#FAF8FF]">
            <div class="min-h-full flex items-center justify-center p-6 md:p-12">
                <div class="w-full max-w-md">

                    <!-- Logo -->
                    <div class="flex items-center gap-2 mb-8">
                        <div class="w-10 h-10 bg-[#1A1743] rounded-xl flex items-center justify-center">
                            <div class="w-4 h-5 bg-[#BFF138]"></div>
                        </div>
                        <div class="flex items-center text-xl font-semibold text-[#1A1743]">
                            Rodeo Laundry<span class="text-[#A7D717]">.</span>
                        </div>
                    </div>

                    <!-- Judul -->
                    <h1 class="text-3xl md:text-4xl font-extrabold text-[#1A1743] mb-3">Masuk ke Akun</h1>
                    <p class="text-sm text-[#47464E] mb-8 leading-relaxed">
                        Masukkan email dan kata sandi Anda untuk mengakses portal manajemen Rodeo Laundry.
                    </p>

                    <!-- Error messages -->
                    @if($errors->any())
                        <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-2xl">
                            <p class="text-sm text-red-700 font-semibold mb-1">Ada masalah dengan data Anda:</p>
                            <ul class="text-xs text-red-600 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Status message -->
                    @if(session('status'))
                        <div class="mb-5 p-4 bg-green-50 border border-green-200 rounded-2xl">
                            <p class="text-sm text-green-700 font-semibold">{{ session('status') }}</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ showPassword: false }">
                        @csrf

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-bold text-[#1A1743] mb-2">Alamat Email</label>
                            <input id="email" 
                                   type="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus 
                                   autocomplete="username"
                                   placeholder="example@gmail.com"
                                   class="w-full px-5 py-4 rounded-2xl bg-[#F2F3FF] border-2 border-transparent 
                                          text-sm text-[#1A1743] placeholder:text-[#9895C8]
                                          focus:outline-none focus:border-[#BFF138] focus:bg-white
                                          transition-all">
                        </div>

                        <!-- Password -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="password" class="block text-sm font-bold text-[#1A1743]">Kata Sandi</label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" 
                                       class="text-xs font-bold text-[#1A1743] hover:text-[#A7D717] transition">
                                        Lupa kata sandi?
                                    </a>
                                @endif
                            </div>
                            <div class="relative">
                                <input id="password" 
                                       :type="showPassword ? 'text' : 'password'" 
                                       name="password" 
                                       required 
                                       autocomplete="current-password"
                                       placeholder="Kata sandi"
                                       class="w-full px-5 py-4 pr-12 rounded-2xl bg-[#F2F3FF] border-2 border-transparent 
                                              text-sm text-[#1A1743] placeholder:text-[#9895C8]
                                              focus:outline-none focus:border-[#BFF138] focus:bg-white
                                              transition-all">
                                <button type="button"
                                        @click="showPassword = !showPassword"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-[#47464E] hover:text-[#1A1743] transition">
                                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center">
                            <input id="remember_me" 
                                   type="checkbox" 
                                   name="remember"
                                   class="w-5 h-5 rounded border-2 border-[#E2E7FF] text-[#1A1743] 
                                          focus:ring-0 focus:ring-offset-0 cursor-pointer
                                          checked:bg-[#1A1743] checked:border-[#1A1743]">
                            <label for="remember_me" class="ml-3 text-sm text-[#47464E] cursor-pointer select-none">
                                Ingat saya
                            </label>
                        </div>

                        <!-- Tombol Masuk -->
                        <button type="submit"
                                class="w-full px-6 py-4 bg-[#1A1743] text-white font-bold rounded-2xl
                                       flex items-center justify-center gap-2
                                       transition-all duration-300 ease-out
                                       hover:bg-[#2F2D59]
                                       hover:shadow-[0_15px_35px_-8px_rgba(26,23,67,0.5)]
                                       hover:-translate-y-0.5
                                       active:translate-y-0">
                            Masuk
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </button>
                    </form>

                    <!-- Register Link -->
                    <p class="text-center text-sm text-[#47464E] mt-10 mb-4">
                        Belum memiliki akun?
                        <a href="{{ route('register') }}" class="font-bold text-[#1A1743] underline hover:text-[#A7D717] transition">
                            Daftar
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>