<?php

namespace Database\Factories;

use App\Models\Torneo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Torneo>
 */
class TorneoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => 'Torneo '.fake()->unique()->numberBetween(1, 100000),
            'juego' => fake()->randomElement([
                'League of Legends',
                'Counter-Strike 2',
                'Valorant',
                'Rocket League',
                'FIFA 26',
                'Street Fighter 6',
            ]),
            'fecha' => fake()->dateTimeBetween('+10 days', '+90 days')->format('Y-m-d'),
            'cupo' => fake()->numberBetween(2, 100),
            'descripcion' => fake()->optional()->sentence(8),
            'estado' => Torneo::ESTADO_ABIERTO,
        ];
    }

    public function cerrado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => Torneo::ESTADO_CERRADO,
        ]);
    }

    public function pasado(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha' => fake()->dateTimeBetween('-60 days', '-1 day')->format('Y-m-d'),
        ]);
    }

    public function sinDescripcion(): static
    {
        return $this->state(fn (array $attributes) => [
            'descripcion' => null,
        ]);
    }
}
