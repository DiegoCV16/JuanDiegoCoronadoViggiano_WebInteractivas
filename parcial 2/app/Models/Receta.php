<?php

namespace App\Models;

use Database\Factories\RecetaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'titulo', 'categoria', 'tiempo_minutos', 'dificultad', 'ingredientes', 'pasos', 'nota'])]
class Receta extends Model
{
    /** @use HasFactory<RecetaFactory> */
    use HasFactory;

    /**
     * Categorías permitidas de una receta.
     *
     * @var list<string>
     */
    public const CATEGORIAS = ['desayuno', 'almuerzo', 'cena', 'postre', 'bebida'];

    /**
     * Dificultades permitidas de una receta.
     *
     * @var list<string>
     */
    public const DIFICULTADES = ['fácil', 'media', 'difícil'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tiempo_minutos' => 'integer',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
