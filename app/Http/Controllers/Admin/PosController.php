<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PosController extends Controller
{
    public function index()
    {
        $customers = Customer::orderBy('name')->get();
        $services = ServiceType::all();
        $invoiceNumber = 'ROD-' . rand(1000, 9999);

        return view('admin.pos', compact('customers', 'services', 'invoiceNumber'));
    }

    public function storeOrder(Request $request)
    {
        $request->validate([
            'customer_id'    => 'required|exists:customers,id',
            'items'          => 'required|array|min:1',
            'items.*.id'     => 'required|exists:service_types,id',
            'items.*.qty'    => 'required|numeric|min:0.1',
            'payment_method' => 'required|string',
            'cash_received'  => 'nullable|numeric',
        ]);

        $order = DB::transaction(function () use ($request) {
            $customer = Customer::findOrFail($request->customer_id);
            $serviceMap = ServiceType::whereIn('id', collect($request->items)->pluck('id'))->get()->keyBy('id');

            $totalPrice = 0;
            $itemsData = [];

            foreach ($request->items as $row) {
                $service = $serviceMap[$row['id']];
                $subtotal = $service->price * (float)$row['qty'];
                $totalPrice += $subtotal;

                $itemsData[] = [
                    'service_id' => $service->id,
                    'quantity'   => $row['qty'],
                    'price'      => $service->price,
                ];
            }

            // Create Order
            $order = Order::create([
                'customer_id'               => $customer->id,
                'total_price'               => $totalPrice,
                'status'                    => 'processing',
                'estimated_completion_time' => Carbon::now()->addDays(2),
            ]);

            // Create Order Items
            foreach ($itemsData as $item) {
                $order->items()->create($item);
            }

            // Map payment method to DB enum
            $method = match(strtolower($request->payment_method ?? 'cash')) {
                'qris', 'e-wallet' => 'e-wallet',
                'transfer', 'bank' => 'transfer',
                'credit_card', 'kartu' => 'credit_card',
                default => 'cash',
            };

            // Create Payment
            Payment::create([
                'order_id' => $order->id,
                'amount'   => $totalPrice,
                'method'   => $method,
                'status'   => 'completed',
            ]);

            // Create Delivery
            Delivery::create([
                'order_id'       => $order->id,
                'type'           => $request->delivery_type === 'delivery' ? 'delivery' : 'pickup',
                'address'        => $customer->address ?? 'Ambil di Outlet Rodeo Malang',
                'scheduled_date' => Carbon::now()->addDays(2),
                'status'         => 'scheduled',
            ]);

            // Update customer loyalty and total spent
            $customer->total_spent += $totalPrice;
            $customer->loyalty_points += (int) floor($totalPrice / 10000);
            $customer->save();

            return $order;
        });

        return redirect()->route('admin.pos')->with('success', 'Transaksi Kasir #' . $order->id . ' berhasil disimpan & nota dicetak!');
    }
}
