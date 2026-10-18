<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        User::create([
            'username' => 'admin',
            'email' => 'admin@rodeo.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 5 Akun Customer tetap untuk kemudahan login testing & demo
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'username' => 'customer' . $i,
                'email' => 'customer' . $i . '@rodeo.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
            ]);
        }
    }
}
