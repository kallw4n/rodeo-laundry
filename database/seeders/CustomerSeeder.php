<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\User;
use Faker\Factory as Faker;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $customerNames = [
            'Budi Santoso',
            'Siti Rahmawati',
            'Ahmad Fauzi',
            'Dewi Lestari',
            'Rizky Pratama',
        ];

        $users = User::where('role', 'customer')->orderBy('id')->get();
        
        foreach ($users as $index => $user) {
            Customer::create([
                'user_id' => $user->id,
                'name' => $customerNames[$index] ?? $faker->name,
                'email' => $user->email,
                'phone' => '08' . $faker->numerify('##########'),
                'address' => 'Jl. ' . $faker->streetName . ' No. ' . $faker->buildingNumber . ', Malang',
                'city' => 'Kota Malang',
                'loyalty_points' => $faker->numberBetween(10, 80),
                'total_spent' => 0,
            ]);
        }

        // 5 pelanggan walk-in / offline tanpa akun user (transaksi kasir POS)
        for ($i = 0; $i < 5; $i++) {
            Customer::create([
                'user_id' => null,
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone' => '08' . $faker->numerify('##########'),
                'address' => 'Jl. ' . $faker->streetName . ' No. ' . $faker->buildingNumber . ', Malang',
                'city' => 'Kota Malang',
                'loyalty_points' => $faker->numberBetween(0, 30),
                'total_spent' => 0,
            ]);
        }
    }
}
