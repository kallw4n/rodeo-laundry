<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    private function rules(): array
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,transfer,e-wallet,credit_card',
            'status' => 'required|in:pending,completed,failed',
        ];
    }

    public function index()
    {
        return view('payments.index', ['items' => Payment::with("order")->latest()->paginate(15)]);
    }

    public function create()
    {
        return view('payments.create', ['orders' => Order::latest()->get()]);
    }

    public function store(Request $request)
    {
        Payment::create($request->validate($this->rules()));
        return redirect()->route('payments.index')->with('success', 'Data disimpan.');
    }

    public function show(Payment $item)
    {
        return view('payments.show', ['item' => $item]);
    }

    public function edit(Payment $item)
    {
        return view('payments.edit', ['item' => $item, 'orders' => Order::latest()->get()]);
    }

    public function update(Request $request, Payment $item)
    {
        $item->update($request->validate($this->rules()));
        return redirect()->route('payments.index')->with('success', 'Data diperbarui.');
    }

    public function destroy(Payment $item)
    {
        $item->delete();
        return redirect()->route('payments.index')->with('success', 'Data dihapus.');
    }
}