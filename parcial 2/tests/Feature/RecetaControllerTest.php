<?php

namespace Tests\Feature;

use App\Models\Receta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecetaControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function datos(): array
    {
        return [
            'titulo' => 'Tacos de birria',
            'categoria' => 'almuerzo',
            'tiempo_minutos' => 45,
            'dificultad' => 'media',
            'ingredientes' => "Carne de res\nCebolla\nChile",
            'pasos' => "Cocinar la carne.\nArmar los tacos.",
            'nota' => 'Servir con limón.',
        ];
    }

    public function test_index_shows_empty_state_when_user_has_no_recipes(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('recetas.index'))
            ->assertOk()
            ->assertSee('Todavía no tienes recetas.');
    }

    public function test_index_only_shows_recipes_of_the_authenticated_user(): void
    {
        $usuario = User::factory()->create();
        $otroUsuario = User::factory()->create();

        Receta::factory()->for($usuario, 'usuario')->create(['titulo' => 'Tacos de birria']);
        Receta::factory()->for($otroUsuario, 'usuario')->create(['titulo' => 'Tarta de chocolate']);

        $this->actingAs($usuario)
            ->get(route('recetas.index'))
            ->assertOk()
            ->assertSee('Tacos de birria')
            ->assertDontSee('Tarta de chocolate');
    }

    public function test_valid_recipe_is_created_and_redirects_with_success_message(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)
            ->post(route('recetas.store'), self::datos())
            ->assertRedirect(route('recetas.index'))
            ->assertSessionHas('exito', 'Receta creada correctamente.');

        $this->assertDatabaseHas('recetas', [
            'titulo' => 'Tacos de birria',
            'user_id' => $usuario->id,
        ]);
    }

    public function test_empty_recipe_payload_shows_errors_on_each_required_field(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('recetas.store'), [
                'titulo' => '',
                'categoria' => '',
                'tiempo_minutos' => '',
                'dificultad' => '',
                'ingredientes' => '',
                'pasos' => '',
            ])
            ->assertSessionHasErrors(['titulo', 'categoria', 'tiempo_minutos', 'dificultad', 'ingredientes', 'pasos']);

        $this->assertDatabaseCount('recetas', 0);
    }

    public function test_time_in_minutes_must_be_greater_than_zero(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('recetas.store'), [
                ...self::datos(),
                'tiempo_minutos' => 0,
            ])->assertSessionHasErrors('tiempo_minutos');

        $this->assertDatabaseCount('recetas', 0);
    }

    public function test_recipe_with_invalid_categoria_shows_message(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('recetas.store'), [
                ...self::datos(),
                'categoria' => 'asado',
            ])->assertSessionHasErrors([
                'categoria' => 'La categoría solo puede ser: desayuno, almuerzo, cena, postre o bebida.',
            ]);
    }

    public function test_recipe_with_invalid_dificultad_shows_message(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('recetas.store'), [
                ...self::datos(),
                'dificultad' => 'alta',
            ])->assertSessionHasErrors([
                'dificultad' => 'La dificultad solo puede ser: fácil, media o difícil.',
            ]);
    }

    public function test_recipe_detail_shows_ingredients_and_steps_as_lists(): void
    {
        $usuario = User::factory()->create();
        $receta = Receta::factory()->for($usuario, 'usuario')->create([
            'ingredientes' => "Carne de res\nCebolla",
            'pasos' => "Cocinar la carne.\nArmar los tacos.",
        ]);

        $this->actingAs($usuario)
            ->get(route('recetas.show', $receta))
            ->assertOk()
            ->assertSee('<li>Carne de res</li>', false)
            ->assertSee('<li>Cebolla</li>', false)
            ->assertSee('<li>Cocinar la carne.</li>', false)
            ->assertSee('<li>Armar los tacos.</li>', false);
    }

    public function test_edit_form_is_prefilled_with_current_recipe_data(): void
    {
        $usuario = User::factory()->create();
        $receta = Receta::factory()->for($usuario, 'usuario')->create();

        $this->actingAs($usuario)
            ->get(route('recetas.edit', $receta))
            ->assertOk()
            ->assertSee('value="'.$receta->titulo.'"', false)
            ->assertSee($receta->ingredientes, false);
    }

    public function test_valid_edit_persists_changes_and_redirects_with_success_message(): void
    {
        $usuario = User::factory()->create();
        $receta = Receta::factory()->for($usuario, 'usuario')->create();

        $this->actingAs($usuario)
            ->put(route('recetas.update', $receta), [
                ...self::datos(),
                'titulo' => 'Tacos de suadero',
            ])
            ->assertRedirect(route('recetas.index'))
            ->assertSessionHas('exito', 'Receta actualizada correctamente.');

        $this->assertDatabaseHas('recetas', [
            'id' => $receta->id,
            'titulo' => 'Tacos de suadero',
        ]);
    }

    public function test_edit_uses_the_same_validations_as_create(): void
    {
        $usuario = User::factory()->create();
        $receta = Receta::factory()->for($usuario, 'usuario')->create();

        $this->actingAs($usuario)
            ->put(route('recetas.update', $receta), [
                ...self::datos(),
                'titulo' => '',
            ])->assertSessionHasErrors('titulo');

        $this->assertDatabaseHas('recetas', [
            'id' => $receta->id,
            'titulo' => $receta->titulo,
        ]);
    }

    public function test_deleting_recipe_removes_it_and_redirects_with_success_message(): void
    {
        $usuario = User::factory()->create();
        $receta = Receta::factory()->for($usuario, 'usuario')->create();

        $this->actingAs($usuario)
            ->delete(route('recetas.destroy', $receta))
            ->assertRedirect(route('recetas.index'))
            ->assertSessionHas('exito', 'Receta eliminada correctamente.');

        $this->assertModelMissing($receta);
    }

    public function test_user_cannot_view_another_users_recipe_detail(): void
    {
        $usuario = User::factory()->create();
        $otroUsuario = User::factory()->create();
        $recetaAjena = Receta::factory()->for($otroUsuario, 'usuario')->create();

        $this->actingAs($usuario)
            ->get(route('recetas.show', $recetaAjena))
            ->assertNotFound();
    }

    public function test_user_cannot_open_the_edit_form_of_another_users_recipe(): void
    {
        $usuario = User::factory()->create();
        $otroUsuario = User::factory()->create();
        $recetaAjena = Receta::factory()->for($otroUsuario, 'usuario')->create();

        $this->actingAs($usuario)
            ->get(route('recetas.edit', $recetaAjena))
            ->assertNotFound();
    }

    public function test_user_cannot_update_another_users_recipe(): void
    {
        $usuario = User::factory()->create();
        $otroUsuario = User::factory()->create();
        $recetaAjena = Receta::factory()->for($otroUsuario, 'usuario')->create();

        $this->actingAs($usuario)
            ->put(route('recetas.update', $recetaAjena), self::datos())
            ->assertNotFound();

        $this->assertDatabaseHas('recetas', [
            'id' => $recetaAjena->id,
            'titulo' => $recetaAjena->titulo,
        ]);
    }

    public function test_user_cannot_delete_another_users_recipe(): void
    {
        $usuario = User::factory()->create();
        $otroUsuario = User::factory()->create();
        $recetaAjena = Receta::factory()->for($otroUsuario, 'usuario')->create();

        $this->actingAs($usuario)
            ->delete(route('recetas.destroy', $recetaAjena))
            ->assertNotFound();

        $this->assertModelExists($recetaAjena);
    }

    public function test_search_by_title_returns_only_matching_recipes(): void
    {
        $usuario = User::factory()->create();
        Receta::factory()->for($usuario, 'usuario')->create(['titulo' => 'Tacos de birria']);
        Receta::factory()->for($usuario, 'usuario')->create(['titulo' => 'Ensalada de atún']);

        $this->actingAs($usuario)
            ->get(route('recetas.index', ['buscar' => 'tacos']))
            ->assertOk()
            ->assertSee('Tacos de birria')
            ->assertDontSee('Ensalada de atún');
    }

    public function test_filter_by_category_returns_only_recipes_of_that_category(): void
    {
        $usuario = User::factory()->create();
        Receta::factory()->for($usuario, 'usuario')->create(['titulo' => 'Hotcakes', 'categoria' => 'desayuno']);
        Receta::factory()->for($usuario, 'usuario')->create(['titulo' => 'Tacos de birria', 'categoria' => 'almuerzo']);

        $this->actingAs($usuario)
            ->get(route('recetas.index', ['categoria' => 'desayuno']))
            ->assertOk()
            ->assertSee('Hotcakes')
            ->assertDontSee('Tacos de birria');
    }

    public function test_search_and_category_filter_are_combined(): void
    {
        $usuario = User::factory()->create();
        Receta::factory()->for($usuario, 'usuario')->create(['titulo' => 'Tacos de birria', 'categoria' => 'almuerzo']);
        Receta::factory()->for($usuario, 'usuario')->create(['titulo' => 'Tacos de campechanos', 'categoria' => 'cena']);

        $this->actingAs($usuario)
            ->get(route('recetas.index', ['buscar' => 'tacos', 'categoria' => 'cena']))
            ->assertOk()
            ->assertSee('Tacos de campechanos')
            ->assertDontSee('Tacos de birria');
    }

    public function test_search_without_results_shows_a_message(): void
    {
        $usuario = User::factory()->create();
        Receta::factory()->for($usuario, 'usuario')->create(['titulo' => 'Ensalada de atún']);

        $this->actingAs($usuario)
            ->get(route('recetas.index', ['buscar' => 'inexistente']))
            ->assertOk()
            ->assertSee('No se encontraron recetas que coincidan con los criterios.');
    }
}
