<?php

namespace App\Models;

use Database\Factories\InscripcionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inscripcion extends Model
{
    /** @use HasFactory<InscripcionFactory> */
    use HasFactory;

    protected $table = 'inscripciones';

    protected $fillable = [
        'torneo_id',
        'usuario_id',
    ];

    public function torneo(): BelongsTo
    {
        return $this->belongsTo(Torneo::class, 'torneo_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
