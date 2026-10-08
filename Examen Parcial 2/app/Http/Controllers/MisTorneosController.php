<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class MisTorneosController extends Controller
{
    /**
     * Torneos en los que está inscrito el jugador autenticado (§10).
     */
    public function index(Request $request): View
    {
        $inscripciones = $request->user()
            ->inscripciones()
            ->with(['torneo' => fn ($query) => $query->withCount('inscripciones')])
            ->get()
            ->sortBy('torneo.fecha')
            ->values();

        return view('mis-torneos.index', compact('inscripciones'));
    }
}
