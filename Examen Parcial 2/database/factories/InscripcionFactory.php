<?php

namespace Database\Factories;

use App\Models\Inscripcion;
use App\Models\Torneo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inscripcion>
 */
class InscripcionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'torneo_id' => Torneo::factory(),
            'usuario_id' => User::factory(),
        ];
    }
}
