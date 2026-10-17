<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\ServiceType;
use Illuminate\Http\Request;

class AdminPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['order.customer', 'order.items.service']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhereHas('order.customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $sort = $request->get('sort', 'latest');
        if ($sort === 'oldest') {
            $query->oldest();
        } elseif ($sort === 'amount_desc') {
            $query->orderBy('amount', 'desc');
        } elseif ($sort === 'amount_asc') {
            $query->orderBy('amount', 'asc');
        } else {
            $query->latest();
        }

        $payments = $query->paginate(10)->withQueryString();
        $totalPayments = Payment::count();

        // Data for Add Payment Modal
        $unpaidOrders = Order::with('customer', 'items.service')
            ->whereDoesntHave('payment', function ($pq) {
                $pq->where('status', 'completed');
            })
            ->latest()
            ->get();

        $allCustomers = Customer::orderBy('name')->get();
        $allServices = ServiceType::all();

        return view('admin.payments.index', compact(
            'payments',
            'totalPayments',
            'unpaidOrders',
            'allCustomers',
            'allServices'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount'   => 'required|numeric|min:0',
            'method'   => 'required|in:cash,transfer,e-wallet,credit_card',
            'status'   => 'required|in:pending,completed,failed',
        ]);

        Payment::create([
            'order_id' => $request->order_id,
            'amount'   => $request->amount,
            'method'   => $request->method,
            'status'   => $request->status,
        ]);

        return redirect()->route('admin.payments.index')->with('success', 'Penerimaan pembayaran Nota #' . $request->order_id . ' berhasil dicatat!');
    }

    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,transfer,e-wallet,credit_card',
            'status' => 'required|in:pending,completed,failed',
        ]);

        $payment->update([
            'amount' => $request->amount,
            'method' => $request->method,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.payments.index')->with('success', 'Status pembayaran Nota #' . $payment->order_id . ' berhasil diperbarui!');
    }

    public function destroy(Payment $payment)
    {
        $orderId = $payment->order_id;
        $payment->delete();

        return redirect()->route('admin.payments.index')->with('success', 'Data pembayaran Nota #' . $orderId . ' berhasil dihapus.');
    }
}
