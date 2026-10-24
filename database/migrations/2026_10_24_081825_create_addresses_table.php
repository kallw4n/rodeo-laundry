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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relasi ke user login
            $table->string('label'); // Contoh: Rumah, Kantor, Kos
            $table->string('recipient_name'); // Nama penerima
            $table->string('phone'); // No HP
            $table->text('address'); // Alamat lengkap
            $table->string('city'); // Kota/Kecamatan
            $table->boolean('is_default')->default(false); // Status alamat utama
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
