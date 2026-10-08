<?php

namespace App\Http\Controllers;

use App\Models\Torneo;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TorneoController extends Controller
{
    /**
     * Listado público de torneos disponibles (§7).
     */
    public function index(Request $request): View
    {
        $buscar = trim((string) $request->query('buscar', ''));

        $torneos = Torneo::query()
            ->disponibles()
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($query) use ($buscar) {
                    $query->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('juego', 'like', "%{$buscar}%");
                });
            })
            ->withCount('inscripciones')
            ->get()
            ->filter(fn (Torneo $torneo) => ! $torneo->estaLleno())
            ->values();

        return view('torneos.index', [
            'torneos' => $torneos,
            'buscar' => $buscar,
        ]);
    }

    /**
     * Detalle de un torneo (§8). Accesible también para torneos cerrados.
     */
    public function show(Request $request, Torneo $torneo): View
    {
        $torneo->load(['inscripciones.usuario'])->loadCount('inscripciones');

        $inscripcionPropia = $request->user()
            ?->inscripciones()
            ->where('torneo_id', $torneo->id)
            ->exists() ?? false;

        return view('torneos.show', [
            'torneo' => $torneo,
            'inscripcionPropia' => $inscripcionPropia,
        ]);
    }
}
