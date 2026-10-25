<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name'  => 'Test Customer',
                'phone' => '081234567890',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('customers', [
            'user_id' => $user->id,
            'name'    => 'Test Customer',
            'phone'   => '081234567890',
        ]);
    }

    public function test_user_can_add_address(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/addresses', [
                'label'          => 'Rumah',
                'recipient_name' => 'Budi Santoso',
                'phone'          => '081234567890',
                'city'           => 'Lowokwaru',
                'address'        => 'Jl. Soekarno Hatta No. 12',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('addresses', [
            'user_id'        => $user->id,
            'label'          => 'Rumah',
            'recipient_name' => 'Budi Santoso',
        ]);
    }

    public function test_user_can_set_default_address(): void
    {
        $user = User::factory()->create();
        $addr1 = Address::create([
            'user_id'        => $user->id,
            'label'          => 'Rumah',
            'recipient_name' => 'Budi',
            'phone'          => '08123456789',
            'city'           => 'Malang',
            'address'        => 'Alamat 1',
            'is_default'     => true,
        ]);
        $addr2 = Address::create([
            'user_id'        => $user->id,
            'label'          => 'Kantor',
            'recipient_name' => 'Budi',
            'phone'          => '08123456789',
            'city'           => 'Malang',
            'address'        => 'Alamat 2',
            'is_default'     => false,
        ]);

        $response = $this
            ->actingAs($user)
            ->patch("/addresses/{$addr2->id}/set-default");

        $response->assertRedirect();
        $this->assertTrue($addr2->fresh()->is_default);
        $this->assertFalse($addr1->fresh()->is_default);
    }

    public function test_user_can_delete_address(): void
    {
        $user = User::factory()->create();
        $address = Address::create([
            'user_id'        => $user->id,
            'label'          => 'Kos',
            'recipient_name' => 'Budi',
            'phone'          => '08123456789',
            'city'           => 'Malang',
            'address'        => 'Jl. Veteran No. 5',
            'is_default'     => false,
        ]);

        $response = $this
            ->actingAs($user)
            ->delete("/addresses/{$address->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('addresses', [
            'id' => $address->id,
        ]);
    }
}
