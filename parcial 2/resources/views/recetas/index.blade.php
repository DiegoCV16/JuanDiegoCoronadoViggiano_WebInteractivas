@extends('layout')

@section('titulo', 'Mis recetas')

@section('contenido')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Mis recetas</h1>
        <a href="{{ route('recetas.create') }}"
            class="rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
            Nueva receta
        </a>
    </div>

    <form method="GET" action="{{ route('recetas.index') }}"
        class="mb-6 flex flex-wrap items-end gap-4 rounded border border-gray-200 bg-white p-4">
        <div class="min-w-48 flex-1">
            <label for="buscar" class="mb-1 block text-sm font-medium text-gray-700">Buscar por título</label>
            <input id="buscar" type="text" name="buscar" value="{{ $busqueda }}" placeholder="Ej. Tacos"
                class="w-full rounded border border-gray-300 px-3 py-2 focus:border-indigo-500 focus:outline-none">
        </div>

        <div>
            <label for="categoria" class="mb-1 block text-sm font-medium text-gray-700">Categoría</label>
            <select id="categoria" name="categoria"
                class="rounded border border-gray-300 px-3 py-2 focus:border-indigo-500 focus:outline-none">
                <option value="">Todas</option>
                @foreach (\App\Models\Receta::CATEGORIAS as $opcion)
                    <option value="{{ $opcion }}" @selected($categoria === $opcion)>{{ ucfirst($opcion) }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit"
            class="rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
            Buscar
        </button>
    </form>

    @if ($recetas->isEmpty())
        <div class="rounded border border-gray-200 bg-white p-8 text-center text-gray-600">
            @if ($busqueda !== '' || $categoria !== '')
                No se encontraron recetas que coincidan con los criterios.
            @else
                Todavía no tienes recetas.
            @endif
        </div>
    @else
        <div class="overflow-hidden rounded border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-medium uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Título</th>
                        <th class="px-4 py-3">Categoría</th>
                        <th class="px-4 py-3">Tiempo</th>
                        <th class="px-4 py-3">Dificultad</th>
                        <th class="px-4 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($recetas as $receta)
                        <tr>
                            <td class="px-4 py-3 font-medium">
                                <a href="{{ route('recetas.show', $receta) }}" class="text-indigo-600 hover:underline">
                                    {{ $receta->titulo }}
                                </a>
                            </td>
                            <td class="px-4 py-3">{{ ucfirst($receta->categoria) }}</td>
                            <td class="px-4 py-3">{{ $receta->tiempo_minutos }} min</td>
                            <td class="px-4 py-3">{{ ucfirst($receta->dificultad) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('recetas.edit', $receta) }}" class="text-gray-600 hover:underline">Editar</a>
                                    <form method="POST" action="{{ route('recetas.destroy', $receta) }}"
                                        onsubmit="return confirm('¿Seguro que deseas eliminar esta receta?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection