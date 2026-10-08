<?php

namespace App\Models;

use Database\Factories\TorneoFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Torneo extends Model
{
    /** @use HasFactory<TorneoFactory> */
    use HasFactory;

    protected $table = 'torneos';

    public const ESTADO_ABIERTO = 'abierto';

    public const ESTADO_CERRADO = 'cerrado';

    protected $fillable = [
        'nombre',
        'juego',
        'fecha',
        'cupo',
        'descripcion',
        'estado',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'cupo' => 'integer',
        ];
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }

    /**
     * Torneos aptos para el listado público: abiertos y con fecha futura.
     * El cupo se verifica con el conteo cargado vía withCount('inscripciones').
     */
    public function scopeDisponibles(Builder $query): Builder
    {
        return $query
            ->where('estado', self::ESTADO_ABIERTO)
            ->whereDate('fecha', '>', today())
            ->orderBy('fecha');
    }

    /**
     * Número de inscritos. Usa el conteo precargado (withCount) si existe.
     */
    public function totalInscritos(): int
    {
        if (array_key_exists('inscripciones_count', $this->attributes)) {
            return (int) $this->attributes['inscripciones_count'];
        }

        return $this->inscripciones()->count();
    }

    public function plazasDisponibles(): int
    {
        return max(0, $this->cupo - $this->totalInscritos());
    }

    public function estaPasado(): bool
    {
        return $this->fecha->isPast();
    }

    public function estaLleno(): bool
    {
        return $this->totalInscritos() >= $this->cupo;
    }

    /**
     * Estado efectivo del torneo según la regla de cerrado (§5).
     */
    public function estaCerrado(): bool
    {
        return $this->estado === self::ESTADO_CERRADO
            || $this->estaPasado()
            || $this->estaLleno();
    }

    public function estaDisponible(): bool
    {
        return ! $this->estaCerrado();
    }
}
