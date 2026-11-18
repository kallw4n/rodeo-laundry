<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CancelledOrderPaymentSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_cancelling_order_marks_pending_payment_failed_and_separates_admin_payment_tabs(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);
        $svc = ServiceType::create(['name' => 'Cuci Sepatu', 'price' => 25000, 'description' => 'Cuci sepatu']);

        // Order #1: dibatalkan oleh customer
        $this->actingAs($customer)->postJson('/pesan', [
            'items' => [['id' => $svc->id, 'qty' => 1]],
            'payment_method' => 'va',
            'pickup' => 'self',
        ])->assertOk();

        $cancelledOrder = Order::first();
        $this->actingAs($customer)->patch('/orders/' . $cancelledOrder->id . '/cancel')->assertRedirect();

        $cancelledOrder->refresh();
        $this->assertEquals('cancelled', $cancelledOrder->status);
        $this->assertEquals('failed', $cancelledOrder->payment->status);
        $this->assertEquals('failed', $cancelledOrder->delivery->status);

        // Order #2: order aktif
        $this->actingAs($customer)->postJson('/pesan', [
            'items' => [['id' => $svc->id, 'qty' => 2]],
            'payment_method' => 'qris',
            'pickup' => 'self',
        ])->assertOk();

        // Tab default (Pembayaran Aktif) tidak menampilkan order batal & tidak melabelinya Belum Lunas
        $activePage = $this->actingAs($admin)->get('/admin/payments');
        $activePage->assertOk();
        $activePage->assertDontSee('#RD-' . str_pad($cancelledOrder->id, 4, '0', STR_PAD_LEFT));
        $activePage->assertSee('#RD-0002');
        $activePage->assertSee('Pesanan Dibatalkan');

        // Tab Pesanan Dibatalkan menampilkan order batal dengan badge "Pesanan Dibatalkan" (bukan "Belum Lunas")
        $cancelledTab = $this->actingAs($admin)->get('/admin/payments?tab=cancelled');
        $cancelledTab->assertOk();
        $cancelledTab->assertSee('#RD-' . str_pad($cancelledOrder->id, 4, '0', STR_PAD_LEFT));
        $cancelledTab->assertSee('Pesanan Dibatalkan');
        $cancelledTab->assertDontSee('Belum Lunas');
    }

    public function test_admin_cancelling_order_from_dashboard_updates_payment_status_to_failed(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);
        $svc = ServiceType::create(['name' => 'Cuci Gorden', 'price' => 15000, 'description' => 'Cuci gorden']);

        $this->actingAs($customer)->postJson('/pesan', [
            'items' => [['id' => $svc->id, 'qty' => 1]],
            'payment_method' => 'va',
            'pickup' => 'self',
        ])->assertOk();

        $order = Order::first();
        $this->actingAs($admin)->patch('/admin/orders/' . $order->id . '/status', [
            'status' => 'cancelled',
        ])->assertRedirect();

        $this->assertEquals('cancelled', $order->fresh()->status);
        $this->assertEquals('failed', $order->payment->fresh()->status);
    }
}
