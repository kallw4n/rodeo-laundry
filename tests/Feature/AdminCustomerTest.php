<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCustomerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_view_customers_page(): void
    {
        Customer::create([
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'city' => 'Kota Malang',
            'address' => 'Jl. Ijen No. 12',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.customers.index'));

        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
    }

    public function test_admin_can_create_customer_successfully(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.customers.store'), [
            'name' => 'Dr. H. Bambang S., M.Kom',
            'email' => 'bambang@example.com',
            'phone' => '0812-9988-7766',
            'city' => 'Kota Malang',
            'address' => 'Perumahan Permata Blok B3',
        ]);

        $response->assertRedirect(route('admin.customers.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('customers', [
            'name' => 'Dr. H. Bambang S., M.Kom',
            'email' => 'bambang@example.com',
            'phone' => '081299887766',
            'city' => 'Kota Malang',
        ]);
    }

    public function test_admin_can_create_customer_with_international_or_prefix8_phone(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.customers.store'), [
            'name' => 'Siti Aminah',
            'phone' => '+6285712345678',
        ]);

        $response->assertRedirect(route('admin.customers.index'));

        $this->assertDatabaseHas('customers', [
            'name' => 'Siti Aminah',
            'phone' => '085712345678',
            'city' => 'Kota Malang',
        ]);
    }

    public function test_admin_can_update_customer(): void
    {
        $customer = Customer::create([
            'name' => 'Pelanggan Lama',
            'phone' => '0811111111',
            'city' => 'Kota Malang',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.customers.update', $customer), [
            'name' => 'Pelanggan Baru Diupdate',
            'phone' => '0822222222',
            'city' => 'Kota Batu',
            'address' => 'Jl. Oro-oro Ombo',
        ]);

        $response->assertRedirect(route('admin.customers.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Pelanggan Baru Diupdate',
            'phone' => '0822222222',
            'city' => 'Kota Batu',
        ]);
    }

    public function test_customer_creation_requires_name(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.customers.store'), [
            'name' => '',
            'phone' => '081234567890',
        ]);

        $response->assertSessionHasErrors(['name']);
    }
}
