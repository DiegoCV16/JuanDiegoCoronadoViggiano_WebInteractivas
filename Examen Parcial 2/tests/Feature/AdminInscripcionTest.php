<?php

namespace Tests\Feature;

use App\Models\Inscripcion;
use App\Models\Torneo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminInscripcionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_ve_los_jugadores_inscritos_de_un_torneo(): void
    {
        $admin = User::factory()->administrador()->create();
        $torneo = Torneo::factory()->create();
        $jugador = User::factory()->create(['name' => 'María Hernández']);
        Inscripcion::factory()->for($torneo, 'torneo')->for($jugador, 'usuario')->create();

        $this->actingAs($admin)
            ->get("/admin/torneos/{$torneo->id}/inscripciones")
            ->assertOk()
            ->assertSee($torneo->nombre)
            ->assertSee('María Hernández');
    }

    public function test_admin_da_de_baja_una_inscripcion_y_libera_la_plaza(): void
    {
        $admin = User::factory()->administrador()->create();
        $torneo = Torneo::factory()->create(['cupo' => 10]);
        $jugador = User::factory()->create();
        $inscripcion = Inscripcion::factory()->for($torneo, 'torneo')->for($jugador, 'usuario')->create();

        $this->actingAs($admin)
            ->delete("/admin/inscripciones/{$inscripcion->id}")
            ->assertSessionHas('exito');

        $this->assertDatabaseMissing('inscripciones', ['id' => $inscripcion->id]);
        $this->assertSame(10, $torneo->fresh()->plazasDisponibles());
    }
}
