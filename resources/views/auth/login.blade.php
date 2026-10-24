<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Rodeo Laundry</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- ini untuk popup nya -->
    <style> /* BUAT ANIMASI POP UP NYA */
        @keyframes fadeSlideDown {
            0% {
                opacity: 0; 
                transform: translateY(-30px); 
            }
            100% { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }
        @keyframes fadeSlideUp {
            0% { 
                opacity: 1; 
                transform: translateY(0); 
            }
            100% { 
                opacity: 0; 
                transform: translateY(-30px); 
            }
        }
        
        .pop-down {
            animation: fadeSlideDown 0.5s cubic-bezier(0.2, 1, 0.5, 1) forwards !important;
        }
        .pop-up {
            animation: fadeSlideUp 0.5s ease-in forwards !important;
        }
    </style>
</head>
<body class="bg-[#FAF8FF] font-sans antialiased">
    <div class="h-screen flex overflow-hidden">

        <!-- ================= GAMBAR UNUTK BAGIAN LOGIN DI KIRI ================= -->
        <div class="hidden lg:block lg:w-1/2 p-8 bg-[#1A1743] shrink-0">
            <div class="relative w-full h-full rounded-[40px] overflow-hidden">
                <img src="https://images.unsplash.com/photo-1545173168-9f1947eebb7f?q=80&w=2071&auto=format&fit=crop"
                     alt="Laundry" 
                     class="absolute inset-0 w-full h-full object-cover">
            </div>
        </div>

        <!-- ================= FORM LOGIN DI BAGIAN KANAN ================= -->
        <div class="w-full lg:w-1/2 overflow-y-auto bg-[#FAF8FF]">
            <div class="min-h-full flex items-center justify-center p-6 md:p-12">
                <div class="w-full max-w-md" x-data="{ showPassword: false }">

                    <!-- Logo -->
                    <div class="flex items-center gap-2 mb-8">
                        <div class="w-9 h-9 md:w-10 md:h-10 bg-[#2F2D59] rounded-xl flex items-center justify-center shadow-lg shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#bff108ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h3"/><path d="M17 6h.01"/><rect width="18" height="20" x="3" y="2" rx="2"/><circle cx="12" cy="13" r="5"/><path d="M12 18a2.5 2.5 0 0 0 0-5 2.5 2.5 0 0 1 0-5"/></svg>
                        </div>
                        <div class="flex items-center text-xl font-semibold text-[#1A1743]">
                            Rodeo Laundry<span class="text-[#A7D717]">.</span>
                        </div>
                    </div>

                    <h1 class="text-3xl md:text-4xl font-extrabold text-[#1A1743] mb-3">Masuk ke Akun</h1>

                    <!-- text kala error login -->
                    @if($errors->any())
                        <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-2xl">
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

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
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
                                   placeholder="nama@rodeolaundry.id"
                                   class="w-full px-5 py-4 rounded-2xl bg-[#F2F3FF] border-2 border-transparent 
                                          text-sm text-[#1A1743] placeholder:text-[#9895C8]
                                          focus:outline-none focus:border-[#BFF138] focus:bg-white
                                          transition-all">
                        </div>

                        <!-- Kata sandi -->
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
                                       placeholder="Kata Sandi"
                                       class="w-full px-5 py-4 pr-12 rounded-2xl bg-[#F2F3FF] border-2 border-transparent 
                                              text-sm text-[#1A1743] placeholder:text-[#9895C8]
                                              focus:outline-none focus:border-[#BFF138] focus:bg-white
                                              transition-all">
                                <button type="button"
                                        @click="showPassword = !showPassword"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-[#47464E] hover:text-[#1A1743] transition">
                                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
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

@if(session()->has('url.intended') && !request()->has('ref') && !$errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
                showClass: {
                    popup: 'pop-down' 
                },
                hideClass: {
                    popup: 'pop-up'
                },
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: 'warning',
                title: 'Silakan login terlebih dahulu ya!',
                customClass: {
                    popup: 'rounded-xl shadow-lg border border-[#E2E7FF]',
                    title: 'text-sm font-bold text-[#1A1743]'
                }
            });
        });
    </script>
@endif
</body>
</html>