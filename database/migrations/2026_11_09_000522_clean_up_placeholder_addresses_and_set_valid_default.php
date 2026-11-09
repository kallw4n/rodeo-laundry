<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Hapus alamat palsu/placeholder dari tabel addresses
        DB::table('addresses')
            ->where('address', 'like', '%Silakan tambah alamat%')
            ->orWhere('address', 'like', '%tambah alamat di menu profil%')
            ->delete();

        // 2. Bersihkan kolom address pada tabel customers jika berisi placeholder
        DB::table('customers')
            ->where('address', 'like', '%Silakan tambah alamat%')
            ->orWhere('address', 'like', '%tambah alamat di menu profil%')
            ->update(['address' => null]);

        // 3. Untuk setiap user, jika tidak ada alamat yang bertanda is_default = true tetapi memiliki alamat lain, set alamat pertamanya sebagai default
        $userIds = DB::table('addresses')->distinct()->pluck('user_id');
        foreach ($userIds as $userId) {
            $hasDefault = DB::table('addresses')
                ->where('user_id', $userId)
                ->where('is_default', true)
                ->exists();

            if (!$hasDefault) {
                $firstAddress = DB::table('addresses')
                    ->where('user_id', $userId)
                    ->orderBy('id', 'asc')
                    ->first();

                if ($firstAddress) {
                    DB::table('addresses')
                        ->where('id', $firstAddress->id)
                        ->update(['is_default' => true]);

                    // Sinkronkan ke tabel customer
                    DB::table('customers')
                        ->where('user_id', $userId)
                        ->update([
                            'address' => $firstAddress->address,
                            'city'    => $firstAddress->city,
                        ]);
                }
            } else {
                // Sinkronkan alamat default ke customer jika customer.address masih kosong
                $defaultAddress = DB::table('addresses')
                    ->where('user_id', $userId)
                    ->where('is_default', true)
                    ->first();

                if ($defaultAddress) {
                    DB::table('customers')
                        ->where('user_id', $userId)
                        ->where(function ($q) {
                            $q->whereNull('address')->orWhere('address', '');
                        })
                        ->update([
                            'address' => $defaultAddress->address,
                            'city'    => $defaultAddress->city,
                        ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
