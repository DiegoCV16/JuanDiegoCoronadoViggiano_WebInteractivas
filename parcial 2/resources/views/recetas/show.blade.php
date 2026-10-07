@extends('layout')

@section('titulo', $receta->titulo)

@section('contenido')
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('recetas.index') }}" class="text-sm text-gray-600 hover:underline">← Volver a mis recetas</a>
        <div class="flex items-center gap-3">
            <a href="{{ route('recetas.edit', $receta) }}"
                class="rounded border border-gray-300 bg-white px-4 py-2 text-sm font-medium hover:bg-gray-50">
                Editar
            </a>
            <form method="POST" action="{{ route('recetas.destroy', $receta) }}"
                onsubmit="return confirm('¿Seguro que deseas eliminar esta receta?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-500">
                    Eliminar
                </button>
            </form>
        </div>
    </div>

    <article class="rounded border border-gray-200 bg-white p-6">
        <h1 class="text-2xl font-semibold">{{ $receta->titulo }}</h1>

        <dl class="mt-4 grid grid-cols-3 gap-4 text-sm">
            <div>
                <dt class="font-medium text-gray-500">Categoría</dt>
                <dd>{{ ucfirst($receta->categoria) }}</dd>
            </div>
            <div>
                <dt class="font-medium text-gray-500">Tiempo</dt>
                <dd>{{ $receta->tiempo_minutos }} min</dd>
            </div>
            <div>
                <dt class="font-medium text-gray-500">Dificultad</dt>
                <dd>{{ ucfirst($receta->dificultad) }}</dd>
            </div>
        </dl>

        <section class="mt-6">
            <h2 class="mb-2 text-lg font-semibold">Ingredientes</h2>
            <ul class="list-inside list-disc space-y-1">
                @foreach (explode("\n", $receta->ingredientes) as $ingrediente)
                    @php($ingrediente = trim($ingrediente))
                    @if ($ingrediente !== '')
                        <li>{{ $ingrediente }}</li>
                    @endif
                @endforeach
            </ul>
        </section>

        <section class="mt-6">
            <h2 class="mb-2 text-lg font-semibold">Pasos de preparación</h2>
            <ol class="list-inside list-decimal space-y-1">
                @foreach (explode("\n", $receta->pasos) as $paso)
                    @php($paso = trim($paso))
                    @if ($paso !== '')
                        <li>{{ $paso }}</li>
                    @endif
                @endforeach
            </ol>
        </section>

        @if ($receta->nota)
            <section class="mt-6 rounded bg-gray-50 p-4">
                <h2 class="mb-1 text-lg font-semibold">Nota personal</h2>
                <p>{{ $receta->nota }}</p>
            </section>
        @endif
    </article>
@endsection