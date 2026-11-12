<x-admin-layout title="Dashboard">
<div x-data="reportModalManager()" class="relative">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl md:text-4xl font-extrabold text-[#1E1B4B] tracking-tight">
                Selamat Pagi, Admin
            </h1>
            <p class="text-sm text-[#64748B] mt-1">
                Ringkasan operasional dan performa hari ini.
            </p>
        </div>

        <div>
            <button type="button"
                    @click="showModal = true"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-slate-50 text-[#1E1B4B] border border-slate-300/80 rounded-full text-xs md:text-sm font-bold shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 text-[#4338CA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>Unduh Laporan</span>
            </button>
        </div>
    </div>

    <!-- 4 Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        
        <!-- Card 1: Total Pesanan -->
        <div class="group bg-white hover:bg-[#1E1B4B] rounded-3xl p-6 border border-slate-300/80 hover:border-[#1E1B4B] shadow-[0_10px_30px_-5px_rgba(26,23,67,0.06),0_4px_12px_rgba(0,0,0,0.03)] hover:shadow-xl hover:shadow-[#1E1B4B]/25 flex flex-col justify-between relative overflow-hidden transition-all duration-300 hover:-translate-y-1 cursor-pointer">
            <div class="absolute -right-6 -bottom-6 w-32 h-32 rounded-full bg-white/0 group-hover:bg-white/5 pointer-events-none transition-all duration-300"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 group-hover:text-slate-300 transition-colors duration-300">Total Pesanan</span>
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-[#4338CA] group-hover:bg-white/10 group-hover:text-[#BFF108] flex items-center justify-center transition-colors duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"/>
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M12 3v3m0 12v3"/>
                    </svg>
                </div>
            </div>
            <div class="relative z-10">
                <div class="text-4xl font-extrabold text-[#1E1B4B] group-hover:text-white tracking-tight mb-3 transition-colors duration-300">
                    {{ $totalOrders }}
                </div>
                <div>
                    <span class="inline-block px-3 py-1 rounded-full bg-slate-100 text-[#1E1B4B] group-hover:bg-[#BFF108] group-hover:text-[#1E1B4B] text-xs font-bold transition-colors duration-300 shadow-xs">
                        {{ ($todayOrdersCount ?? 0) > 0 ? "+{$todayOrdersCount} Hari Ini" : 'Semua Status' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Card 2: Pendapatan Hari Ini -->
        <div class="group bg-white hover:bg-[#1E1B4B] rounded-3xl p-6 border border-slate-300/80 hover:border-[#1E1B4B] shadow-[0_10px_30px_-5px_rgba(26,23,67,0.06),0_4px_12px_rgba(0,0,0,0.03)] hover:shadow-xl hover:shadow-[#1E1B4B]/25 flex flex-col justify-between relative overflow-hidden transition-all duration-300 hover:-translate-y-1 cursor-pointer">
            <div class="absolute -right-6 -bottom-6 w-32 h-32 rounded-full bg-white/0 group-hover:bg-white/5 pointer-events-none transition-all duration-300"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 group-hover:text-slate-300 transition-colors duration-300">
                    {{ isset($isDateFiltered) && $isDateFiltered ? 'Pendapatan ' . \Carbon\Carbon::parse($selectedDate)->translatedFormat('d M Y') : 'Pendapatan Hari Ini' }}
                </span>
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-[#4338CA] group-hover:bg-white/10 group-hover:text-[#BFF108] flex items-center justify-center transition-colors duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
            <div class="relative z-10">
                <div class="text-3xl lg:text-4xl font-extrabold text-[#1E1B4B] group-hover:text-white tracking-tight mb-3 flex items-baseline gap-1 transition-colors duration-300">
                    <span class="text-indigo-600 group-hover:text-[#BFF108] text-xl font-bold transition-colors duration-300">Rp</span>
                    @if($todayRevenue >= 1000000)
                        <span>{{ number_format($todayRevenue / 1000000, 2, ',', '.') }}</span>
                        <span class="text-indigo-600 group-hover:text-[#BFF108] text-xl font-bold transition-colors duration-300">jt</span>
                    @elseif($todayRevenue > 0)
                        <span>{{ number_format($todayRevenue, 0, ',', '.') }}</span>
                    @else
                        <span>0</span>
                    @endif
                </div>
                <div>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-slate-100 text-[#1E1B4B] group-hover:bg-[#BFF108] group-hover:text-[#1E1B4B] text-xs font-bold transition-colors duration-300 shadow-xs">
                        @if($todayRevenue > 0)
                            Rp {{ number_format($todayRevenue, 0, ',', '.') }}
                        @else
                            {{ isset($isDateFiltered) && $isDateFiltered ? 'Tidak ada pendapatan' : 'Belum ada pendapatan hari ini' }}
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Card 3: Order Dalam Proses -->
        <div class="group bg-white hover:bg-[#1E1B4B] rounded-3xl p-6 border border-slate-300/80 hover:border-[#1E1B4B] shadow-[0_10px_30px_-5px_rgba(26,23,67,0.06),0_4px_12px_rgba(0,0,0,0.03)] hover:shadow-xl hover:shadow-[#1E1B4B]/25 flex flex-col justify-between relative overflow-hidden transition-all duration-300 hover:-translate-y-1 cursor-pointer">
            <div class="absolute -right-6 -bottom-6 w-32 h-32 rounded-full bg-white/0 group-hover:bg-white/5 pointer-events-none transition-all duration-300"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 group-hover:text-slate-300 transition-colors duration-300">Order Dalam Proses</span>
                <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 group-hover:bg-white/10 group-hover:text-[#BFF108] flex items-center justify-center transition-colors duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
            <div class="relative z-10">
                <div class="text-4xl font-extrabold text-[#1E1B4B] group-hover:text-white tracking-tight mb-3 transition-colors duration-300">
                    {{ $inProcessOrders ?? $activeCustomers }}
                </div>
                <div>
                    <span class="inline-block px-3 py-1 rounded-full bg-slate-100 text-[#1E1B4B] group-hover:bg-[#BFF108] group-hover:text-[#1E1B4B] text-xs font-bold transition-colors duration-300 shadow-xs">
                        {{ $inProcessOrders ?? $activeCustomers }} Aktif
                    </span>
                </div>
            </div>
        </div>

        <!-- Card 4: Order Selesai -->
        <div class="group bg-white hover:bg-[#1E1B4B] rounded-3xl p-6 border border-slate-300/80 hover:border-[#1E1B4B] shadow-[0_10px_30px_-5px_rgba(26,23,67,0.06),0_4px_12px_rgba(0,0,0,0.03)] hover:shadow-xl hover:shadow-[#1E1B4B]/25 flex flex-col justify-between relative overflow-hidden transition-all duration-300 hover:-translate-y-1 cursor-pointer">
            <div class="absolute -right-6 -bottom-6 w-32 h-32 rounded-full bg-white/0 group-hover:bg-white/5 pointer-events-none transition-all duration-300"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 group-hover:text-slate-300 transition-colors duration-300">Order Selesai</span>
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 group-hover:bg-white/10 group-hover:text-[#BFF108] flex items-center justify-center transition-colors duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="relative z-10">
                <div class="text-4xl font-extrabold text-[#1E1B4B] group-hover:text-white tracking-tight mb-3 transition-colors duration-300">
                    {{ $completedOrders ?? $activeMachineQueue }}
                </div>
                <div>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 group-hover:bg-[#BFF108] group-hover:text-[#1E1B4B] text-xs font-bold transition-colors duration-300 shadow-xs">
                        {{ $completedOrders ?? $activeMachineQueue }} Selesai
                    </span>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 px-5 py-3 bg-emerald-50 border border-emerald-300 rounded-2xl text-emerald-800 text-sm font-semibold flex items-center gap-2">
        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <!-- Main Section: Pesanan Terbaru Table -->
    <div class="bg-white rounded-[32px] p-6 md:p-8 border border-slate-300/80 shadow-[0_10px_30px_-5px_rgba(26,23,67,0.06),0_4px_12px_rgba(0,0,0,0.03)]">
        
        <!-- Table Top Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-extrabold text-[#1E1B4B] tracking-tight">
                    Pesanan Terbaru
                </h2>
                <p class="text-xs text-[#64748B] mt-0.5">
                    Daftar transaksi dan status pengerjaan langsung
                </p>
            </div>

            <!-- Filter & Export Controls -->
            <div class="flex items-center gap-2.5">
                <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center">
                    @if(request('date'))
                        <input type="hidden" name="date" value="{{ request('date') }}">
                    @endif
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <select name="status" 
                            onchange="this.form.submit()" 
                            class="px-4 py-2 bg-slate-50 border border-slate-300/80 rounded-full text-xs font-bold text-slate-800 focus:outline-none focus:bg-white focus:border-[#1E1B4B] cursor-pointer shadow-xs">
                        <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Antrian Masuk</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Sedang Dicuci</option>
                        <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Siap Ambil</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </form>

                <button type="button"
                        @click="showModal = true"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-50 hover:bg-slate-100 border border-slate-300/80 rounded-full text-xs font-bold text-slate-800 shadow-xs transition cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Export Rekap</span>
                </button>
            </div>
        </div>

        @if(isset($isDateFiltered) && $isDateFiltered)
        <div class="mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-indigo-50/80 border border-indigo-200/90 px-4 md:px-5 py-3 rounded-2xl text-xs text-[#1E1B4B]">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-full bg-indigo-100 text-[#4338CA] flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-slate-600">Menampilkan pesanan yang dilakukan pada:</span>
                    <strong class="font-bold text-[#1E1B4B] ml-1">{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}</strong>
                    <span class="text-slate-500 ml-1">({{ $recentOrders->total() }} pesanan ditemukan)</span>
                </div>
            </div>
            <a href="{{ route('admin.dashboard', request()->except('date')) }}" 
               class="inline-flex items-center gap-1.5 font-bold text-rose-600 hover:text-rose-700 bg-white hover:bg-rose-50 px-3.5 py-1.5 rounded-full border border-rose-200 transition shadow-2xs self-start sm:self-auto cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span>Hapus Filter Tanggal</span>
            </a>
        </div>
        @endif

        <!-- Table Container -->
        <div class="overflow-x-auto md:overflow-visible">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-[11px] font-extrabold uppercase tracking-wider text-slate-600 bg-slate-100/90 border-y border-slate-200/80">
                        <th class="py-3.5 px-4 rounded-l-2xl">ID & WAKTU</th>
                        <th class="py-3.5 px-4">PELANGGAN</th>
                        <th class="py-3.5 px-4">LAYANAN</th>
                        <th class="py-3.5 px-4">TOTAL BAYAR</th>
                        <th class="py-3.5 px-4">STATUS</th>
                        <th class="py-3.5 px-4 text-center rounded-r-2xl">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/80">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-indigo-50/40 transition">
                            <!-- ID & Waktu -->
                            <td class="py-4 px-4">
                                <div class="font-extrabold text-[#1E1B4B]">
                                    #RD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                                </div>
                                <div class="text-xs text-[#94A3B8]">
                                    {{ $order->created_at->format('H:i') }} WIB
                                </div>
                            </td>

                            <!-- Pelanggan -->
                            <td class="py-4 px-4">
                                <div class="font-bold text-[#1E1B4B]">
                                    {{ $order->customer_display_name }}
                                </div>
                                <div class="text-xs text-[#64748B]">
                                    {{ $order->customer_display_phone }}
                                </div>
                            </td>

                            <!-- Layanan -->
                            <td class="py-4 px-4">
                                @if($order->items->isNotEmpty())
                                    <div class="font-semibold text-[#1E1B4B]">
                                        {{ $order->items->first()->service->name ?? 'Layanan Laundry' }}
                                        @if($order->items->count() > 1)
                                            <span class="text-xs text-[#64748B] font-normal">+{{ $order->items->count() - 1 }} lainnya</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-[#94A3B8]">
                                        {{ $order->items->first()->quantity }} item/kg
                                    </div>
                                @else
                                    <span class="text-xs text-[#94A3B8]">-</span>
                                @endif
                            </td>

                            <!-- Total Bayar -->
                            <td class="py-4 px-4 font-bold text-[#1E1B4B]">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </td>

                            <!-- Status Badge -->
                            <td class="py-4 px-4">
                                @php
                                    $statusMap = [
                                        'pending'    => ['label' => 'Antrian Masuk', 'bg' => 'bg-[#F1F3FA]', 'text' => 'text-[#47464E]', 'dot' => 'bg-[#64748B]'],
                                        'processing' => ['label' => 'Sedang Dicuci', 'bg' => 'bg-[#EEF2FF]', 'text' => 'text-[#4338CA]', 'dot' => 'bg-[#4F46E5]'],
                                        'ready'      => ['label' => 'Siap Ambil', 'bg' => 'bg-[#D9F99D]/60', 'text' => 'text-[#365314]', 'dot' => 'bg-[#65A30D]'],
                                        'completed'  => ['label' => 'Selesai', 'bg' => 'bg-[#ECFDF5]', 'text' => 'text-[#065F46]', 'dot' => 'bg-[#10B981]'],
                                        'cancelled'  => ['label' => 'Dibatalkan', 'bg' => 'bg-[#FEF2F2]', 'text' => 'text-[#991B1B]', 'dot' => 'bg-[#EF4444]'],
                                    ];
                                    $st = $statusMap[$order->status] ?? $statusMap['pending'];
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $st['bg'] }} {{ $st['text'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $st['dot'] }}"></span>
                                    {{ $st['label'] }}
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="py-4 px-4 text-center">
                                <div class="inline-flex items-center gap-1" x-data="{ open: false }">
                                    <div class="relative">
                                        <button @click="open = !open" class="p-1.5 rounded-lg text-[#94A3B8] hover:text-[#1E1B4B] hover:bg-[#F1F3FA] transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                            </svg>
                                        </button>
                                        
                                        <!-- Dropdown Menu -->
                                        <div x-show="open" 
                                             @click.outside="open = false"
                                             class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-[#E9ECF4] py-2 z-30 text-left text-xs font-semibold">
                                            <!-- Update Status -->
                                            <div class="px-4 py-2 text-[10px] uppercase tracking-wider text-[#94A3B8] font-bold">Ubah Status</div>
                                            <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}">
                                                @csrf @method('PATCH')
                                                <div class="px-4 pb-2 flex gap-1.5">
                                                    @php
                                                        $readyLabel = ($order->delivery && $order->delivery->type === 'delivery') ? 'Diantar Kurir' : 'Siap Ambil';
                                                    @endphp
                                                    <select name="status" class="flex-1 text-xs border border-[#E9ECF4] rounded-xl px-2 py-1.5 text-[#1E1B4B] bg-[#F8F9FD] focus:outline-none focus:border-[#4338CA]">
                                                        @foreach(['pending' => 'Antrian', 'processing' => 'Dicuci', 'ready' => $readyLabel, 'completed' => 'Selesai', 'cancelled' => 'Batal'] as $val => $lbl)
                                                            <option value="{{ $val }}" {{ $order->status === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                                        @endforeach
                                                    </select>
                                                    <button type="submit" class="px-2.5 py-1.5 bg-[#1E1B4B] text-white rounded-xl hover:bg-[#4338CA] transition text-xs font-bold">✓</button>
                                                </div>
                                            </form>
                                            <div class="border-t border-[#F1F3F8] my-1"></div>
                                            <a href="javascript:void(0)" 
                                               onclick="window.open('{{ route('admin.orders.receipt', $order) }}', '_blank', 'width=450,height=650')" 
                                               class="px-4 py-2 hover:bg-[#F8F9FD] text-[#1E1B4B] flex items-center gap-2">
                                                <svg class="w-3.5 h-3.5 text-[#1E1B4B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                                </svg>
                                                <span>Cetak Struk</span>
                                            </a>
                                            <a href="{{ route('admin.payments.index') }}" class="block px-4 py-2 hover:bg-[#F8F9FD] text-[#1E1B4B]">Cek Pembayaran</a>
                                            <a href="{{ route('admin.deliveries.index') }}" class="block px-4 py-2 hover:bg-[#F8F9FD] text-[#1E1B4B]">Jadwal Pengiriman</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-14 text-center text-sm text-[#94A3B8]">
                                @if(isset($isDateFiltered) && $isDateFiltered)
                                    <div class="flex flex-col items-center justify-center gap-2.5 max-w-sm mx-auto">
                                        <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-700 text-sm">Tidak ada pesanan pada tanggal ini</p>
                                            <p class="text-xs text-slate-500 mt-0.5">Tidak ditemukan transaksi yang dibuat pada tanggal {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}.</p>
                                        </div>
                                        <a href="{{ route('admin.dashboard', request()->except('date')) }}" 
                                           class="mt-2 inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-[#4338CA] rounded-full text-xs font-bold transition">
                                            <span>Tampilkan Semua Pesanan</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </div>
                                @else
                                    Belum ada pesanan aktif saat ini.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer: Pagination & Counter -->
        <div class="mt-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs text-[#64748B]">
            <div>
                Menampilkan {{ $recentOrders->count() }} dari {{ $recentOrders->total() }} pesanan aktif
            </div>
            <div>
                {{ $recentOrders->links() }}
            </div>
        </div>
    </div>

    <!-- ================= MODAL UNDUH & EXPORT LAPORAN ================= -->
    <div x-show="showModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
         style="display: none;">
        
        <div @click.away="showModal = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 md:p-8 space-y-5 text-left">
            
            <!-- Header Modal -->
            <div class="flex items-start justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-[#1E1B4B]">
                        <svg class="w-5 h-5 text-[#4338CA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-extrabold text-[#1E1B4B]">Unduh Laporan & Rekap</h3>
                        <p class="text-xs text-slate-500">Pilih periode dan format unduhan laporan transaksi.</p>
                    </div>
                </div>
                <button type="button" @click="showModal = false" class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-full transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Periode Selection Buttons Grid -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-[#1E1B4B] uppercase tracking-wider">1. Pilih Periode Waktu</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    <button type="button"
                            @click="period = 'today'"
                            :class="period === 'today' ? 'bg-[#1E1B4B] text-white border-[#1E1B4B]' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                            class="px-3 py-2 rounded-xl border text-xs font-bold transition text-center flex items-center justify-center gap-1.5">
                        <span>⚡ Hari Ini</span>
                    </button>

                    <button type="button"
                            @click="period = 'date'"
                            :class="period === 'date' ? 'bg-[#1E1B4B] text-white border-[#1E1B4B]' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                            class="px-3 py-2 rounded-xl border text-xs font-bold transition text-center flex items-center justify-center gap-1.5">
                        <span>📅 Per Tanggal</span>
                    </button>

                    <button type="button"
                            @click="period = 'week'"
                            :class="period === 'week' ? 'bg-[#1E1B4B] text-white border-[#1E1B4B]' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                            class="px-3 py-2 rounded-xl border text-xs font-bold transition text-center flex items-center justify-center gap-1.5">
                        <span>🗓️ 1 Minggu</span>
                    </button>

                    <button type="button"
                            @click="period = 'month'"
                            :class="period === 'month' ? 'bg-[#1E1B4B] text-white border-[#1E1B4B]' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                            class="px-3 py-2 rounded-xl border text-xs font-bold transition text-center flex items-center justify-center gap-1.5">
                        <span>📆 30 Hari</span>
                    </button>

                    <button type="button"
                            @click="period = 'this_month'"
                            :class="period === 'this_month' ? 'bg-[#1E1B4B] text-white border-[#1E1B4B]' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                            class="px-3 py-2 rounded-xl border text-xs font-bold transition text-center flex items-center justify-center gap-1.5">
                        <span>📊 Bulan Ini</span>
                    </button>

                    <button type="button"
                            @click="period = 'custom'"
                            :class="period === 'custom' ? 'bg-[#1E1B4B] text-white border-[#1E1B4B]' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                            class="px-3 py-2 rounded-xl border text-xs font-bold transition text-center flex items-center justify-center gap-1.5">
                        <span>🎯 Kustom</span>
                    </button>

                    <button type="button"
                            @click="period = 'all'"
                            :class="period === 'all' ? 'bg-[#1E1B4B] text-white border-[#1E1B4B]' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                            class="px-3 py-2 rounded-xl border text-xs font-bold transition text-center flex items-center justify-center gap-1.5 col-span-2 sm:col-span-3">
                        <span>🌐 Semua Waktu (Rekap Total)</span>
                    </button>
                </div>
            </div>

            <!-- Dynamic Date Picker Inputs -->
            <div x-show="period === 'date'" class="p-3.5 bg-indigo-50/70 border border-indigo-200/80 rounded-2xl space-y-1.5">
                <label class="text-xs font-bold text-[#1E1B4B]">Pilih Tanggal Transaksi:</label>
                <input type="date"
                       x-model="date"
                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-[#1E1B4B]">
            </div>

            <div x-show="period === 'custom'" class="p-3.5 bg-indigo-50/70 border border-indigo-200/80 rounded-2xl space-y-2">
                <label class="text-xs font-bold text-[#1E1B4B]">Rentang Tanggal:</label>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <span class="text-[11px] text-slate-500 font-semibold block mb-1">Dari Tanggal:</span>
                        <input type="date"
                               x-model="startDate"
                               class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-[#1E1B4B]">
                    </div>
                    <div>
                        <span class="text-[11px] text-slate-500 font-semibold block mb-1">Sampai Tanggal:</span>
                        <input type="date"
                               x-model="endDate"
                               class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-[#1E1B4B]">
                    </div>
                </div>
            </div>

            <!-- Status Filter -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-[#1E1B4B] uppercase tracking-wider">2. Filter Status Pesanan</label>
                <select x-model="status"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300/80 rounded-2xl text-xs font-bold text-slate-800 focus:outline-none focus:bg-white focus:border-[#1E1B4B]">
                    <option value="all">Semua Status Pesanan</option>
                    <option value="completed">Selesai</option>
                    <option value="ready">Siap Diambil</option>
                    <option value="processing">Sedang Dicuci</option>
                    <option value="pending">Antrian Masuk</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
            </div>

            <!-- Informational Note -->
            <div class="flex items-start gap-2.5 p-3 bg-slate-50 border border-slate-200/80 rounded-2xl text-[11px] text-slate-600">
                <svg class="w-4 h-4 text-indigo-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Dokumen laporan diekspor secara utuh sesuai filter waktu (tidak terpotong paginasi 10 item) dan bebas dari elemen menu samping / tombol navigasi.</span>
            </div>

            <!-- Action Buttons Footer -->
            <div class="pt-2 border-t border-slate-100 flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-2.5">
                <button type="button"
                        @click="showModal = false"
                        class="px-4 py-2.5 rounded-2xl text-xs font-bold text-slate-500 hover:bg-slate-100 transition text-center">
                    Batal
                </button>

                <button type="button"
                        @click="openExport()"
                        class="px-4 py-2.5 rounded-2xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-800 text-xs font-bold shadow-xs transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Download Excel (CSV)</span>
                </button>

                <button type="button"
                        @click="openPrint()"
                        class="px-5 py-2.5 rounded-2xl bg-[#1E1B4B] hover:bg-[#2A2663] text-white text-xs font-bold shadow-md transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-[#BFF108]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak / Simpan PDF</span>
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    function reportModalManager() {
        return {
            showModal: false,
            period: '{{ request('date') ? 'date' : 'today' }}',
            date: '{{ request('date', date('Y-m-d')) }}',
            startDate: '{{ date('Y-m-d', strtotime('-7 days')) }}',
            endDate: '{{ date('Y-m-d') }}',
            status: '{{ request('status', 'all') }}',
            search: '{{ request('search', '') }}',

            get printUrl() {
                const params = new URLSearchParams({
                    period: this.period,
                    date: this.date,
                    start_date: this.startDate,
                    end_date: this.endDate,
                    status: this.status,
                    search: this.search
                });
                return '{{ route('admin.reports.print') }}?' + params.toString();
            },

            get exportUrl() {
                const params = new URLSearchParams({
                    period: this.period,
                    date: this.date,
                    start_date: this.startDate,
                    end_date: this.endDate,
                    status: this.status,
                    search: this.search
                });
                return '{{ route('admin.reports.export') }}?' + params.toString();
            },

            openPrint() {
                window.open(this.printUrl, '_blank');
                this.showModal = false;
            },

            openExport() {
                window.location.href = this.exportUrl;
                this.showModal = false;
            }
        };
    }
</script>
</x-admin-layout>
