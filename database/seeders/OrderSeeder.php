<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\ServiceType;
use Faker\Factory as Faker;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $customers = Customer::all();
        $services = ServiceType::all();

        foreach ($customers as $customer) {
            // Create 1-3 orders per customer
            $numOrders = $faker->numberBetween(1, 3);
            for ($i = 0; $i < $numOrders; $i++) {
                $order = Order::create([
                    'customer_id' => $customer->id,
                    'total_price' => 0, // will be updated
                    'status' => $faker->randomElement(['pending', 'processing', 'ready', 'completed', 'cancelled']),
                    'estimated_completion_time' => $faker->dateTimeBetween('now', '+3 days'),
                ]);

                $total = 0;
                // Add 1-4 items per order
                $numItems = $faker->numberBetween(1, 4);
                $randomServices = $services->random($numItems);
                
                foreach ($randomServices as $service) {
                    $qty = $faker->numberBetween(1, 5);
                    $price = $service->price;
                    OrderItem::create([
                        'order_id' => $order->id,
                        'service_id' => $service->id,
                        'quantity' => $qty,
                        'price' => $price,
                    ]);
                    $total += ($qty * $price);
                }

                $order->update(['total_price' => $total]);
                
                // Update customer total_spent if order is completed
                if ($order->status === 'completed') {
                    $customer->total_spent += $total;
                    $customer->save();
                }
            }
        }
    }
}
