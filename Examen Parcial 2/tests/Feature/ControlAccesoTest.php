<?php

namespace Tests\Feature;

use App\Models\Inscripcion;
use App\Models\Torneo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ControlAccesoTest extends TestCase
{
    use RefreshDatabase;

    private const MENSAJE_ACCESO = 'Acceso no permitido: no tienes permisos de administrador para acceder a esa sección.';

    /**
     * Caso 5: un jugador no puede acceder a la gestión de administrador.
     */
    public function test_un_jugador_no_puede_acceder_a_la_gestion(): void
    {
        $jugador = User::factory()->create();

        $this->actingAs($jugador)
            ->get('/admin/torneos')
            ->assertRedirect(route('torneos.index'))
            ->assertSessionHas('error', self::MENSAJE_ACCESO);
    }

    /**
     * Un visitante sin sesión es redirigido al inicio de sesión.
     */
    public function test_un_visitante_es_redirigido_al_login_en_admin(): void
    {
        $this->get('/admin/torneos')->assertRedirect(route('login'));
    }

    public function test_un_visitante_no_puede_inscribirse(): void
    {
        $torneo = Torneo::factory()->create();

        $this->post("/torneos/{$torneo->id}/inscribirse")
            ->assertRedirect(route('login'));
    }

    /**
     * Caso 4: el administrador sí puede acceder a la gestión.
     */
    public function test_un_administrador_accede_a_la_gestion(): void
    {
        $admin = User::factory()->administrador()->create();

        $this->actingAs($admin)
            ->get('/admin/torneos')
            ->assertOk();
    }

    public function test_un_jugador_no_puede_cancelar_la_inscripcion_de_otro(): void
    {
        $torneo = Torneo::factory()->create();
        $dueño = User::factory()->create();
        $intruso = User::factory()->create();

        Inscripcion::factory()->for($torneo, 'torneo')->for($dueño, 'usuario')->create();

        $this->actingAs($intruso)
            ->delete("/mis-torneos/{$torneo->id}/cancelar")
            ->assertSessionHas('error', 'No estás inscrito en este torneo.');

        $this->assertDatabaseHas('inscripciones', [
            'torneo_id' => $torneo->id,
            'usuario_id' => $dueño->id,
        ]);
    }
}
