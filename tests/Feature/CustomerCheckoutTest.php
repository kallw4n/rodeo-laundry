<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_checkout_creates_order_payment_and_delivery(): void
    {
        $user = User::factory()->create();
        $svc = ServiceType::create(['name' => 'Cuci Bedcover Satuan', 'price' => 25000, 'description' => 'x']);

        $this->actingAs($user)->postJson('/pesan', [
            'items' => [['id' => $svc->id, 'qty' => 3]],
            'payment_method' => 'qris',
            'pickup' => 'courier',
            'address' => 'Jl. Ijen No. 10, Klojen, Malang',
            'notes' => 'Titip satpam',
        ])->assertOk()->assertJsonStructure(['redirect']);

        $order = Order::with(['payment', 'delivery', 'customer'])->first();
        $this->assertEquals(75000, $order->total_price);
        $this->assertEquals($user->id, $order->customer->user_id);
        $this->assertEquals('e-wallet', $order->payment->method);
        $this->assertEquals('pending', $order->payment->status);
        $this->assertEquals('delivery', $order->delivery->type);
        $this->assertStringContainsString('Jl. Ijen No. 10', $order->delivery->address);
    }

    public function test_customer_orders_page_displays_payment_status_and_advances_on_admin_approval(): void
    {
        $user = User::factory()->create();
        $svc = ServiceType::create(['name' => 'Cuci Bedcover Satuan', 'price' => 25000, 'description' => 'x']);

        $this->actingAs($user)->postJson('/pesan', [
            'items' => [['id' => $svc->id, 'qty' => 2]],
            'payment_method' => 'qris',
            'pickup' => 'self',
        ])->assertOk();

        $order = Order::with('payment')->first();
        $this->assertEquals('pending', $order->status);
        $this->assertEquals('pending', $order->payment->status);

        // Cek halaman pesanan pelanggan awal: harus terlihat Menunggu Pembayaran
        $response = $this->actingAs($user)->get('/pesanan');
        $response->assertOk();
        $response->assertSee('Menunggu Pembayaran');

        // Admin menyetujui pembayaran menjadi 'completed' (Lunas)
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->put("/admin/payments/{$order->payment->id}", [
            'amount' => 50000,
            'method' => 'e-wallet',
            'status' => 'completed',
        ])->assertRedirect('/admin/payments');

        // Cek database: payment jadi completed dan order otomatis maju ke 'processing' (Sedang Dicuci)
        $order->refresh();
        $this->assertEquals('completed', $order->payment->status);
        $this->assertEquals('processing', $order->status);

        // Pelanggan buka kembali halaman pesanannya: harus terlihat 'Lunas' dan 'Sedang Dicuci'
        $responseAfter = $this->actingAs($user)->get('/pesanan');
        $responseAfter->assertOk();
        $responseAfter->assertSee('Lunas');
        $responseAfter->assertSee('Sedang Dicuci');
    }

    public function test_guest_cannot_checkout(): void
    {
        $this->postJson('/pesan', [])->assertUnauthorized();
    }

    public function test_customer_can_checkout_with_cod_and_view_pasca_timbang_labels(): void
    {
        $user = User::factory()->create();
        $svc = ServiceType::create(['name' => 'Cuci Setrika Kiloan', 'price' => 8000, 'description' => 'x']);

        // 1. Kurir jemput & COD
        $this->actingAs($user)->postJson('/pesan', [
            'items' => [['id' => $svc->id, 'qty' => 4]],
            'payment_method' => 'cod',
            'pickup' => 'courier',
            'address' => 'Jl. Bunga Melati No. 5',
        ])->assertOk();

        // 2. Antar sendiri & bayar di outlet
        $this->actingAs($user)->postJson('/pesan', [
            'items' => [['id' => $svc->id, 'qty' => 2]],
            'payment_method' => 'cod',
            'pickup' => 'self',
        ])->assertOk();

        $response = $this->actingAs($user)->get('/pesanan');
        $response->assertOk();
        $response->assertSee('Jemput Dulu', false);
        $response->assertSee('Bayar di Outlet', false);
    }

    public function test_kiloan_order_cannot_use_upfront_payment_methods(): void
    {
        $user = User::factory()->create();
        $svc = ServiceType::create(['name' => 'Cuci Kiloan Reguler', 'price' => 5000, 'description' => 'x']);

        $response = $this->actingAs($user)->postJson('/pesan', [
            'items' => [['id' => $svc->id, 'qty' => 3]],
            'payment_method' => 'qris',
            'pickup' => 'courier',
            'address' => 'Jl. Veteran No. 1',
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment([
                'message' => 'Layanan kiloan wajib menggunakan metode Bayar Nanti (COD / Pasca-Timbang) karena berat pakaian harus dipastikan melalui timbangan resmi outlet.',
            ]);
    }

    public function test_kiloan_order_supports_decimal_weight_and_calculates_correct_price(): void
    {
        $user = User::factory()->create();
        $svc = ServiceType::create(['name' => 'Cuci Kiloan Reguler', 'price' => 5000, 'description' => 'x']);

        // Pelanggan memesan 2.7 kg
        $response = $this->actingAs($user)->postJson('/pesan', [
            'items' => [['id' => $svc->id, 'qty' => 2.7]],
            'payment_method' => 'cod',
            'pickup' => 'courier',
            'address' => 'Jl. Soekarno Hatta No. 9',
        ]);

        $response->assertOk();

        $order = Order::with('items')->first();
        $this->assertNotNull($order);
        $this->assertEquals(1, $order->items->count());
        $this->assertEquals(2.7, (float) $order->items->first()->quantity);
        // 5000 * 2.7 = 13500
        $this->assertEquals(13500, $order->total_price);
    }
}
