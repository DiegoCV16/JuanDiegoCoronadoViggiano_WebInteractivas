<?php

namespace Tests\Feature;

use App\Models\Inscripcion;
use App\Models\Torneo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListadoTorneoTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_listado_solo_muestra_torneos_abiertos_futuros_y_con_cupo(): void
    {
        $disponible = Torneo::factory()->create();
        $cerrado = Torneo::factory()->cerrado()->create();
        $pasado = Torneo::factory()->pasado()->create();
        $lleno = Torneo::factory()->create(['cupo' => 2]);
        Inscripcion::factory()->count(2)->for($lleno, 'torneo')->create();

        $response = $this->get('/');

        $response->assertOk();

        $torneos = $response->viewData('torneos');
        $this->assertCount(1, $torneos);
        $this->assertSame($disponible->id, $torneos->first()->id);
    }

    public function test_el_listado_ordena_por_fecha_mas_proxima(): void
    {
        $lejano = Torneo::factory()->create(['fecha' => now()->addDays(60)->format('Y-m-d')]);
        $proximo = Torneo::factory()->create(['fecha' => now()->addDays(10)->format('Y-m-d')]);

        $torneos = $this->get('/')->viewData('torneos');

        $this->assertSame($proximo->id, $torneos->first()->id);
        $this->assertSame($lejano->id, $torneos->last()->id);
    }

    public function test_el_listado_muestra_mensaje_cuando_no_hay_disponibles(): void
    {
        Torneo::factory()->cerrado()->create();

        $response = $this->get('/');

        $response->assertOk();
        $this->assertCount(0, $response->viewData('torneos'));
        $response->assertSee('No hay torneos disponibles.');
    }

    public function test_el_buscador_filtra_por_nombre_o_juego(): void
    {
        $futbol = Torneo::factory()->create(['nombre' => 'Copa Mundial', 'juego' => 'Fútbol']);
        $ajedrez = Torneo::factory()->create(['juego' => 'Ajedrez']);

        $torneos = $this->get('/?buscar=Copa')->viewData('torneos');
        $this->assertSame([$futbol->id], $torneos->pluck('id')->all());

        $torneos = $this->get('/?buscar=ajedrez')->viewData('torneos');
        $this->assertSame([$ajedrez->id], $torneos->pluck('id')->all());
    }

    public function test_el_detalle_muestra_los_datos_y_participantes(): void
    {
        $torneo = Torneo::factory()->create(['descripcion' => 'Un torneo especial']);
        $jugadores = User::factory()->count(2)->create();
        foreach ($jugadores as $jugador) {
            Inscripcion::factory()->for($torneo, 'torneo')->for($jugador, 'usuario')->create();
        }

        $this->get("/torneos/{$torneo->id}")
            ->assertOk()
            ->assertSee($torneo->nombre)
            ->assertSee($torneo->juego)
            ->assertSee('Un torneo especial')
            ->assertSee($jugadores[0]->name)
            ->assertSee($jugadores[1]->name);
    }

    public function test_un_torneo_cerrado_se_consulta_por_acceso_directo(): void
    {
        $cerrado = Torneo::factory()->cerrado()->create();

        $this->get("/torneos/{$cerrado->id}")
            ->assertOk()
            ->assertSee($cerrado->nombre)
            ->assertSee('Cerrado');
    }
}
