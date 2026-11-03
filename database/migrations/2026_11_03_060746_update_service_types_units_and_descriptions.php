<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::table('service_types')
            ->where('name', 'Cuci Kering Biasa')
            ->update(['description' => 'Cuci dan keringkan pakaian harian (kg)']);

        \Illuminate\Support\Facades\DB::table('service_types')
            ->where('name', 'Cuci Gorden')
            ->update(['description' => 'Cuci gorden rumahan (pcs)']);

        \Illuminate\Support\Facades\DB::table('service_types')
            ->where('name', 'Setrika Saja')
            ->update(['description' => 'Hanya setrika pakaian harian (kg)']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::table('service_types')
            ->where('name', 'Cuci Kering Biasa')
            ->update(['description' => 'Cuci dan keringkan pakaian harian (pcs)']);

        \Illuminate\Support\Facades\DB::table('service_types')
            ->where('name', 'Cuci Gorden')
            ->update(['description' => 'Cuci gorden per meter (meter)']);

        \Illuminate\Support\Facades\DB::table('service_types')
            ->where('name', 'Setrika Saja')
            ->update(['description' => 'Hanya setrika pakaian harian (pcs)']);
    }
};
