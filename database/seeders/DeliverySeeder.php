<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Delivery;
use App\Models\Order;
use Faker\Factory as Faker;

class DeliverySeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $orders = Order::all();

        foreach ($orders as $order) {
            if ($faker->boolean(40)) { // 40% chance to have delivery/pickup
                Delivery::create([
                    'order_id' => $order->id,
                    'type' => $faker->randomElement(['pickup', 'delivery']),
                    'address' => $order->customer->address ?? $faker->address,
                    'scheduled_date' => $faker->dateTimeBetween('now', '+2 days'),
                    'status' => $faker->randomElement(['scheduled', 'on-way', 'completed']),
                ]);
            }
        }
    }
}
