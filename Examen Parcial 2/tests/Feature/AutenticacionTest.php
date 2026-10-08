<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutenticacionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Caso 1: registrar un usuario nuevo crea la cuenta con rol jugador.
     */
    public function test_registro_crea_cuenta_con_rol_jugador(): void
    {
        $response = $this->post('/registro', [
            'name' => 'Carlos García',
            'email' => 'carlos@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('torneos.index'));
        $response->assertSessionHas('exito');

        $this->assertDatabaseHas('users', [
            'email' => 'carlos@example.com',
            'rol' => 'jugador',
        ]);
    }

    /**
     * El registro público no debe permitir elegir el rol administrador.
     */
    public function test_registro_ignora_toda_intencion_de_crear_un_administrador(): void
    {
        $this->post('/registro', [
            'name' => 'Carlos García',
            'email' => 'carlos@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'rol' => 'administrador',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'carlos@example.com',
            'rol' => 'jugador',
        ]);
    }

    public function test_registro_valida_los_datos_obligatorios(): void
    {
        $this->post('/registro', [])
            ->assertSessionHasErrors(['name', 'email', 'password']);
    }

    /**
     * Caso 2: iniciar sesión con un usuario registrado.
     */
    public function test_login_de_un_jugador_redirige_al_listado(): void
    {
        $jugador = User::factory()->create([
            'email' => 'jugador@example.com',
            'password' => 'password123',
        ]);

        $this->post('/login', [
            'email' => 'jugador@example.com',
            'password' => 'password123',
        ])->assertRedirect(route('torneos.index'));

        $this->assertAuthenticatedAs($jugador);
    }

    public function test_login_de_un_administrador_redirige_a_gestion(): void
    {
        $admin = User::factory()->administrador()->create([
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ])->assertRedirect(route('admin.torneos.index'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_login_fallido_muestra_error(): void
    {
        User::factory()->create(['email' => 'jugador@example.com']);

        $this->post('/login', [
            'email' => 'jugador@example.com',
            'password' => 'contrasena-incorrecta',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    /**
     * Caso 3: cerrar sesión termina la sesión correctamente.
     */
    public function test_logout_cierra_la_sesion(): void
    {
        $jugador = User::factory()->create();

        $this->actingAs($jugador)
            ->post('/logout')
            ->assertRedirect(route('torneos.index'));

        $this->assertGuest();
    }
}
