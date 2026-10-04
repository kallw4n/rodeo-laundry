<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    private function rules(): array
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'type' => 'required|in:pickup,delivery',
            'address' => 'required|string',
            'scheduled_date' => 'nullable|date',
            'status' => 'required|in:scheduled,on-way,completed,failed',
        ];
    }

    public function index()
    {
        return view('deliveries.index', ['items' => Delivery::with("order")->latest()->paginate(15)]);
    }

    public function create()
    {
        return view('deliveries.create', ['orders' => Order::latest()->get()]);
    }

    public function store(Request $request)
    {
        Delivery::create($request->validate($this->rules()));
        return redirect()->route('deliveries.index')->with('success', 'Data disimpan.');
    }

    public function show(Delivery $item)
    {
        return view('deliveries.show', ['item' => $item]);
    }

    public function edit(Delivery $item)
    {
        return view('deliveries.edit', ['item' => $item, 'orders' => Order::latest()->get()]);
    }

    public function update(Request $request, Delivery $item)
    {
        $item->update($request->validate($this->rules()));
        return redirect()->route('deliveries.index')->with('success', 'Data diperbarui.');
    }

    public function destroy(Delivery $item)
    {
        $item->delete();
        return redirect()->route('deliveries.index')->with('success', 'Data dihapus.');
    }
}