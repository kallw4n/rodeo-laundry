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

        // Total pendapatan hari ini (atau total keseluruhan pembayaran lunas jika seeder belum ada hari ini)
        $todayRevenue = Payment::where('status', 'completed')
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');

        if ($todayRevenue == 0) {
            $todayRevenue = Payment::where('status', 'completed')->sum('amount');
        }

        $activeCustomers = Customer::count();

        $activeMachineQueue = Order::whereIn('status', ['pending', 'processing'])->count();
        $urgentCount = Order::where('status', 'processing')->count();

        // Query pesanan terbaru dengan filter status jika ada
        $query = Order::with(['customer', 'items.service'])->latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $recentOrders = $query->paginate(10)->withQueryString();

        return view('admin.dashboard', compact(
            'totalOrders',
            'todayRevenue',
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

        return back()->with('success', 'Status pesanan #RD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT) . ' diperbarui.');
    }
}
