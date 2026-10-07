<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CustomerSeeder::class,
            ServiceTypeSeeder::class,
            OrderSeeder::class,
            PaymentSeeder::class,
            DeliverySeeder::class,
        ]);
    }
}
