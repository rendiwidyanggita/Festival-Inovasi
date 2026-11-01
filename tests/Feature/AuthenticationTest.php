<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function login_page_is_accessible(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertViewIs('auth.login');
    }

    #[Test]
    public function superadmin_can_login_and_redirect_to_admin_dashboard(): void
    {
        $superadmin = User::factory()->superadmin()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $response = $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($superadmin);
    }

    #[Test]
    public function jury_can_login_and_redirect_to_jury_dashboard(): void
    {
        $jury = User::factory()->jury()->create([
            'email' => 'jury@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $response = $this->post(route('login'), [
            'email' => 'jury@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('jury.dashboard'));
        $this->assertAuthenticatedAs($jury);
    }

    #[Test]
    public function login_fails_with_wrong_password(): void
    {
        User::factory()->superadmin()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    #[Test]
    public function inactive_user_cannot_login(): void
    {
        User::factory()->superadmin()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_active' => false,
        ]);

        $response = $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    #[Test]
    public function user_can_logout(): void
    {
        $user = User::factory()->superadmin()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    #[Test]
    public function guest_is_redirected_to_login_when_accessing_admin(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function guest_is_redirected_to_login_when_accessing_jury(): void
    {
        $response = $this->get(route('jury.dashboard'));

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function jury_cannot_access_admin_pages(): void
    {
        $jury = User::factory()->jury()->create();

        $response = $this->actingAs($jury)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    #[Test]
    public function superadmin_cannot_access_jury_pages(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $response = $this->actingAs($superadmin)->get(route('jury.dashboard'));

        $response->assertForbidden();
    }

    #[Test]
    public function login_requires_email_and_password(): void
    {
        $response = $this->post(route('login'), []);

        $response->assertSessionHasErrors(['email', 'password']);
    }

    #[Test]
    public function login_requires_valid_email(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'not-an-email',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
