<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inscripcion;
use App\Models\Torneo;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InscripcionController extends Controller
{
    /**
     * Jugadores inscritos en un torneo (§12).
     */
    public function index(Torneo $torneo): View
    {
        $torneo->load(['inscripciones.usuario'])->loadCount('inscripciones');

        return view('admin.inscripciones.index', compact('torneo'));
    }

    /**
     * Dar de baja una inscripción y liberar su plaza (§12).
     */
    public function destroy(Inscripcion $inscripcion): RedirectResponse
    {
        $inscripcion->delete();

        return back()->with('exito', 'La inscripción ha sido dada de baja. La plaza vuelve a estar disponible.');
    }
}
