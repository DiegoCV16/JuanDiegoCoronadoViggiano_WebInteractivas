<?php

namespace App\Http\Requests\Admin;

use App\Models\Torneo;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ActualizarTorneoRequest extends AlmacenarTorneoRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $reglas = parent::rules();
        $torneo = $this->route('torneo');

        $reglas['cupo'][] = function (string $attribute, mixed $value, Closure $fail) use ($torneo): void {
            if (! $torneo instanceof Torneo || ! is_numeric($value)) {
                return;
            }

            $inscritos = $torneo->totalInscritos();

            if ((int) $value < $inscritos) {
                $fail("El cupo no puede reducirse por debajo de los jugadores inscritos actuales ({$inscritos}).");
            }
        };

        return $reglas;
    }
}
