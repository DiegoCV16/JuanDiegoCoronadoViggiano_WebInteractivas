<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Crear las cuentas demo: un administrador y un jugador.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@torneos.test'],
            [
                'name' => 'Administrador',
                'password' => 'password',
                'rol' => User::ROL_ADMINISTRADOR,
            ],
        );

        User::firstOrCreate(
            ['email' => 'jugador@torneos.test'],
            [
                'name' => 'Jugador Demo',
                'password' => 'password',
                'rol' => User::ROL_JUGADOR,
            ],
        );
    }
}
