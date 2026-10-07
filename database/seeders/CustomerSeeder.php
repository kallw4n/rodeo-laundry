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
        $users = User::where('role', 'customer')->get();
        
        foreach ($users as $user) {
            Customer::create([
                'user_id' => $user->id,
                'name' => $faker->name,
                'email' => $user->email,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'city' => $faker->city,
                'loyalty_points' => $faker->numberBetween(0, 100),
                'total_spent' => 0,
            ]);
        }

        // Add some customers without user account
        for ($i = 0; $i < 5; $i++) {
            Customer::create([
                'user_id' => null,
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'city' => $faker->city,
                'loyalty_points' => $faker->numberBetween(0, 50),
                'total_spent' => 0,
            ]);
        }
    }
}
