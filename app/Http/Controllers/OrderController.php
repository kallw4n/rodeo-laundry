<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        return view('orders.index', ['items' => Order::with('customer')->latest()->paginate(15)]);
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