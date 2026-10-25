<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceType;

class ServiceTypeSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Cuci Kering Biasa', 'price' => 5000, 'description' => 'Cuci dan keringkan pakaian harian (per kg)'],
            ['name' => 'Cuci Setrika Kiloan', 'price' => 8000, 'description' => 'Cuci, kering, dan setrika per kilo'],
            ['name' => 'Cuci Sepatu', 'price' => 25000, 'description' => 'Cuci sepatu sneaker/kanvas (per pasang)'],
            ['name' => 'Cuci Gorden', 'price' => 15000, 'description' => 'Cuci gorden per meter (meter)'],
            ['name' => 'Setrika Saja', 'price' => 4000, 'description' => 'Hanya setrika pakaian harian (per kg)'],
        ];

        foreach ($services as $svc) {
            ServiceType::create($svc);
        }
    }
}
