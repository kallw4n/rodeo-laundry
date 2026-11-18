<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_login_notice_only_shows_on_auth_redirect_and_not_when_returning_from_register(): void
    {
        // 1. Akses rute terproteksi saat guest memicu notif sekali saja di /login
        $redirected = $this->get('/pemesanan');
        $redirected->assertRedirect('/login');
        $this->get('/login')->assertOk()->assertSee('Silakan login terlebih dahulu ya!');

        // 2. Pindah ke /register lalu kembali ke /login tidak memunculkan notif meski url.intended masih ada
        $this->get('/register')->assertOk()->assertSee(route('login', ['ref' => 'register']), false);
        $this->get(route('login', ['ref' => 'register']))
            ->assertOk()
            ->assertDontSee('Silakan login terlebih dahulu ya!');

        // 3. Kunjungan langsung ke /login tanpa redirect auth juga tidak memunculkan notif
        $this->get('/login')->assertOk()->assertDontSee('Silakan login terlebih dahulu ya!');
    }
}
