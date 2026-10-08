<?php

namespace Tests\Feature;

use App\Models\Inscripcion;
use App\Models\Torneo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTorneoTest extends TestCase
{
    use RefreshDatabase;

    private function unAdmin(): User
    {
        return User::factory()->administrador()->create();
    }

    private function datosValidos(array $sobrecarga = []): array
    {
        return array_merge([
            'nombre' => 'Torneo de prueba',
            'juego' => 'Fútbol',
            'fecha' => now()->addDays(10)->format('Y-m-d'),
            'cupo' => 16,
            'descripcion' => 'Un torneo para todos',
            'estado' => 'abierto',
        ], $sobrecarga);
    }

    public function test_crear_torneo_valido(): void
    {
        $this->actingAs($this->unAdmin())
            ->post('/admin/torneos', $this->datosValidos())
            ->assertRedirect(route('admin.torneos.index'))
            ->assertSessionHas('exito');

        $this->assertDatabaseHas('torneos', [
            'nombre' => 'Torneo de prueba',
            'juego' => 'Fútbol',
            'cupo' => 16,
            'estado' => 'abierto',
        ]);
    }

    public function test_las_vistas_de_crear_y_editar_se_renderizan(): void
    {
        $admin = $this->unAdmin();
        $torneo = Torneo::factory()->create();

        $this->actingAs($admin)
            ->get('/admin/torneos/create')
            ->assertOk()
            ->assertSee('Crear torneo');

        $this->actingAs($admin)
            ->get("/admin/torneos/{$torneo->id}/edit")
            ->assertOk()
            ->assertSee('Editar torneo');
    }

    public function test_crear_torneo_con_datos_invalidos(): void
    {
        $this->actingAs($this->unAdmin())
            ->post('/admin/torneos', [])
            ->assertSessionHasErrors(['nombre', 'juego', 'fecha', 'cupo', 'estado']);
    }

    public function test_crear_torneo_exige_fecha_futura(): void
    {
        $this->actingAs($this->unAdmin())
            ->post('/admin/torneos', $this->datosValidos([
                'fecha' => now()->subDay()->format('Y-m-d'),
            ]))
            ->assertSessionHasErrors(['fecha' => 'La fecha del torneo debe ser una fecha futura.']);
    }

    public function test_crear_torneo_valida_los_limites_del_cupo(): void
    {
        $admin = $this->unAdmin();

        $this->actingAs($admin)
            ->post('/admin/torneos', $this->datosValidos(['cupo' => 1]))
            ->assertSessionHasErrors(['cupo' => 'El cupo debe ser como mínimo 2.']);

        $this->actingAs($admin)
            ->post('/admin/torneos', $this->datosValidos(['cupo' => 101]))
            ->assertSessionHasErrors(['cupo' => 'El cupo debe ser como máximo 100.']);
    }

    public function test_editar_torneo_guarda_los_cambios(): void
    {
        $torneo = Torneo::factory()->create();

        $this->actingAs($this->unAdmin())
            ->put("/admin/torneos/{$torneo->id}", $this->datosValidos([
                'nombre' => 'Torneo renombrado',
                'juego' => 'Tenis',
                'cupo' => 8,
            ]))
            ->assertRedirect(route('admin.torneos.index'));

        $this->assertDatabaseHas('torneos', [
            'id' => $torneo->id,
            'nombre' => 'Torneo renombrado',
            'juego' => 'Tenis',
            'cupo' => 8,
        ]);
    }

    public function test_no_se_puede_reducir_el_cupo_por_debajo_de_los_inscritos(): void
    {
        $torneo = Torneo::factory()->create(['cupo' => 10]);
        Inscripcion::factory()->count(3)->for($torneo, 'torneo')->create();

        $this->actingAs($this->unAdmin())
            ->put("/admin/torneos/{$torneo->id}", $this->datosValidos([
                'nombre' => $torneo->nombre,
                'juego' => $torneo->juego,
                'fecha' => $torneo->fecha->format('Y-m-d'),
                'cupo' => 2,
                'descripcion' => $torneo->descripcion,
                'estado' => $torneo->estado,
            ]))
            ->assertSessionHasErrors(['cupo' => 'El cupo no puede reducirse por debajo de los jugadores inscritos actuales (3).']);
    }

    public function test_se_puede_dejar_el_cupo_igual_a_los_inscritos(): void
    {
        $torneo = Torneo::factory()->create(['cupo' => 10]);
        Inscripcion::factory()->count(3)->for($torneo, 'torneo')->create();

        $this->actingAs($this->unAdmin())
            ->put("/admin/torneos/{$torneo->id}", $this->datosValidos([
                'nombre' => $torneo->nombre,
                'juego' => $torneo->juego,
                'fecha' => $torneo->fecha->format('Y-m-d'),
                'cupo' => 3,
                'descripcion' => $torneo->descripcion,
                'estado' => $torneo->estado,
            ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.torneos.index'));
    }

    public function test_eliminar_torneo_elimina_sus_inscripciones_en_cascada(): void
    {
        $torneo = Torneo::factory()->create();
        $inscripciones = Inscripcion::factory()->count(2)->for($torneo, 'torneo')->create();

        $this->actingAs($this->unAdmin())
            ->delete("/admin/torneos/{$torneo->id}")
            ->assertRedirect(route('admin.torneos.index'))
            ->assertSessionHas('exito');

        $this->assertDatabaseMissing('torneos', ['id' => $torneo->id]);
        $this->assertDatabaseMissing('inscripciones', ['id' => $inscripciones[0]->id]);
        $this->assertDatabaseMissing('inscripciones', ['id' => $inscripciones[1]->id]);
    }
}
