<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Nota #RD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }} - Rodeo Laundry</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Courier New', Courier, monospace, monospace;
        }
        body {
            background-color: #f3f4f6;
            color: #111827;
            padding: 20px 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .receipt-card {
            background: #ffffff;
            width: 100%;
            max-width: 380px;
            padding: 24px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border: 1px dashed #cbd5e1;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .text-xs { font-size: 11px; }
        .text-sm { font-size: 12px; }
        .text-base { font-size: 14px; }
        .text-lg { font-size: 16px; }
        .divider {
            border-top: 1px dashed #94a3b8;
            margin: 12px 0;
        }
        .double-divider {
            border-top: 2px dashed #475569;
            margin: 12px 0;
        }
        .flex-between {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 4px;
        }
        .item-row {
            margin-bottom: 6px;
        }
        .btn-bar {
            margin-top: 16px;
            display: flex;
            gap: 10px;
            width: 100%;
            max-width: 380px;
        }
        .btn {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }
        .btn-print {
            background-color: #1e1b4b;
            color: #ffffff;
        }
        .btn-close {
            background-color: #e2e8f0;
            color: #334155;
        }
        @media print {
            body {
                background: none;
                padding: 0;
            }
            .receipt-card {
                box-shadow: none;
                border: none;
                max-width: 100%;
                width: 100%;
                padding: 0;
            }
            .btn-bar {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="receipt-card">
        <!-- Header Toko -->
        <div class="text-center">
            <h1 class="bold text-lg" style="letter-spacing: 1px;">RODEO LAUNDRY</h1>
            <p class="text-xs">Bersih, Cepat & Wangi • Buka 07.00 - 21.00 WIB</p>
            <p class="text-xs">Gg. Rodeo, RT.04/RW.10, Sumberejo, Kec. Batu, Kota Batu 65318</p>
            <p class="text-xs">WhatsApp: +62 813-5805-5257</p>
        </div>

        <div class="double-divider"></div>

        <!-- Info Nota -->
        <div class="text-xs">
            <div class="flex-between">
                <span>No. Nota:</span>
                <span class="bold">#RD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="flex-between">
                <span>Tanggal:</span>
                <span>{{ $order->created_at->format('d/m/Y H:i') }} WIB</span>
            </div>
            <div class="flex-between">
                <span>Kasir:</span>
                <span>Admin Rodeo</span>
            </div>
            <div class="flex-between">
                <span>Pelanggan:</span>
                <span class="bold">{{ $order->customer->name ?? 'Pelanggan Umum' }}</span>
            </div>
            @if($order->customer && $order->customer->phone)
            <div class="flex-between">
                <span>No. Telp:</span>
                <span>{{ $order->customer->phone }}</span>
            </div>
            @endif
        </div>

        <div class="divider"></div>

        <!-- Daftar Item Layanan -->
        <div class="text-xs">
            <div class="flex-between bold" style="margin-bottom: 8px;">
                <span style="flex: 2;">LAYANAN</span>
                <span style="flex: 1;" class="text-center">QTY</span>
                <span style="flex: 1;" class="text-right">TOTAL</span>
            </div>

            @forelse($order->items as $item)
                <div class="item-row">
                    <div class="bold">{{ $item->service->name ?? 'Layanan Laundry' }}</div>
                    <div class="flex-between" style="color: #475569;">
                        <span>@ Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                        <span class="text-center">{{ $item->quantity + 0 }} {{ $item->service->unit ?? \App\Models\ServiceType::resolveUnit($item->service->name ?? '') }}</span>
                        <span class="text-right bold" style="color: #111827;">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                    </div>
                </div>
            @empty
                <div class="text-center" style="color: #94a3b8;">Tidak ada rincian item</div>
            @endforelse
        </div>

        <div class="divider"></div>

        <!-- Rincian Total -->
        <div class="text-xs">
            <div class="flex-between bold text-sm" style="margin-top: 4px;">
                <span>TOTAL HARGA:</span>
                <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
            @php
                $payment = $order->payment ?? \App\Models\Payment::where('order_id', $order->id)->first();
                $isPaid = $payment && $payment->status === 'completed';
            @endphp
            <div class="flex-between" style="margin-top: 6px;">
                <span>Metode Pembayaran:</span>
                <span class="bold">{{ strtoupper($payment->method ?? 'Cash / Tunai') }}</span>
            </div>
            <div class="flex-between" style="margin-top: 2px;">
                <span>Status Pembayaran:</span>
                <span class="bold" style="color: {{ $isPaid ? '#15803d' : '#b91c1c' }};">
                    {{ $isPaid ? '[ LUNAS ]' : '[ BELUM LUNAS ]' }}
                </span>
            </div>
            <div class="flex-between" style="margin-top: 2px;">
                <span>Status Order:</span>
                <span class="bold">{{ strtoupper($order->status) }}</span>
            </div>
        </div>

        <div class="double-divider"></div>

        <!-- Syarat & Ketentuan -->
        <div class="text-center text-xs" style="color: #64748b; font-size: 10px; line-height: 1.4;">
            <p>1. Pengambilan cucian wajib membawa struk ini.</p>
            <p>2. Komplain maksimal 1x24 jam setelah cucian diambil.</p>
            <p>3. Cucian yang tidak diambil >30 hari di luar tanggung jawab kami.</p>
            <p class="bold" style="margin-top: 8px; color: #1e1b4b;">~ Terima Kasih Atas Kunjungan Anda ~</p>
        </div>
    </div>

    <!-- Tombol Cetak / Tutup -->
    <div class="btn-bar">
        <button onclick="window.print()" class="btn btn-print">🖨️ Cetak Struk</button>
        <button onclick="window.close()" class="btn btn-close">Tutup</button>
    </div>

    <script>
        // Auto trigger print saat popup dibuka
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 400);
        };
    </script>
</body>
</html>
