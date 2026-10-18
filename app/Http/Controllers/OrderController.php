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

        $formattedOrders = $orders->map(function ($order) use ($statusLabels) {
            return [
                'id'             => 'ROD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT),
                'raw_id'         => $order->id,
                'date'           => $order->created_at ? $order->created_at->translatedFormat('d F Y, H:i') : '-',
                'status'         => $order->status,
                'statusLabel'    => $statusLabels[$order->status] ?? ucfirst($order->status),
                'items'          => $order->items->map(function ($item) {
                    $name = strtolower($item->service->name ?? '');
                    $unit = 'pcs';
                    if (str_contains($name, 'kiloan')) $unit = 'kg';
                    elseif (str_contains($name, 'sepatu')) $unit = 'psg';
                    elseif (str_contains($name, 'gorden')) $unit = 'meter';

                    return [
                        'name'  => $item->service->name ?? 'Layanan Laundry',
                        'qty'   => (float) $item->quantity,
                        'unit'  => $unit,
                        'price' => (int) ($item->price * $item->quantity),
                    ];
                }),
                'total'          => (int) $order->total_price,
                'address'        => $order->delivery->address ?? ($order->customer->address ?? 'Ambil di Outlet Rodeo Malang'),
                'canCancel'      => $order->status === 'pending',
                'canEditAddress' => in_array($order->status, ['pending', 'processing']),
            ];
        });

        return view('pesanan', ['orders' => $formattedOrders]);
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

        $user = $request->user();
        $customer = Customer::firstOrCreate(
            ['user_id' => $user->id],
            ['name' => $user->username, 'email' => $user->email]
        );

        if (!empty($data['address']) && empty($customer->address)) {
            $customer->update(['address' => $data['address']]);
        }

        DB::transaction(function () use ($data, $customer) {
            $services = ServiceType::whereIn('id', collect($data['items'])->pluck('id'))->get()->keyBy('id');
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