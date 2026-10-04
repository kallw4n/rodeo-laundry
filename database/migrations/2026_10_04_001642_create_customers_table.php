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
    Schema::create('customers', function (Blueprint $table) {
        $table->id();
        // Foreign key ke tabel users (bisa null)
        $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
        $table->string('name', 100);
        $table->string('email', 100)->nullable();
        $table->string('phone', 20)->nullable();
        $table->text('address')->nullable();
        $table->string('city', 50)->nullable();
        $table->integer('loyalty_points')->default(0);
        $table->decimal('total_spent', 10, 2)->default(0.00);
        $table->timestamp('created_at')->useCurrent();
        $table->timestamp('updated_at')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
