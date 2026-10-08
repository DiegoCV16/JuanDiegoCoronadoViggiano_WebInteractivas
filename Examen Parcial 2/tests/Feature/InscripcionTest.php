<?php

namespace Tests\Feature;

use App\Models\Inscripcion;
use App\Models\Torneo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InscripcionTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_jugador_puede_inscribirse_en_un_torneo_disponible(): void
    {
        $jugador = User::factory()->create();
        $torneo = Torneo::factory()->create();

        $this->actingAs($jugador)
            ->post("/torneos/{$torneo->id}/inscribirse")
            ->assertSessionHas('exito');

        $this->assertDatabaseHas('inscripciones', [
            'torneo_id' => $torneo->id,
            'usuario_id' => $jugador->id,
        ]);
    }

    public function test_no_se_puede_inscribir_dos_veces_al_mismo_torneo(): void
    {
        $jugador = User::factory()->create();
        $torneo = Torneo::factory()->create();
        Inscripcion::factory()->for($torneo, 'torneo')->for($jugador, 'usuario')->create();

        $this->actingAs($jugador)
            ->post("/torneos/{$torneo->id}/inscribirse")
            ->assertSessionHas('error', 'No es posible inscribirte: ya estás inscrito en este torneo.');

        $this->assertSame(1, Inscripcion::where('torneo_id', $torneo->id)->count());
    }

    public function test_no_se_puede_inscribir_a_un_torneo_lleno(): void
    {
        $jugador = User::factory()->create();
        $torneo = Torneo::factory()->create(['cupo' => 2]);
        Inscripcion::factory()->count(2)->for($torneo, 'torneo')->create();

        $this->actingAs($jugador)
            ->post("/torneos/{$torneo->id}/inscribirse")
            ->assertSessionHas('error', 'No es posible inscribirse: el torneo ya no tiene plazas disponibles.');
    }

    public function test_no_se_puede_inscribir_a_un_torneo_cerrado(): void
    {
        $jugador = User::factory()->create();
        $torneo = Torneo::factory()->cerrado()->create();

        $this->actingAs($jugador)
            ->post("/torneos/{$torneo->id}/inscribirse")
            ->assertSessionHas('error', 'No es posible inscribirse: el torneo está cerrado.');
    }

    public function test_no_se_puede_inscribir_a_un_torneo_con_fecha_pasada(): void
    {
        $jugador = User::factory()->create();
        $torneo = Torneo::factory()->pasado()->create();

        $this->actingAs($jugador)
            ->post("/torneos/{$torneo->id}/inscribirse")
            ->assertSessionHas('error', 'No es posible inscribirse: la fecha del torneo ya pasó.');
    }

    public function test_mis_torneos_muestra_solo_las_inscripciones_del_jugador_actual(): void
    {
        $jugador = User::factory()->create();
        $otro = User::factory()->create();
        $miTorneo = Torneo::factory()->create(['nombre' => 'Mi Torneo Unico']);
        $torneoAjeno = Torneo::factory()->create(['nombre' => 'Torneo Ajeno']);

        Inscripcion::factory()->for($miTorneo, 'torneo')->for($jugador, 'usuario')->create();
        Inscripcion::factory()->for($torneoAjeno, 'torneo')->for($otro, 'usuario')->create();

        $this->actingAs($jugador)
            ->get('/mis-torneos')
            ->assertOk()
            ->assertSee('Mi Torneo Unico')
            ->assertDontSee('Torneo Ajeno');
    }

    public function test_cancelar_la_inscripcion_libera_la_plaza(): void
    {
        $jugador = User::factory()->create();
        $otro = User::factory()->create();
        $torneo = Torneo::factory()->create(['cupo' => 5]);
        Inscripcion::factory()->for($torneo, 'torneo')->for($jugador, 'usuario')->create();
        Inscripcion::factory()->for($torneo, 'torneo')->for($otro, 'usuario')->create();

        $this->actingAs($jugador)
            ->delete("/mis-torneos/{$torneo->id}/cancelar")
            ->assertSessionHas('exito');

        $this->assertDatabaseMissing('inscripciones', [
            'torneo_id' => $torneo->id,
            'usuario_id' => $jugador->id,
        ]);

        $torneo = $torneo->fresh();
        $this->assertSame(4, $torneo->plazasDisponibles());

        $this->actingAs($jugador)
            ->post("/torneos/{$torneo->id}/inscribirse")
            ->assertSessionHas('exito');
    }

    public function test_no_se_puede_cancelar_una_inscripcion_despues_de_la_fecha_del_evento(): void
    {
        $jugador = User::factory()->create();
        $torneo = Torneo::factory()->pasado()->create();
        $inscripcion = Inscripcion::factory()->for($torneo, 'torneo')->for($jugador, 'usuario')->create();

        $this->actingAs($jugador)
            ->delete("/mis-torneos/{$torneo->id}/cancelar")
            ->assertSessionHas('error', 'No es posible cancelar la inscripción: la fecha del torneo ya pasó.');

        $this->assertDatabaseHas('inscripciones', ['id' => $inscripcion->id]);
    }
}
