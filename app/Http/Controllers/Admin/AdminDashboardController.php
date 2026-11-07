<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalOrders = Order::count();

        // Tanggal yang dipilih (default hari ini)
        $selectedDate = $request->query('date', Carbon::today()->toDateString());
        $isDateFiltered = $request->filled('date');

        // Total pesanan hari ini / pada tanggal terpilih
        $todayOrdersCount = Order::whereDate('created_at', $selectedDate)->count();

        // Total pendapatan pada tanggal terpilih (Real-time, tanpa fallback data lama/dummy)
        $todayPaymentRevenue = Payment::where('status', 'completed')
            ->where(function ($q) use ($selectedDate) {
                $q->whereDate('created_at', $selectedDate)
                  ->orWhereDate('updated_at', $selectedDate);
            })
            ->sum('amount');

        $todayOrderRevenue = Order::whereDate('created_at', $selectedDate)
            ->where('status', '!=', 'cancelled')
            ->sum('total_price');

        $todayRevenue = (float) max($todayPaymentRevenue, $todayOrderRevenue);

        // Metrik baru: Order Dalam Proses & Order Selesai
        $inProcessOrders = Order::whereIn('status', ['pending', 'processing'])->count();
        $completedOrders = Order::whereIn('status', ['ready', 'completed'])->count();

        // Kompatibilitas variabel lama
        $activeCustomers = $inProcessOrders;
        $activeMachineQueue = $completedOrders;
        $urgentCount = Order::where('status', 'processing')->count();

        // Query pesanan terbaru dengan filter status jika ada
        $query = Order::with(['customer', 'items.service'])->latest();

        // Filter tanggal dari widget kalender di header
        if ($isDateFiltered) {
            $query->whereDate('created_at', $selectedDate);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $cleanNumeric = preg_replace('/[^0-9]/', '', $search);

            $query->where(function ($q) use ($search, $cleanNumeric) {
                $q->where('id', 'like', "%{$search}%");
                if (!empty($cleanNumeric)) {
                    $q->orWhere('id', (int)$cleanNumeric);
                }
                $q->orWhereHas('customer', function ($cq) use ($search) {
                    $cq->where('name', 'like', "%{$search}%")
                       ->orWhere('phone', 'like', "%{$search}%");
                });
                $q->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $recentOrders = $query->paginate(10)->withQueryString();

        return view('admin.dashboard', compact(
            'totalOrders',
            'todayOrdersCount',
            'todayRevenue',
            'selectedDate',
            'isDateFiltered',
            'inProcessOrders',
            'completedOrders',
            'activeCustomers',
            'activeMachineQueue',
            'urgentCount',
            'recentOrders'
        ));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', Rule::in(['pending', 'processing', 'ready', 'completed', 'cancelled'])],
        ]);

        $order->update(['status' => $request->status]);

        if ($order->delivery) {
            if ($request->status === 'ready') {
                $order->delivery->update(['status' => 'on-way']);
            } elseif ($request->status === 'completed') {
                $order->delivery->update(['status' => 'completed']);
            } elseif (in_array($request->status, ['pending', 'processing'])) {
                $order->delivery->update(['status' => 'scheduled']);
            }
        }

        return back()->with('success', 'Status pesanan #RD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT) . ' diperbarui.');
    }

    public function printReceipt(Order $order)
    {
        $order->load(['customer', 'items.service', 'payment', 'delivery']);
        return view('admin.orders.receipt', compact('order'));
    }
}
