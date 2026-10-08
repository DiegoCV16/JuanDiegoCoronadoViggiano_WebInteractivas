<?php

namespace App\Http\Controllers;

use App\Models\Torneo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InscripcionController extends Controller
{
    /**
     * Inscribir al jugador autenticado en un torneo disponible (§9).
     */
    public function store(Request $request, Torneo $torneo): RedirectResponse
    {
        $usuario = $request->user();

        if ($torneo->estado === Torneo::ESTADO_CERRADO) {
            return back()->with('error', 'No es posible inscribirse: el torneo está cerrado.');
        }

        if ($torneo->estaPasado()) {
            return back()->with('error', 'No es posible inscribirse: la fecha del torneo ya pasó.');
        }

        if ($torneo->inscripciones()->where('usuario_id', $usuario->id)->exists()) {
            return back()->with('error', 'No es posible inscribirte: ya estás inscrito en este torneo.');
        }

        if ($torneo->estaLleno()) {
            return back()->with('error', 'No es posible inscribirse: el torneo ya no tiene plazas disponibles.');
        }

        $torneo->inscripciones()->create(['usuario_id' => $usuario->id]);

        return back()->with('exito', 'Te has inscrito correctamente en el torneo "'.$torneo->nombre.'".');
    }

    /**
     * Cancelar la inscripción del jugador hasta la fecha del evento (§11).
     */
    public function cancelar(Request $request, Torneo $torneo): RedirectResponse
    {
        $inscripcion = $request->user()
            ->inscripciones()
            ->where('torneo_id', $torneo->id)
            ->first();

        if (! $inscripcion) {
            return back()->with('error', 'No estás inscrito en este torneo.');
        }

        if ($torneo->estaPasado()) {
            return back()->with('error', 'No es posible cancelar la inscripción: la fecha del torneo ya pasó.');
        }

        $inscripcion->delete();

        return back()->with('exito', 'Tu inscripción en "'.$torneo->nombre.'" ha sido cancelada. La plaza vuelve a estar disponible.');
    }
}
