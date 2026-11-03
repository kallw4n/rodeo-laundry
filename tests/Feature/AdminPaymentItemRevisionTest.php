<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPaymentItemRevisionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_revise_item_weights_and_customer_views_updated_weights_and_prices(): void
    {
        // 1. Setup services
        $svc1 = ServiceType::create(['name' => 'Cuci Setrika Kiloan', 'price' => 8000, 'description' => 'x']);
        $svc2 = ServiceType::create(['name' => 'Cuci Kering Biasa', 'price' => 5000, 'description' => 'x']);
        $svc3 = ServiceType::create(['name' => 'Setrika Saja', 'price' => 4000, 'description' => 'x']);

        // 2. Customer creates order with initial 1 kg estimates
        $customerUser = User::factory()->create(['username' => 'Haikal']);
        $customer = Customer::create([
            'user_id' => $customerUser->id,
            'name'    => 'Haikal',
            'email'   => $customerUser->email,
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'total_price' => 17000,
            'status'      => 'pending',
        ]);

        $item1 = $order->items()->create(['service_id' => $svc1->id, 'quantity' => 1, 'price' => 8000]);
        $item2 = $order->items()->create(['service_id' => $svc2->id, 'quantity' => 1, 'price' => 5000]);
        $item3 = $order->items()->create(['service_id' => $svc3->id, 'quantity' => 1, 'price' => 4000]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'amount'   => 17000,
            'method'   => 'cash',
            'status'   => 'pending',
        ]);

        // Customer sees 1kg and 17000 initially in orders data
        $responseBefore = $this->actingAs($customerUser)->get('/pesanan');
        $responseBefore->assertOk();
        $ordersBefore = $responseBefore->viewData('orders');
        $this->assertEquals(17000, $ordersBefore->first()['total']);
        $this->assertEquals(1.0, (float) $ordersBefore->first()['items'][0]['qty']);

        // 3. Admin weighs the laundry at outlet:
        // item 1 = 1.5 kg (Rp 12.000)
        // item 2 = 1.3 kg (Rp 6.500)
        // item 3 = 0.9 kg (Rp 3.600)
        // Total = 12000 + 6500 + 3600 = 22100
        $admin = User::factory()->admin()->create();

        $updateResponse = $this->actingAs($admin)->put("/admin/payments/{$payment->id}", [
            'amount' => 22100,
            'method' => 'e-wallet',
            'status' => 'completed',
            'items'  => [
                ['id' => $item1->id, 'quantity' => 1.5],
                ['id' => $item2->id, 'quantity' => 1.3],
                ['id' => $item3->id, 'quantity' => 0.9],
            ],
            'submit_action' => 'save',
        ]);

        $updateResponse->assertRedirect('/admin/payments');

        // 4. Verify Database
        $this->assertEquals(1.5, (float) $item1->fresh()->quantity);
        $this->assertEquals(1.3, (float) $item2->fresh()->quantity);
        $this->assertEquals(0.9, (float) $item3->fresh()->quantity);

        $order->refresh();
        $this->assertEquals(22100, $order->total_price);
        $this->assertEquals('processing', $order->status);

        $payment->refresh();
        $this->assertEquals(22100, $payment->amount);
        $this->assertEquals('e-wallet', $payment->method);
        $this->assertEquals('completed', $payment->status);

        // 5. Customer refreshes /pesanan and sees the revised weights & totals
        $responseAfter = $this->actingAs($customerUser)->get('/pesanan');
        $responseAfter->assertOk();
        $ordersAfter = $responseAfter->viewData('orders');
        $this->assertEquals(22100, $ordersAfter->first()['total']);
        $this->assertEquals('processing', $ordersAfter->first()['status']);
        $this->assertEquals('completed', $ordersAfter->first()['payment_status']);
        $this->assertEquals(1.5, (float) $ordersAfter->first()['items'][0]['qty']);
        $this->assertEquals(12000, $ordersAfter->first()['items'][0]['price']);
        $this->assertEquals(1.3, (float) $ordersAfter->first()['items'][1]['qty']);
        $this->assertEquals(6500, $ordersAfter->first()['items'][1]['price']);
        $this->assertEquals(0.9, (float) $ordersAfter->first()['items'][2]['qty']);
        $this->assertEquals(3600, $ordersAfter->first()['items'][2]['price']);

        $responseAfter->assertSee('Lunas');
        $responseAfter->assertSee('Sedang Dicuci');
    }
}
