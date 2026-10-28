<?php

namespace Tests\Feature;

use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerFrontendPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        ServiceType::create([
            'name' => 'Cuci Kiloan',
            'category' => 'kiloan',
            'price' => 8000,
            'unit' => 'kg',
            'description' => 'Cuci setrika reguler wangi higienis'
        ]);

        ServiceType::create([
            'name' => 'Cuci Sepatu Premium',
            'category' => 'sepatu',
            'price' => 25000,
            'unit' => 'pasang',
            'description' => 'Deep clean sepatu & treatment anti-bakteri'
        ]);
    }

    public function test_landing_page_renders_successfully(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('Rodeo Laundry');
        $response->assertSee('Pesan Jasa Laundry Sekarang');
        $response->assertSee('WhatsApp');
    }

    public function test_auth_login_and_register_pages_render_successfully(): void
    {
        $loginRes = $this->get('/login');
        $loginRes->assertOk();
        $loginRes->assertSee('Masuk ke Akun');
        $loginRes->assertSee('Google Review');
        $loginRes->assertSee('Pen B');

        $regRes = $this->get('/register');
        $regRes->assertOk();
        $regRes->assertSee('Buat Akun Baru');
        $regRes->assertSee('Gratis Ongkir');
    }

    public function test_customer_portal_pages_require_auth(): void
    {
        $this->get('/pemesanan')->assertRedirect('/login');
        $this->get('/pengiriman')->assertRedirect('/login');
        $this->get('/pembayaran')->assertRedirect('/login');
    }

    public function test_customer_portal_pages_render_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $pesanRes = $this->actingAs($user)->get('/pemesanan');
        $pesanRes->assertOk();
        $pesanRes->assertSee('Pilih Layanan');
        $pesanRes->assertSee('Cuci Kiloan');

        $kirimRes = $this->actingAs($user)->get('/pengiriman');
        $kirimRes->assertOk();
        $kirimRes->assertSee('Pengiriman');
        $kirimRes->assertSee('Alamat Penjemputan');

        $bayarRes = $this->actingAs($user)->get('/pembayaran');
        $bayarRes->assertOk();
        $bayarRes->assertSee('Pembayaran');
        $bayarRes->assertSee('Bayar via QRIS');
        $bayarRes->assertSee('fa-solid fa-check');
        $bayarRes->assertSee("Antar Sendiri & Ambil Sendiri", false);
    }
}
