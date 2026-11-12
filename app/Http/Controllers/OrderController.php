<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $customer = Customer::where('user_id', $user->id)->first();

        $orders = $customer
            ? Order::with(['items.service', 'delivery', 'payment'])
                ->where('customer_id', $customer->id)
                ->latest()
                ->get()
            : collect();

        $statusLabels = [
            'pending'    => 'Menunggu Konfirmasi',
            'processing' => 'Sedang Dicuci',
            'ready'      => 'Siap Diambil/Diantar',
            'completed'  => 'Selesai',
            'cancelled'  => 'Dibatalkan',
        ];

        $formattedOrders = $orders->map(function ($order) {
            $payment = $order->payment;
            $paymentStatus = $payment->status ?? 'pending';

            // Sinkronisasi otomatis: Jika pembayaran sudah lunas tapi status pesanan masih pending,
            // otomatis perbarui ke 'processing' (Sedang Dicuci) agar stepper langsung maju.
            if ($order->status === 'pending' && $paymentStatus === 'completed') {
                $order->update(['status' => 'processing']);
                $order->status = 'processing';
            }

            // Sinkronisasi otomatis Delivery:
            // Jika status pengiriman kurir sudah 'on-way', otomatis sinkronkan status pesanan ke 'ready' (Sedang Diantar)
            if ($order->delivery && $order->delivery->status === 'on-way' && in_array($order->status, ['pending', 'processing'])) {
                $order->update(['status' => 'ready']);
                $order->status = 'ready';
            }

            // Jika status pengiriman kurir sudah 'completed', sinkronkan status pesanan ke 'completed'
            if ($order->delivery && $order->delivery->status === 'completed' && !in_array($order->status, ['completed', 'cancelled'])) {
                $order->update(['status' => 'completed']);
                $order->status = 'completed';
            }

            $isSelf = ($order->delivery->type ?? '') === 'pickup';

            $currentStatusLabel = match ($order->status) {
                'pending'    => 'Menunggu Konfirmasi',
                'processing' => 'Sedang Dicuci',
                'ready'      => $isSelf ? 'Siap Diambil di Outlet' : 'Sedang Diantar Kurir',
                'completed'  => 'Selesai',
                'cancelled'  => 'Dibatalkan',
                default      => ucfirst($order->status),
            };

            $paymentStatusLabels = [
                'pending'   => 'Menunggu Pembayaran',
                'completed' => 'Lunas',
                'failed'    => 'Gagal',
            ];

            $paymentMethodLabels = [
                'cash'        => $isSelf ? 'Bayar di Outlet (Pasca-Timbang)' : 'Jemput Dulu & Bayar Nanti (COD)',
                'transfer'    => 'Transfer Bank (VA)',
                'e-wallet'    => 'QRIS / E-Wallet',
                'credit_card' => 'Kartu Kredit',
            ];

            return [
                'id'                   => 'ROD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT),
                'raw_id'               => $order->id,
                'date'                 => $order->created_at ? $order->created_at->translatedFormat('d F Y, H:i') : '-',
                'status'               => $order->status,
                'statusLabel'          => $currentStatusLabel,
                'isSelf'               => $isSelf,
                'payment_status'       => $paymentStatus,
                'payment_status_label' => $paymentStatusLabels[$paymentStatus] ?? ucfirst($paymentStatus),
                'payment_method'       => $payment->method ?? '-',
                'payment_method_label' => $paymentMethodLabels[$payment->method ?? ''] ?? ucfirst($payment->method ?? '-'),
                'items'                => $order->items->map(function ($item) {
                    return [
                        'name'  => $item->service->name ?? 'Layanan Laundry',
                        'qty'   => (float) $item->quantity,
                        'unit'  => \App\Models\ServiceType::resolveUnit($item->service->name ?? ''),
                        'price' => (int) ($item->price * $item->quantity),
                    ];
                }),
                'total'          => (int) $order->total_price,
                'address'        => $order->delivery->address ?? ($order->customer->address ?? 'Ambil di Outlet Rodeo Malang'),
                'canCancel'      => $order->status === 'pending' && $paymentStatus !== 'completed',
                'canEditAddress' => in_array($order->status, ['pending', 'processing']),
                'month_key'      => $order->created_at ? $order->created_at->format('Y-m') : 'unknown',
                'month_label'    => $order->created_at ? $order->created_at->translatedFormat('F Y') : 'Lainnya',
                'is_active'      => in_array($order->status, ['pending', 'processing', 'ready']),
                'is_completed'   => in_array($order->status, ['completed', 'cancelled']),
            ];
        });

        $activeOrders = $formattedOrders->filter(fn ($o) => $o['is_active'])->values();
        $completedOrders = $formattedOrders->filter(fn ($o) => $o['is_completed'])->values();

        // Mengelompokkan riwayat yang sudah selesai / dibatalkan berdasarkan bulan (Folder Bulanan)
        $monthlyArchives = $completedOrders->groupBy('month_label')->map(function ($group, $monthLabel) {
            return [
                'month_label'  => $monthLabel,
                'month_key'    => $group->first()['month_key'] ?? '',
                'total_orders' => $group->count(),
                'total_amount' => (int) $group->sum('total'),
                'orders'       => $group->values(),
            ];
        })->values();

        $viewName = view()->exists('pelanggan.pesanan') ? 'pelanggan.pesanan' : 'pesanan';
        return view($viewName, [
            'orders'          => $formattedOrders,
            'activeOrders'    => $activeOrders,
            'completedOrders' => $completedOrders,
            'monthlyArchives' => $monthlyArchives,
        ]);
    }

    public function cancelCustomerOrder(Request $request, Order $order)
    {
        $customer = Customer::where('user_id', $request->user()->id)->first();
        if (!$customer || $order->customer_id !== $customer->id) {
            abort(403);
        }

        if ($order->status === 'pending') {
            $order->update(['status' => 'cancelled']);
            return back()->with('success', 'Pesanan berhasil dibatalkan.');
        }

        return back()->with('error', 'Pesanan yang sedang diproses tidak dapat dibatalkan.');
    }

    // Checkout pelanggan dari alur /pemesanan -> /pengiriman -> /pembayaran.
    public function checkout(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:service_types,id',
            'items.*.qty' => 'required|numeric|min:0.1',
            'payment_method' => 'required|in:qris,va,ewallet,cod',
            'pickup' => 'required|in:courier,self',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500',
        ]);

        $serviceIds = collect($data['items'])->pluck('id');
        $services = ServiceType::whereIn('id', $serviceIds)->get()->keyBy('id');
        $hasKiloanService = $services->contains(function ($svc) {
            return $svc->unit === 'kg';
        });

        if ($hasKiloanService && $data['payment_method'] !== 'cod') {
            return response()->json([
                'message' => 'Layanan kiloan wajib menggunakan metode Bayar Nanti (COD / Pasca-Timbang) karena berat pakaian harus dipastikan melalui timbangan resmi outlet.',
            ], 422);
        }

        $user = $request->user();
        $customer = Customer::firstOrCreate(
            ['user_id' => $user->id],
            ['name' => $user->username, 'email' => $user->email]
        );

        $rawAddress = trim($data['address'] ?? '');
        $isDummyPlaceholder = empty($rawAddress) || str_contains(strtolower($rawAddress), 'silakan tambah alamat') || str_contains(strtolower($rawAddress), 'tambah alamat di menu profil');

        if (!$isDummyPlaceholder) {
            if (empty($customer->address) || str_contains(strtolower($customer->address), 'silakan tambah alamat')) {
                $customer->update(['address' => $rawAddress]);
            }

            if (!\App\Models\Address::where('user_id', $user->id)->exists()) {
                \App\Models\Address::create([
                    'user_id'        => $user->id,
                    'label'          => 'Utama',
                    'recipient_name' => $customer->name ?? $user->username,
                    'phone'          => $customer->phone ?? '',
                    'address'        => $rawAddress,
                    'city'           => $customer->city ?? 'Kota Malang',
                    'is_default'     => true,
                ]);
            }
        }

        DB::transaction(function () use ($data, $customer, $services) {
            $order = Order::create([
                'customer_id' => $customer->id,
                'total_price' => 0,
                'status' => 'pending',
                'estimated_completion_time' => now()->addDays(2),
            ]);

            $total = 0;
            foreach ($data['items'] as $row) {
                $price = $services[$row['id']]->price;
                $order->items()->create(['service_id' => $row['id'], 'quantity' => $row['qty'], 'price' => $price]);
                $total += $price * $row['qty'];
            }
            $order->update(['total_price' => $total]);

            Payment::create([
                'order_id' => $order->id,
                'amount' => $total,
                'method' => match ($data['payment_method']) {
                    'qris', 'ewallet' => 'e-wallet',
                    'va' => 'transfer',
                    default => 'cash',
                },
                'status' => 'pending',
            ]);

            $delivery = $data['pickup'] === 'courier';
            $addrText = !empty($data['address']) ? $data['address'] : ($customer->address ?? 'Alamat belum diisi');
            Delivery::create([
                'order_id' => $order->id,
                'type' => $delivery ? 'delivery' : 'pickup',
                'address' => ($delivery ? $addrText : 'Ambil di Outlet Rodeo Malang')
                    . ($data['notes'] ?? '' ? ' | Catatan: ' . $data['notes'] : ''),
                'scheduled_date' => now()->addDays(2),
                'status' => 'scheduled',
            ]);
        });

        return response()->json(['redirect' => route('pesanan.index')]);
    }
}