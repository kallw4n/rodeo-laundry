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

class AdminPortalTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customerUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create([
            'email' => 'admin@rodeo.com',
        ]);

        $this->customerUser = User::factory()->create([
            'role' => 'customer',
        ]);
    }

    public function test_guest_is_redirected_from_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->customerUser)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Pesanan Terbaru');
    }

    public function test_admin_can_access_pos(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/pos');
        $response->assertStatus(200);
        $response->assertSee('Point of Sale (POS) / Kasir');
    }

    public function test_admin_can_store_order_via_pos(): void
    {
        $customer = Customer::create([
            'name'    => 'Dimas Pratama',
            'phone'   => '08123456789',
            'city'    => 'Kota Malang',
            'address' => 'Jl. Borobudur No. 42',
        ]);

        $service = ServiceType::create([
            'name'        => 'Cuci Kering Lipat',
            'price'       => 6000,
            'description' => 'Khusus pakaian santai',
        ]);

        $response = $this->actingAs($this->admin)->post('/admin/pos/order', [
            'customer_id'    => $customer->id,
            'payment_method' => 'qris',
            'cash_received'  => 70000,
            'items'          => [
                [
                    'id'  => $service->id,
                    'qty' => 5,
                ],
            ],
        ]);

        $response->assertRedirect('/admin/pos');
        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'total_price' => 30000,
            'status'      => 'processing',
        ]);
        $this->assertDatabaseHas('payments', [
            'amount' => 30000,
            'method' => 'e-wallet',
            'status' => 'completed',
        ]);
    }

    public function test_admin_can_manage_customers(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/customers', [
            'name'    => 'Bambang Setyawan',
            'email'   => 'bambang@gmail.com',
            'phone'   => '087811239081',
            'city'    => 'Kota Malang',
            'address' => 'Jl. Ijen No. 10',
        ]);

        $response->assertRedirect('/admin/customers');
        $this->assertDatabaseHas('customers', [
            'name'  => 'Bambang Setyawan',
            'email' => 'bambang@gmail.com',
        ]);
    }

    public function test_admin_can_manage_services(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/services', [
            'name'        => 'Cuci Sepatu Sneakers',
            'price'       => 25000,
            'description' => 'Deep clean insole dan outsole',
        ]);

        $response->assertRedirect('/admin/services');
        $this->assertDatabaseHas('service_types', [
            'name'  => 'Cuci Sepatu Sneakers',
            'price' => 25000,
        ]);
    }

    public function test_admin_can_dispatch_delivery(): void
    {
        $customer = Customer::create([
            'name'    => 'Rahmat Hidayatullah',
            'phone'   => '085799124418',
            'city'    => 'Kota Malang',
            'address' => 'Permata Jingga Blok CC-09',
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'total_price' => 50000,
            'status'      => 'ready',
        ]);

        $delivery = Delivery::create([
            'order_id' => $order->id,
            'type'     => 'delivery',
            'address'  => 'Permata Jingga Blok CC-09',
            'status'   => 'scheduled',
        ]);

        $response = $this->actingAs($this->admin)->patch("/admin/deliveries/{$delivery->id}/status", [
            'status' => 'on-way',
        ]);

        $response->assertRedirect('/admin/deliveries');
        $this->assertDatabaseHas('deliveries', [
            'id'     => $delivery->id,
            'status' => 'on-way',
        ]);
    }

    public function test_admin_can_access_payments(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/payments');
        $response->assertStatus(200);
        $response->assertSee('Kelola Pembayaran');
    }

    public function test_admin_can_store_payment(): void
    {
        $customer = Customer::create([
            'name'    => 'Zahra',
            'phone'   => '081233445566',
            'city'    => 'Kota Malang',
            'address' => 'Jl. Soekarno Hatta',
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'total_price' => 30000,
            'status'      => 'ready',
        ]);

        $response = $this->actingAs($this->admin)->post('/admin/payments', [
            'order_id' => $order->id,
            'amount'   => 30000,
            'method'   => 'e-wallet',
            'status'   => 'completed',
        ]);

        $response->assertRedirect('/admin/payments');
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'amount'   => 30000,
            'method'   => 'e-wallet',
            'status'   => 'completed',
        ]);
    }
}

