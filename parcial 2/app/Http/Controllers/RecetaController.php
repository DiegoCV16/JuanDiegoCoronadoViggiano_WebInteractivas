<?php

namespace App\Http\Controllers;

use App\Models\Receta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecetaController extends Controller
{
    public function index(Request $request): View
    {
        $busqueda = trim((string) $request->query('buscar', ''));
        $categoria = (string) $request->query('categoria', '');

        $consulta = $request->user()->recetas()->latest();

        if ($busqueda !== '') {
            $consulta->where('titulo', 'like', "%{$busqueda}%");
        }

        if (in_array($categoria, Receta::CATEGORIAS, true)) {
            $consulta->where('categoria', $categoria);
        }

        return view('recetas.index', [
            'recetas' => $consulta->get(),
            'busqueda' => $busqueda,
            'categoria' => $categoria,
        ]);
    }

    public function create(): View
    {
        return view('recetas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate($this->reglas(), $this->mensajes());

        $request->user()->recetas()->create($datos);

        return redirect()
            ->route('recetas.index')
            ->with('exito', 'Receta creada correctamente.');
    }

    public function show(Request $request, Receta $receta): View
    {
        $this->garantizarPropiedad($request, $receta);

        return view('recetas.show', ['receta' => $receta]);
    }

    public function edit(Request $request, Receta $receta): View
    {
        $this->garantizarPropiedad($request, $receta);

        return view('recetas.edit', ['receta' => $receta]);
    }

    public function update(Request $request, Receta $receta): RedirectResponse
    {
        $this->garantizarPropiedad($request, $receta);

        $datos = $request->validate($this->reglas(), $this->mensajes());

        $receta->update($datos);

        return redirect()
            ->route('recetas.index')
            ->with('exito', 'Receta actualizada correctamente.');
    }

    public function destroy(Request $request, Receta $receta): RedirectResponse
    {
        $this->garantizarPropiedad($request, $receta);

        $receta->delete();

        return redirect()
            ->route('recetas.index')
            ->with('exito', 'Receta eliminada correctamente.');
    }

    private function garantizarPropiedad(Request $request, Receta $receta): void
    {
        abort_unless((int) $receta->user_id === (int) $request->user()->id, 404);
    }

    /**
     * @return array<string, list<string>>
     */
    private function reglas(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'categoria' => ['required', 'in:desayuno,almuerzo,cena,postre,bebida'],
            'tiempo_minutos' => ['required', 'integer', 'gt:0'],
            'dificultad' => ['required', 'in:fácil,media,difícil'],
            'ingredientes' => ['required', 'string'],
            'pasos' => ['required', 'string'],
            'nota' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function mensajes(): array
    {
        return [
            'titulo.required' => 'El título es obligatorio.',
            'titulo.string' => 'El título debe ser texto.',
            'titulo.max' => 'El título no debe exceder 255 caracteres.',
            'categoria.required' => 'La categoría es obligatoria.',
            'categoria.in' => 'La categoría solo puede ser: desayuno, almuerzo, cena, postre o bebida.',
            'tiempo_minutos.required' => 'El tiempo en minutos es obligatorio.',
            'tiempo_minutos.integer' => 'El tiempo en minutos debe ser un número entero.',
            'tiempo_minutos.gt' => 'El tiempo en minutos debe ser mayor a 0.',
            'dificultad.required' => 'La dificultad es obligatoria.',
            'dificultad.in' => 'La dificultad solo puede ser: fácil, media o difícil.',
            'ingredientes.required' => 'Los ingredientes son obligatorios.',
            'ingredientes.string' => 'Los ingredientes deben ser texto.',
            'pasos.required' => 'Los pasos de preparación son obligatorios.',
            'pasos.string' => 'Los pasos de preparación deben ser texto.',
            'nota.string' => 'La nota personal debe ser texto.',
        ];
    }
}
