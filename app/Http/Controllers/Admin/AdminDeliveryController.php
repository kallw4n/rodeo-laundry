<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminDeliveryController extends Controller
{
    public function index(Request $request)
    {
        $query = Delivery::with(['order.customer', 'order.items.service']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('address', 'like', "%{$search}%")
                  ->orWhere('order_id', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($oq) use ($search) {
                      $oq->where('customer_name', 'like', "%{$search}%")
                         ->orWhere('customer_phone', 'like', "%{$search}%")
                         ->orWhereHas('customer', function ($cq) use ($search) {
                             $cq->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                         });
                  });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $sort = $request->get('sort', 'latest');
        if ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $deliveries = $query->paginate(10)->withQueryString();
        $totalDeliveries = Delivery::count();

        // Orders that don't have delivery or need delivery scheduling
        $readyOrders = Order::with('customer', 'items.service')
            ->whereIn('status', ['ready', 'processing', 'completed'])
            ->latest()
            ->take(20)
            ->get();

        return view('admin.deliveries.index', compact(
            'deliveries',
            'totalDeliveries',
            'readyOrders'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id'       => 'required|exists:orders,id',
            'address'        => 'required|string',
            'status'         => 'required|in:scheduled,on-way,completed,failed',
            'scheduled_date' => 'nullable|date',
            'notes'          => 'nullable|string',
        ]);

        $addr = trim($request->address);
        if ($request->filled('notes')) {
            $addr .= ' | Catatan: ' . trim($request->notes);
        }

        $delivery = Delivery::create([
            'order_id'       => $request->order_id,
            'type'           => 'delivery',
            'address'        => $addr,
            'status'         => $request->status,
            'scheduled_date' => $request->scheduled_date ?? now(),
        ]);

        if ($delivery->order) {
            if ($request->status === 'on-way') {
                $delivery->order->update(['status' => 'ready']);
            } elseif ($request->status === 'completed') {
                $delivery->order->update(['status' => 'completed']);
            }
        }

        return redirect()->route('admin.deliveries.index')->with('success', 'Jadwal pengantaran untuk Nota #' . $request->order_id . ' berhasil ditambahkan!');
    }

    public function update(Request $request, Delivery $delivery)
    {
        $request->validate([
            'address'        => 'required|string',
            'status'         => 'required|in:scheduled,on-way,completed,failed',
            'scheduled_date' => 'nullable|date',
            'notes'          => 'nullable|string',
        ]);

        $addr = trim($request->address);
        if ($request->filled('notes')) {
            $note = trim($request->notes);
            $addr .= ' | ' . (str_starts_with(strtolower($note), 'catatan') ? $note : 'Catatan: ' . $note);
        }

        $delivery->update([
            'address'        => $addr,
            'status'         => $request->status,
            'scheduled_date' => $request->scheduled_date ?? $delivery->scheduled_date,
        ]);

        if ($delivery->order) {
            if ($request->status === 'on-way' && in_array($delivery->order->status, ['pending', 'processing'])) {
                $delivery->order->update(['status' => 'ready']);
            } elseif ($request->status === 'completed') {
                $delivery->order->update(['status' => 'completed']);
            } elseif ($request->status === 'scheduled' && $delivery->order->status === 'ready') {
                $delivery->order->update(['status' => 'processing']);
            }
        }

        return redirect()->route('admin.deliveries.index')->with('success', 'Data pengantaran Nota #' . $delivery->order_id . ' berhasil diperbarui!');
    }

    public function updateStatus(Request $request, Delivery $delivery)
    {
        $request->validate([
            'status' => 'required|in:scheduled,on-way,completed,failed',
        ]);

        $delivery->update(['status' => $request->status]);

        if ($delivery->order) {
            if ($request->status === 'on-way' && in_array($delivery->order->status, ['pending', 'processing'])) {
                $delivery->order->update(['status' => 'ready']);
            } elseif ($request->status === 'completed') {
                $delivery->order->update(['status' => 'completed']);
            } elseif ($request->status === 'scheduled' && $delivery->order->status === 'ready') {
                $delivery->order->update(['status' => 'processing']);
            }
        }

        $statusText = match ($request->status) {
            'on-way'    => 'Sedang di Antar oleh Driver',
            'completed' => 'Telah Berhasil Diterima Customer',
            'failed'    => 'Pengantaran Gagal',
            default     => 'Jadwal Antar Diperbarui',
        };

        return redirect()->route('admin.deliveries.index')->with('success', 'Status pengiriman #' . $delivery->order_id . ' diubah ke: ' . $statusText);
    }

    public function destroy(Delivery $delivery)
    {
        $orderId = $delivery->order_id;
        $delivery->delete();

        return redirect()->route('admin.deliveries.index')->with('success', 'Data pengantaran Nota #' . $orderId . ' berhasil dihapus.');
    }
}
