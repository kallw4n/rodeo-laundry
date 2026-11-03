<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryStatusSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_updating_delivery_to_on_way_updates_order_status_and_customer_view(): void
    {
        $customerUser = User::factory()->create(['username' => 'Budi']);
        $customer = Customer::create([
            'user_id' => $customerUser->id,
            'name'    => 'Budi',
            'email'   => $customerUser->email,
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'total_price' => 22000,
            'status'      => 'processing',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'amount'   => 22000,
            'method'   => 'cash',
            'status'   => 'completed',
        ]);

        $delivery = Delivery::create([
            'order_id'       => $order->id,
            'type'           => 'delivery',
            'address'        => 'Jl. Soekarno Hatta No. 12',
            'status'         => 'scheduled',
            'scheduled_date' => now(),
        ]);

        // Customer initially sees Sedang Dicuci (processing)
        $response1 = $this->actingAs($customerUser)->get('/pesanan');
        $response1->assertOk();
        $orders1 = $response1->viewData('orders');
        $this->assertEquals('processing', $orders1->first()['status']);
        $this->assertEquals('Sedang Dicuci', $orders1->first()['statusLabel']);

        // Admin marks delivery as 'on-way' via /admin/deliveries/{id}/status
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->patch("/admin/deliveries/{$delivery->id}/status", [
            'status' => 'on-way',
        ])->assertRedirect('/admin/deliveries');

        // Check DB synchronization
        $delivery->refresh();
        $order->refresh();
        $this->assertEquals('on-way', $delivery->status);
        $this->assertEquals('ready', $order->status);

        // Customer page now displays Sedang Diantar Kurir
        $response2 = $this->actingAs($customerUser)->get('/pesanan');
        $response2->assertOk();
        $orders2 = $response2->viewData('orders');
        $this->assertEquals('ready', $orders2->first()['status']);
        $this->assertEquals('Sedang Diantar Kurir', $orders2->first()['statusLabel']);
    }

    public function test_admin_dashboard_order_status_syncs_with_delivery(): void
    {
        $customerUser = User::factory()->create(['username' => 'Dewi']);
        $customer = Customer::create([
            'user_id' => $customerUser->id,
            'name'    => 'Dewi',
            'email'   => $customerUser->email,
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'total_price' => 30000,
            'status'      => 'processing',
        ]);

        $delivery = Delivery::create([
            'order_id'       => $order->id,
            'type'           => 'delivery',
            'address'        => 'Jl. Merbabu No. 3',
            'status'         => 'scheduled',
            'scheduled_date' => now(),
        ]);

        $admin = User::factory()->admin()->create();

        // Admin advances order to ready on dashboard
        $this->actingAs($admin)->patch("/admin/orders/{$order->id}/status", [
            'status' => 'ready',
        ]);

        $order->refresh();
        $delivery->refresh();
        $this->assertEquals('ready', $order->status);
        $this->assertEquals('on-way', $delivery->status);

        // Customer sees Sedang Diantar Kurir
        $response = $this->actingAs($customerUser)->get('/pesanan');
        $orders = $response->viewData('orders');
        $this->assertEquals('ready', $orders->first()['status']);
        $this->assertEquals('Sedang Diantar Kurir', $orders->first()['statusLabel']);
    }

    public function test_pickup_order_displays_siap_diambil_label(): void
    {
        $customerUser = User::factory()->create(['username' => 'Rian']);
        $customer = Customer::create([
            'user_id' => $customerUser->id,
            'name'    => 'Rian',
            'email'   => $customerUser->email,
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'total_price' => 15000,
            'status'      => 'ready',
        ]);

        Delivery::create([
            'order_id'       => $order->id,
            'type'           => 'pickup',
            'address'        => 'Ambil di Outlet Rodeo Malang',
            'status'         => 'scheduled',
            'scheduled_date' => now(),
        ]);

        $response = $this->actingAs($customerUser)->get('/pesanan');
        $orders = $response->viewData('orders');
        $this->assertEquals('ready', $orders->first()['status']);
        $this->assertEquals('Siap Diambil di Outlet', $orders->first()['statusLabel']);
        $this->assertTrue($orders->first()['isSelf']);
    }
}
