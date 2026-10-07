<?php

namespace Database\Factories;

use App\Models\Receta;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Receta>
 */
class RecetaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'titulo' => fake()->sentence(3),
            'categoria' => fake()->randomElement(Receta::CATEGORIAS),
            'tiempo_minutos' => fake()->numberBetween(1, 180),
            'dificultad' => fake()->randomElement(Receta::DIFICULTADES),
            'ingredientes' => "Harina\nAzúcar\nHuevo",
            'pasos' => "Mezclar los ingredientes.\nHornear durante 20 minutos.",
            'nota' => fake()->optional()->sentence(),
        ];
    }
}
