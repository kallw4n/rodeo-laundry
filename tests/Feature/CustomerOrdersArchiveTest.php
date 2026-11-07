<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOrdersArchiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_orders_are_divided_into_active_and_monthly_archives(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create([
            'user_id' => $user->id,
            'name' => 'Fahri Haikal',
            'email' => $user->email,
        ]);

        $svc = ServiceType::create(['name' => 'Cuci Kiloan Reguler', 'price' => 7000, 'description' => 'x']);

        // 1. Pesanan aktif (Sedang Dicuci)
        $activeOrder = Order::create([
            'customer_id' => $customer->id,
            'total_price' => 35000,
            'status' => 'processing',
            'created_at' => now(),
        ]);
        Payment::create(['order_id' => $activeOrder->id, 'amount' => 35000, 'method' => 'cash', 'status' => 'completed']);

        // 2. Pesanan selesai bulan ini (November 2026)
        $completedOrder = Order::create([
            'customer_id' => $customer->id,
            'total_price' => 50000,
            'status' => 'completed',
            'created_at' => now(),
        ]);
        Payment::create(['order_id' => $completedOrder->id, 'amount' => 50000, 'method' => 'cash', 'status' => 'completed']);

        // 3. Pesanan selesai 2 bulan lalu
        $pastCompletedOrder = Order::create([
            'customer_id' => $customer->id,
            'total_price' => 70000,
            'status' => 'completed',
        ]);
        $pastCompletedOrder->created_at = now()->subMonths(2);
        $pastCompletedOrder->save();
        Payment::create(['order_id' => $pastCompletedOrder->id, 'amount' => 70000, 'method' => 'cash', 'status' => 'completed']);

        $response = $this->actingAs($user)->get('/pesanan');
        $response->assertOk();

        // Verifikasi view data
        $active = $response->viewData('activeOrders');
        $completed = $response->viewData('completedOrders');
        $monthly = $response->viewData('monthlyArchives');

        $this->assertCount(1, $active);
        $this->assertEquals($activeOrder->id, $active->first()['raw_id']);
        $this->assertEquals('processing', $active->first()['status']);

        $this->assertCount(2, $completed);

        // Verifikasi arsip bulanan terbagi menjadi 2 folder bulan
        $this->assertCount(2, $monthly);

        // Cek label tab dan folder di HTML
        $response->assertSee('Sedang Diproses');
        $response->assertSee('Arsip Riwayat');
        $response->assertSee('Filter Bulan:');
        $response->assertSee('Buka Semua');
        $response->assertSee('Tutup Semua');
    }
}
