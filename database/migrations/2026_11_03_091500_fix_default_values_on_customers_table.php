<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('customers')) {
            $driver = DB::getDriverName();
            if ($driver === 'pgsql') {
                DB::statement('ALTER TABLE customers ALTER COLUMN total_spent SET DEFAULT 0.00;');
                DB::statement('ALTER TABLE customers ALTER COLUMN loyalty_points SET DEFAULT 0;');
            } else {
                Schema::table('customers', function (Blueprint $table) {
                    $table->decimal('total_spent', 15, 2)->default(0.00)->change();
                    $table->integer('loyalty_points')->default(0)->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down needed
    }
};
