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
    public function index()
    {
        $orders = Order::with(['customer', 'items.service'])
            ->latest()
            ->get();
            
        return view('pesanan', compact('orders'));
    }

    public function create()
    {
        return view('orders.create', [
            'customers' => Customer::orderBy('name')->get(),
            'services' => ServiceType::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'required|exists:service_types,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Harga diambil dari server, bukan dari input client.
        DB::transaction(function () use ($data) {
            $services = ServiceType::whereIn('id', collect($data['items'])->pluck('service_id'))->get()->keyBy('id');
            $order = Order::create(['customer_id' => $data['customer_id'], 'total_price' => 0]);
            $total = 0;
            foreach ($data['items'] as $row) {
                $price = $services[$row['service_id']]->price;
                $order->items()->create([
                    'service_id' => $row['service_id'],
                    'quantity' => $row['quantity'],
                    'price' => $price,
                ]);
                $total += $price * $row['quantity'];
            }
            $order->update(['total_price' => $total]);
        });

        return redirect()->route('orders.index')->with('success', 'Order dibuat.');
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
            'notes' => 'nullable|string|max:500',
        ]);

        $user = $request->user();
        $customer = Customer::firstOrCreate(
            ['user_id' => $user->id],
            ['name' => $user->username, 'email' => $user->email]
        );

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
            Delivery::create([
                'order_id' => $order->id,
                'type' => $delivery ? 'delivery' : 'pickup',
                'address' => ($delivery ? ($customer->address ?? 'Alamat belum diisi') : 'Ambil di Outlet Rodeo Malang')
                    . ($data['notes'] ?? '' ? ' | Catatan: ' . $data['notes'] : ''),
                'scheduled_date' => now()->addDays(2),
                'status' => 'scheduled',
            ]);
        });

        return response()->json(['redirect' => route('pesanan.index')]);
    }

    public function show(Order $item)
    {
        return view('orders.show', ['item' => $item->load('customer', 'items.service', 'payments', 'deliveries')]);
    }

    public function edit(Order $item)
    {
        return view('orders.edit', ['item' => $item]);
    }

    // Hanya status yang bisa diubah; item/total tetap.
    public function update(Request $request, Order $item)
    {
        $item->update($request->validate([
            'status' => 'required|in:pending,processing,ready,completed,cancelled',
        ]));
        return redirect()->route('orders.show', $item)->with('success', 'Status diperbarui.');
    }

    public function destroy(Order $item)
    {
        $item->delete();
        return redirect()->route('orders.index')->with('success', 'Order dihapus.');
    }
}