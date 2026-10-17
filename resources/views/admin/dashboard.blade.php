<x-admin-layout title="Dashboard">
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
            <a href="javascript:window.print()"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#EEF2FF] hover:bg-[#E0E7FF] text-[#1E1B4B] rounded-full text-xs md:text-sm font-bold shadow-sm transition">
                <svg class="w-4 h-4 text-[#4338CA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>Unduh Laporan</span>
            </a>
        </div>
    </div>

    <!-- 4 Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        
        <!-- Card 1: Total Pesanan -->
        <div class="bg-white rounded-3xl p-6 border border-[#E9ECF4] shadow-sm flex flex-col justify-between hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-[#64748B]">Total Pesanan</span>
                <div class="w-10 h-10 rounded-2xl bg-[#EEF2FF] text-[#4338CA] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"/>
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M12 3v3m0 12v3"/>
                    </svg>
                </div>
            </div>
            <div>
                <div class="text-4xl font-extrabold text-[#1E1B4B] tracking-tight mb-3">
                    {{ $totalOrders }}
                </div>
                <div>
                    <span class="inline-block px-3 py-1 rounded-full bg-[#F1F3FA] text-[#1E1B4B] text-xs font-bold">
                        +14.2%
                    </span>
                </div>
            </div>
        </div>

        <!-- Card 2: Pendapatan Hari Ini (Hero Navy Card) -->
        <div class="bg-[#1E1B4B] rounded-3xl p-6 shadow-xl flex flex-col justify-between relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 w-32 h-32 rounded-full bg-white/5 pointer-events-none"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <span class="text-xs font-bold uppercase tracking-wider text-[#94A3B8]">Pendapatan Hari Ini</span>
                <div class="w-10 h-10 rounded-2xl bg-white/10 text-[#BFF108] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
            <div class="relative z-10">
                <div class="text-3xl lg:text-4xl font-extrabold text-white tracking-tight mb-3 flex items-baseline gap-1">
                    <span class="text-[#BFF108] text-xl font-bold">Rp</span>
                    <span>{{ number_format($todayRevenue >= 1000000 ? $todayRevenue / 1000000 : $todayRevenue / 1000, 2, ',', '.') }}</span>
                    <span class="text-[#BFF108] text-xl font-bold">{{ $todayRevenue >= 1000000 ? 'jt' : 'rb' }}</span>
                </div>
                <div>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-[#BFF108] text-[#1E1B4B] text-xs font-bold">
                        ↑ +8.5%
                    </span>
                </div>
            </div>
        </div>

        <!-- Card 3: Pelanggan Aktif -->
        <div class="bg-white rounded-3xl p-6 border border-[#E9ECF4] shadow-sm flex flex-col justify-between hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-[#64748B]">Pelanggan Aktif</span>
                <div class="w-10 h-10 rounded-2xl bg-[#EFF6FF] text-[#2563EB] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
            <div>
                <div class="text-4xl font-extrabold text-[#1E1B4B] tracking-tight mb-3">
                    {{ $activeCustomers }}
                </div>
                <div>
                    <span class="inline-block px-3 py-1 rounded-full bg-[#F1F3FA] text-[#1E1B4B] text-xs font-bold">
                        +12 Baru
                    </span>
                </div>
            </div>
        </div>

        <!-- Card 4: Antrian Mesin Aktif -->
        <div class="bg-white rounded-3xl p-6 border border-[#E9ECF4] shadow-sm flex flex-col justify-between hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-[#64748B]">Antrian Mesin Aktif</span>
                <div class="w-10 h-10 rounded-2xl bg-[#FEF3C7] text-[#D97706] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
            </div>
            <div>
                <div class="text-4xl font-extrabold text-[#1E1B4B] tracking-tight mb-3">
                    {{ $activeMachineQueue }}
                </div>
                <div>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-[#FEE2E2] text-[#B91C1C] text-xs font-bold">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                        {{ $urgentCount }} Urgent
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Section: Pesanan Terbaru Table -->
    <div class="bg-white rounded-[32px] p-6 md:p-8 border border-[#E9ECF4] shadow-sm">
        
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
                    <select name="status" 
                            onchange="this.form.submit()" 
                            class="px-4 py-2 bg-[#F8F9FD] border border-[#E9ECF4] rounded-full text-xs font-bold text-[#1E1B4B] focus:outline-none focus:border-[#1E1B4B] cursor-pointer shadow-sm">
                        <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Antrian Masuk</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Sedang Dicuci</option>
                        <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Siap Ambil</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </form>

                <a href="javascript:window.print()" 
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#F8F9FD] hover:bg-[#EEF1FA] border border-[#E9ECF4] rounded-full text-xs font-bold text-[#1E1B4B] shadow-sm transition">
                    <svg class="w-3.5 h-3.5 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Export Rekap</span>
                </a>
            </div>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-[11px] font-extrabold uppercase tracking-wider text-[#64748B] bg-[#F8F9FD] border-y border-[#EEF1F6]">
                        <th class="py-3.5 px-4 rounded-l-2xl">ID & WAKTU</th>
                        <th class="py-3.5 px-4">PELANGGAN</th>
                        <th class="py-3.5 px-4">LAYANAN</th>
                        <th class="py-3.5 px-4">TOTAL BAYAR</th>
                        <th class="py-3.5 px-4">STATUS</th>
                        <th class="py-3.5 px-4 text-center rounded-r-2xl">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F1F3F8]">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-[#FBFBFE] transition">
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
                                    {{ $order->customer->name ?? 'Pelanggan Umum' }}
                                </div>
                                <div class="text-xs text-[#64748B]">
                                    {{ $order->customer->phone ?? '0812-xxxx-xxxx' }}
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
                                             class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-[#E9ECF4] py-2 z-30 text-left text-xs font-semibold">
                                            <a href="{{ route('admin.pos') }}" class="block px-4 py-2 hover:bg-[#F8F9FD] text-[#1E1B4B]">Buka di POS / Kasir</a>
                                            <a href="{{ route('admin.payments.index') }}" class="block px-4 py-2 hover:bg-[#F8F9FD] text-[#1E1B4B]">Cek Pembayaran</a>
                                            <a href="{{ route('admin.deliveries.index') }}" class="block px-4 py-2 hover:bg-[#F8F9FD] text-[#1E1B4B]">Jadwal Pengiriman</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-sm text-[#94A3B8]">
                                Belum ada pesanan aktif saat ini.
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
</x-admin-layout>
