<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ActualizarTorneoRequest;
use App\Http\Requests\Admin\AlmacenarTorneoRequest;
use App\Models\Torneo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TorneoController extends Controller
{
    /**
     * Gestión de todos los torneos para el administrador (§6).
     */
    public function index(Request $request): View
    {
        $buscar = trim((string) $request->query('buscar', ''));

        $torneos = Torneo::query()
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($query) use ($buscar) {
                    $query->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('juego', 'like', "%{$buscar}%");
                });
            })
            ->withCount('inscripciones')
            ->orderBy('fecha')
            ->get();

        return view('admin.torneos.index', [
            'torneos' => $torneos,
            'buscar' => $buscar,
        ]);
    }

    public function create(): View
    {
        return view('admin.torneos.create');
    }

    public function store(AlmacenarTorneoRequest $request): RedirectResponse
    {
        Torneo::create($request->validated());

        return redirect()
            ->route('admin.torneos.index')
            ->with('exito', 'Torneo creado correctamente.');
    }

    public function edit(Torneo $torneo): View
    {
        $torneo->loadCount('inscripciones');

        return view('admin.torneos.edit', compact('torneo'));
    }

    public function update(ActualizarTorneoRequest $request, Torneo $torneo): RedirectResponse
    {
        $torneo->update($request->validated());

        return redirect()
            ->route('admin.torneos.index')
            ->with('exito', 'Torneo actualizado correctamente.');
    }

    public function destroy(Torneo $torneo): RedirectResponse
    {
        $nombre = $torneo->nombre;

        $torneo->delete();

        return redirect()
            ->route('admin.torneos.index')
            ->with('exito', 'El torneo "'.$nombre.'" y sus inscripciones han sido eliminados.');
    }
}
