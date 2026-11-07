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

    public function test_dashboard_displays_zero_when_no_revenue_today_without_falling_back_to_old_data(): void
    {
        $customer = Customer::create([
            'name' => 'Budi Santoso',
            'phone' => '08123456789',
            'city' => 'Kota Batu',
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'total_price' => 1500000,
            'status' => 'completed',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => 1500000,
            'method' => 'cash',
            'status' => 'completed',
        ]);

        \Illuminate\Support\Facades\DB::table('orders')->where('id', $order->id)->update([
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(10),
        ]);
        \Illuminate\Support\Facades\DB::table('payments')->where('id', $payment->id)->update([
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(10),
        ]);

        // Access dashboard today without date filter
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('todayRevenue', 0.0);
    }

    public function test_dashboard_displays_realtime_revenue_when_paid_today(): void
    {
        $customer = Customer::create([
            'name' => 'Citra Dewi',
            'phone' => '08129876543',
            'city' => 'Kota Batu',
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'total_price' => 85000,
            'status' => 'processing',
            'created_at' => now(),
        ]);

        Payment::create([
            'order_id' => $order->id,
            'amount' => 85000,
            'method' => 'cash',
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('todayRevenue', 85000.0);
        $response->assertSee('85.000');
    }

    public function test_dashboard_calendar_filters_orders_by_selected_date_and_empties_when_none(): void
    {
        $customer = Customer::create([
            'name' => 'Fahri Tanggal',
            'phone' => '08129876541',
            'city' => 'Kota Malang',
        ]);

        $orderNov1 = Order::create([
            'customer_id' => $customer->id,
            'total_price' => 50000,
            'status' => 'processing',
        ]);
        $orderNov1->created_at = '2026-11-01 10:00:00';
        $orderNov1->save();

        $orderNov2 = Order::create([
            'customer_id' => $customer->id,
            'total_price' => 75000,
            'status' => 'ready',
        ]);
        $orderNov2->created_at = '2026-11-02 11:00:00';
        $orderNov2->save();

        // 1. Filter tanggal 1 Nov 2026: Hanya orderNov1 yang muncul
        $resNov1 = $this->actingAs($this->admin)->get('/admin/dashboard?date=2026-11-01');
        $resNov1->assertStatus(200);
        $ordersNov1 = $resNov1->viewData('recentOrders');
        $this->assertEquals(1, $ordersNov1->total());
        $this->assertEquals($orderNov1->id, $ordersNov1->first()->id);
        $this->assertTrue($ordersNov1->contains('id', $orderNov1->id));
        $this->assertFalse($ordersNov1->contains('id', $orderNov2->id));

        // 2. Filter tanggal 2 Nov 2026: Hanya orderNov2 yang muncul
        $resNov2 = $this->actingAs($this->admin)->get('/admin/dashboard?date=2026-11-02');
        $resNov2->assertStatus(200);
        $ordersNov2 = $resNov2->viewData('recentOrders');
        $this->assertEquals(1, $ordersNov2->total());
        $this->assertEquals($orderNov2->id, $ordersNov2->first()->id);
        $this->assertTrue($ordersNov2->contains('id', $orderNov2->id));
        $this->assertFalse($ordersNov2->contains('id', $orderNov1->id));

        // 3. Filter tanggal 5 Nov 2026: Kosong (tidak ada pesanan)
        $resNov5 = $this->actingAs($this->admin)->get('/admin/dashboard?date=2026-11-05');
        $resNov5->assertStatus(200);
        $ordersNov5 = $resNov5->viewData('recentOrders');
        $this->assertEquals(0, $ordersNov5->total());
        $resNov5->assertSee('Tidak ada pesanan pada tanggal ini');
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

    public function test_pos_payment_is_pending_when_cash_received_is_less_than_total(): void
    {
        $customer = Customer::create([
            'name'    => 'Ahmad Fauzi',
            'phone'   => '081234567890',
            'city'    => 'Kota Malang',
            'address' => 'Jl. Suhat',
        ]);

        $service = ServiceType::create([
            'name'        => 'Cuci Kering Biasa',
            'price'       => 50000,
            'description' => 'Test Layanan',
        ]);

        $response = $this->actingAs($this->admin)->post('/admin/pos/order', [
            'customer_id'    => $customer->id,
            'payment_method' => 'cash',
            'cash_received'  => 30000, // Kurang dari 50000!
            'items'          => [
                ['id' => $service->id, 'qty' => 1],
            ],
        ]);

        $response->assertRedirect('/admin/pos');
        $this->assertDatabaseHas('payments', [
            'amount' => 50000,
            'method' => 'cash',
            'status' => 'pending', // Harus pending, bukan completed!
        ]);
    }

    public function test_customer_creation_validates_alpha_name_and_numeric_phone(): void
    {
        // Name containing numbers should fail
        $response1 = $this->actingAs($this->admin)->post('/admin/customers', [
            'name'    => '1678',
            'phone'   => '08123456789',
            'city'    => 'Kota Malang',
            'address' => 'Jl. Ijen',
        ]);
        $response1->assertSessionHasErrors('name');

        // Phone containing letters should fail
        $response2 = $this->actingAs($this->admin)->post('/admin/customers', [
            'name'    => 'Budi Santoso',
            'phone'   => 'abcde123',
            'city'    => 'Kota Malang',
            'address' => 'Jl. Ijen',
        ]);
        $response2->assertSessionHasErrors('phone');
    }

    public function test_admin_can_view_order_receipt(): void
    {
        $customer = Customer::create([
            'name'    => 'Siti Rahmawati',
            'phone'   => '081234567891',
            'city'    => 'Kota Malang',
            'address' => 'Jl. Dinoyo',
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'total_price' => 45000,
            'status'      => 'processing',
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/orders/{$order->id}/receipt");
        $response->assertStatus(200);
        $response->assertSee('RODEO LAUNDRY');
        $response->assertSee('Siti Rahmawati');
        $response->assertSee('#RD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT));
    }

    public function test_dashboard_stat_cards_show_order_dalam_proses_and_order_selesai(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Order Dalam Proses');
        $response->assertSee('Order Selesai');
    }

    public function test_admin_can_view_print_report(): void
    {
        $customer = Customer::create([
            'name'    => 'Ahmad Dhani',
            'phone'   => '081299988877',
            'city'    => 'Kota Malang',
            'address' => 'Jl. Kalpataru',
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'total_price' => 75000,
            'status'      => 'completed',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/reports/print?period=today');
        $response->assertStatus(200);
        $response->assertSee('REKAPITULASI TRANSAKSI');
        $response->assertSee('Ahmad Dhani');
        $response->assertSee('75.000');
    }

    public function test_admin_can_export_report_csv(): void
    {
        $customer = Customer::create([
            'name'    => 'Rahmat Hidayat',
            'phone'   => '081233344455',
            'city'    => 'Kota Malang',
            'address' => 'Jl. Suhat',
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'total_price' => 50000,
            'status'      => 'ready',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/reports/export?period=week');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        
        $content = $response->streamedContent();
        $this->assertStringContainsString('LAPORAN REKAPITULASI TRANSAKSI', $content);
        $this->assertStringContainsString('Rahmat Hidayat', $content);
        $this->assertStringContainsString('#RD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT), $content);
    }

    public function test_admin_layout_renders_operational_notifications(): void
    {
        $customer = Customer::create([
            'name'    => 'Bayu Skak',
            'phone'   => '081299900011',
            'city'    => 'Kota Malang',
            'address' => 'Jl. Veteran',
        ]);

        Order::create([
            'customer_id' => $customer->id,
            'total_price' => 35000,
            'status'      => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Pemberitahuan');
        $response->assertSee('Pesanan Baru Masuk');
        $response->assertSee('Bayu Skak');
    }
}

