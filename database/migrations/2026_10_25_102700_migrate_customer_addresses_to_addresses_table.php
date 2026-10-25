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
        $customers = DB::table('customers')
            ->whereNotNull('user_id')
            ->whereNotNull('address')
            ->where('address', '!=', '')
            ->get();

        foreach ($customers as $customer) {
            $alreadyHasAddress = DB::table('addresses')
                ->where('user_id', $customer->user_id)
                ->exists();

            if (!$alreadyHasAddress) {
                DB::table('addresses')->insert([
                    'user_id'        => $customer->user_id,
                    'label'          => 'Rumah',
                    'recipient_name' => $customer->name,
                    'phone'          => $customer->phone ?? '',
                    'address'        => $customer->address,
                    'city'           => $customer->city ?? 'Kota Malang',
                    'is_default'     => true,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data migration does not need to drop address records
    }
};
