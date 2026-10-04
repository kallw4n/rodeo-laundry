<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->decimal('total_price', 10, 2)->default(0);
            $table->enum('status', ['pending', 'washing', 'drying', 'ironing', 'packing', 'ready', 'completed', 'cancelled'])->default('pending');
            $table->dateTime('estimated_completion_time')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('orders'); }
};