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
        $svc = ServiceType::create(['name' => 'Cuci Kiloan', 'price' => 8000, 'description' => 'x']);

        $this->actingAs($user)->postJson('/pesan', [
            'items' => [['id' => $svc->id, 'qty' => 3]],
            'payment_method' => 'qris',
            'pickup' => 'courier',
            'address' => 'Jl. Ijen No. 10, Klojen, Malang',
            'notes' => 'Titip satpam',
        ])->assertOk()->assertJsonStructure(['redirect']);

        $order = Order::with(['payment', 'delivery', 'customer'])->first();
        $this->assertEquals(24000, $order->total_price);
        $this->assertEquals($user->id, $order->customer->user_id);
        $this->assertEquals('e-wallet', $order->payment->method);
        $this->assertEquals('pending', $order->payment->status);
        $this->assertEquals('delivery', $order->delivery->type);
        $this->assertStringContainsString('Jl. Ijen No. 10', $order->delivery->address);
    }

    public function test_guest_cannot_checkout(): void
    {
        $this->postJson('/pesan', [])->assertUnauthorized();
    }
}
