<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    /**
     * Membangun query pesanan berdasarkan filter periode dan status
     */
    protected function buildQuery(Request $request)
    {
        $query = Order::with(['customer', 'items.service', 'payment'])->latest();

        $period = $request->get('period', 'today');

        if ($period === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($period === 'date' && $request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        } elseif ($period === 'week') {
            // 7 hari terakhir
            $query->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay());
        } elseif ($period === 'month') {
            // 30 hari terakhir
            $query->where('created_at', '>=', Carbon::now()->subDays(29)->startOfDay());
        } elseif ($period === 'this_month') {
            // Bulan kalender berjalan
            $query->whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ]);
        } elseif ($period === 'custom') {
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ]);
            } elseif ($request->filled('start_date')) {
                $query->where('created_at', '>=', Carbon::parse($request->start_date)->startOfDay());
            } elseif ($request->filled('end_date')) {
                $query->where('created_at', '<=', Carbon::parse($request->end_date)->endOfDay());
            }
        }
        // Jika period === 'all', tidak ada batasan tanggal

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

        return $query;
    }

    /**
     * Label teks periode yang mudah dibaca untuk kop laporan
     */
    protected function getPeriodLabel(Request $request): string
    {
        $period = $request->get('period', 'today');

        if ($period === 'today') {
            return 'Hari Ini (' . Carbon::today()->translatedFormat('d F Y') . ')';
        } elseif ($period === 'date' && $request->filled('date')) {
            return 'Tanggal: ' . Carbon::parse($request->date)->translatedFormat('d F Y');
        } elseif ($period === 'week') {
            return '7 Hari Terakhir (' . Carbon::now()->subDays(6)->translatedFormat('d M') . ' - ' . Carbon::now()->translatedFormat('d M Y') . ')';
        } elseif ($period === 'month') {
            return '30 Hari Terakhir (' . Carbon::now()->subDays(29)->translatedFormat('d M') . ' - ' . Carbon::now()->translatedFormat('d M Y') . ')';
        } elseif ($period === 'this_month') {
            return 'Bulan Ini (' . Carbon::now()->translatedFormat('F Y') . ')';
        } elseif ($period === 'custom') {
            $start = $request->filled('start_date') ? Carbon::parse($request->start_date)->translatedFormat('d M Y') : 'Awal';
            $end = $request->filled('end_date') ? Carbon::parse($request->end_date)->translatedFormat('d M Y') : 'Sekarang';
            return "Rentang Tanggal: {$start} s/d {$end}";
        }

        return 'Semua Waktu';
    }

    /**
     * Menampilkan halaman cetak laporan yang bersih dan terisolasi untuk PDF/Print
     */
    public function print(Request $request)
    {
        $orders = $this->buildQuery($request)->get();
        $periodLabel = $this->getPeriodLabel($request);

        $totalOrders = $orders->count();
        $totalRevenue = $orders->where('status', '!=', 'cancelled')->sum('total_price');
        $completedCount = $orders->whereIn('status', ['ready', 'completed'])->count();
        $processingCount = $orders->whereIn('status', ['pending', 'processing'])->count();
        $cancelledCount = $orders->where('status', 'cancelled')->count();

        $statusFilter = $request->get('status', 'all');

        return view('admin.reports.print', compact(
            'orders',
            'periodLabel',
            'totalOrders',
            'totalRevenue',
            'completedCount',
            'processingCount',
            'cancelledCount',
            'statusFilter'
        ));
    }

    /**
     * Mengunduh rekap laporan dalam format file spreadsheet CSV (bisa dibuka langsung di Excel)
     */
    public function export(Request $request)
    {
        $orders = $this->buildQuery($request)->get();
        $periodLabel = $this->getPeriodLabel($request);

        $cleanPeriodName = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $request->get('period', 'today')));
        $filename = 'laporan_rekap_rodeo_' . $cleanPeriodName . '_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($orders, $periodLabel) {
            $handle = fopen('php://output', 'w');
            // Menulis UTF-8 BOM agar aksen dan format terbaca rapi di Microsoft Excel
            fputs($handle, "\xEF\xBB\xBF");

            // Header Informasi Laporan
            fputcsv($handle, ['LAPORAN REKAPITULASI TRANSAKSI - RODEO LAUNDRY']);
            fputcsv($handle, ['Alamat Outlet:', 'Gg. Rodeo, RT.04/RW.10, Sumberejo, Kec. Batu, Kota Batu, Jawa Timur 65318']);
            fputcsv($handle, ['WhatsApp CS:', '+62 813-5805-5257']);
            fputcsv($handle, ['Jam Operasional:', 'Buka Setiap Hari: 07.00 - 21.00 WIB']);
            fputcsv($handle, ['Periode Laporan:', $periodLabel]);
            fputcsv($handle, ['Waktu Download:', Carbon::now()->translatedFormat('d F Y, H:i') . ' WIB']);
            fputcsv($handle, ['Total Pesanan:', $orders->count() . ' Pesanan']);
            fputcsv($handle, ['Total Omset / Pendapatan:', 'Rp ' . number_format($orders->where('status', '!=', 'cancelled')->sum('total_price'), 0, ',', '.')]);
            fputcsv($handle, []); // Baris kosong pembatas

            // Baris Judul Kolom Tabel
            fputcsv($handle, [
                'No',
                'No Nota',
                'Tanggal & Waktu Masuk',
                'Nama Pelanggan',
                'No Telepon',
                'Rincian Layanan & Item',
                'Total Qty / Berat',
                'Metode Pembayaran',
                'Status Pembayaran',
                'Total Tagihan (Rp)',
                'Status Pesanan'
            ]);

            foreach ($orders as $index => $order) {
                $customer = $order->customer;
                $itemsSummary = $order->items->map(function ($it) {
                    $serviceName = $it->service ? $it->service->name : 'Layanan';
                    return "{$serviceName} ({$it->quantity})";
                })->implode('; ');

                $totalQty = $order->items->sum('quantity');
                $paymentMethod = $order->payment ? strtoupper($order->payment->payment_method ?? 'CASH') : 'CASH';
                $paymentStatus = $order->payment && $order->payment->status === 'completed' ? 'Lunas' : 'Belum Lunas';

                $statusIndo = match ($order->status) {
                    'pending' => 'Antrian Masuk',
                    'processing' => 'Sedang Dicuci',
                    'ready' => 'Siap Ambil',
                    'completed' => 'Selesai',
                    'cancelled' => 'Dibatalkan',
                    default => ucfirst($order->status),
                };

                fputcsv($handle, [
                    $index + 1,
                    '#RD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT),
                    $order->created_at ? $order->created_at->format('d/m/Y H:i') : '-',
                    $order->customer_display_name,
                    $order->customer_display_phone,
                    $itemsSummary ?: 'Layanan Reguler',
                    $totalQty,
                    $paymentMethod,
                    $paymentStatus,
                    (float) $order->total_price,
                    $statusIndo,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
