<?php

namespace App\Http\Requests\Admin;

use App\Models\Torneo;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AlmacenarTorneoRequest extends FormRequest
{
    /**
     * La autorización se resuelve en el grupo de rutas (auth + admin).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'juego' => ['required', 'string', 'max:255'],
            'fecha' => ['required', 'date', 'after:today'],
            'cupo' => ['required', 'integer', 'min:2', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'estado' => ['required', Rule::in([Torneo::ESTADO_ABIERTO, Torneo::ESTADO_CERRADO])],
        ];
    }

    /**
     * Mensajes de validación en español.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del torneo es obligatorio.',
            'nombre.string' => 'El nombre del torneo debe ser un texto.',
            'nombre.max' => 'El nombre del torneo no puede superar los 255 caracteres.',
            'juego.required' => 'El juego o deporte es obligatorio.',
            'juego.string' => 'El juego o deporte debe ser un texto.',
            'juego.max' => 'El juego o deporte no puede superar los 255 caracteres.',
            'fecha.required' => 'La fecha del torneo es obligatoria.',
            'fecha.date' => 'La fecha del torneo no tiene un formato válido.',
            'fecha.after' => 'La fecha del torneo debe ser una fecha futura.',
            'cupo.required' => 'El cupo es obligatorio.',
            'cupo.integer' => 'El cupo debe ser un número entero.',
            'cupo.min' => 'El cupo debe ser como mínimo 2.',
            'cupo.max' => 'El cupo debe ser como máximo 100.',
            'descripcion.string' => 'La descripción debe ser un texto.',
            'descripcion.max' => 'La descripción no puede superar los 1000 caracteres.',
            'estado.required' => 'El estado del torneo es obligatorio.',
            'estado.in' => 'El estado debe ser "abierto" o "cerrado".',
        ];
    }
}
