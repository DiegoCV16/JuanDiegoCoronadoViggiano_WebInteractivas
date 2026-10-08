@extends('layouts.app')

@section('titulo', 'Torneos disponibles')

@section('contenido')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Torneos disponibles</h1>
                <p class="mt-1 text-sm text-gray-500">Solo se muestran los torneos abiertos, con fecha futura y plazas libres.</p>
            </div>

            <form method="GET" action="{{ route('torneos.index') }}" class="flex w-full max-w-md gap-2">
                <label for="buscar" class="sr-only">Buscar torneos</label>
                <input type="search" id="buscar" name="buscar" value="{{ $buscar }}"
                    placeholder="Buscar por nombre o juego..."
                    class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                <button type="submit"
                    class="inline-flex shrink-0 items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    Buscar
                </button>
            </form>
        </div>

        @if ($torneos->isEmpty())
            <div class="rounded-xl border border-dashed border-gray-300 bg-white p-10 text-center">
                <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                <p class="mt-3 text-base font-semibold text-gray-900">No hay torneos disponibles.</p>
                <p class="mt-1 text-sm text-gray-500">Vuelve más tarde; pronto se publicarán nuevos torneos.</p>
                @if ($buscar !== '')
                    <a href="{{ route('torneos.index') }}"
                        class="mt-4 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-500">Limpiar búsqueda</a>
                @endif
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($torneos as $torneo)
                    <article
                        class="flex flex-col rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                        <div class="flex items-start justify-between gap-3">
                            <h2 class="text-lg font-semibold text-gray-900">
                                <a href="{{ route('torneos.show', $torneo) }}" class="hover:text-indigo-600">
                                    {{ $torneo->nombre }}
                                </a>
                            </h2>
                            <x-estado-torneo :torneo="$torneo" />
                        </div>

                        <p class="mt-1 text-sm font-medium text-indigo-600">{{ $torneo->juego }}</p>

                        <dl class="mt-4 space-y-2 text-sm text-gray-600">
                            <div class="flex items-center gap-2">
                                <dt class="sr-only">Fecha</dt>
                                <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                                <dd>{{ $torneo->fecha->format('d/m/Y') }}</dd>
                            </div>
                            <div class="flex items-center gap-2">
                                <dt class="sr-only">Plazas</dt>
                                <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                                </svg>
                                <dd><x-plazas :torneo="$torneo" /></dd>
                            </div>
                        </dl>

                        <div class="mt-5 flex items-center justify-between border-t border-gray-100 pt-4">
                            <a href="{{ route('torneos.show', $torneo) }}"
                                class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                Ver detalle &rarr;
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
@endsection