<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Order;
use Faker\Factory as Faker;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $orders = Order::all();

        foreach ($orders as $order) {
            // Only some orders get paid
            if ($order->status === 'completed' || $order->status === 'ready' || $faker->boolean(70)) {
                Payment::create([
                    'order_id' => $order->id,
                    'amount' => $order->total_price,
                    'method' => $faker->randomElement(['cash', 'transfer', 'e-wallet', 'credit_card']),
                    'status' => 'completed',
                ]);
            }
        }
    }
}
