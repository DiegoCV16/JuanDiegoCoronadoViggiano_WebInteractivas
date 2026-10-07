<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_accessing_recetario_is_redirected_to_login(): void
    {
        $this->get(route('recetas.index'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_visiting_homepage_is_redirected_to_login(): void
    {
        $this->get('/')
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_visiting_homepage_is_redirected_to_recetario(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/')
            ->assertRedirect(route('recetas.index'));
    }

    public function test_valid_registration_creates_user_and_redirects_to_empty_recetario(): void
    {
        $this->post(route('registro'), [
            'email' => 'cocinera@example.com',
            'password' => 'contraseña-segura',
            'password_confirmation' => 'contraseña-segura',
        ])->assertRedirect(route('recetas.index'));

        $this->assertDatabaseHas('users', ['email' => 'cocinera@example.com']);
        $this->assertAuthenticated();

        $this->get(route('recetas.index'))
            ->assertOk()
            ->assertSee('Todavía no tienes recetas.');
    }

    public function test_registration_with_invalid_data_shows_errors_and_does_not_log_in(): void
    {
        $this->post(route('registro'), [
            'email' => 'correo-no-valido',
            'password' => 'corta',
            'password_confirmation' => 'distinta',
        ])->assertSessionHasErrors(['email', 'password']);

        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    public function test_login_with_valid_credentials_authenticates_and_redirects_to_recetario(): void
    {
        $user = User::factory()->create(['password' => 'contraseña-segura']);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'contraseña-segura',
        ])->assertRedirect(route('recetas.index'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_with_wrong_credentials_shows_message_and_does_not_authenticate(): void
    {
        $user = User::factory()->create(['password' => 'contraseña-segura']);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'contraseña-incorrecta',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_logout_destroys_session_and_redirects_to_login(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
