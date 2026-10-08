@extends('layouts.app')

@section('titulo', 'Mis torneos')

@section('contenido')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Mis torneos</h1>
                <p class="mt-1 text-sm text-gray-500">Torneos en los que estás inscrito.</p>
            </div>
            <a href="{{ route('torneos.index') }}"
                class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                Buscar torneos disponibles
            </a>
        </div>

        @if ($inscripciones->isEmpty())
            <div class="rounded-xl border border-dashed border-gray-300 bg-white p-10 text-center">
                <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16 8.25v6m-8-6v6m4-10.5l-7-2.25v6.75c0 3.722 2.637 6.135 6.444 6.735.356.056.712.056 1.112 0C13.363 15.135 16 12.722 16 9V2.25L9 2.25z" />
                </svg>
                <p class="mt-3 text-base font-semibold text-gray-900">Aún no estás inscrito en ningún torneo.</p>
                <p class="mt-1 text-sm text-gray-500">Consulta el listado de torneos disponibles e inscríbete.</p>
                <a href="{{ route('torneos.index') }}"
                    class="mt-4 inline-block rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                    Ver torneos disponibles
                </a>
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($inscripciones as $inscripcion)
                    @php($torneo = $inscripcion->torneo)
                    <article
                        class="flex flex-col rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">
                                    <a href="{{ route('torneos.show', $torneo) }}" class="hover:text-indigo-600">
                                        {{ $torneo->nombre }}
                                    </a>
                                </h2>
                                <p class="mt-0.5 text-sm font-medium text-indigo-600">{{ $torneo->juego }}</p>
                            </div>
                            <span
                                class="inline-flex shrink-0 items-center rounded-full bg-sky-100 px-2.5 py-0.5 text-xs font-semibold text-sky-700">
                                Inscrito
                            </span>
                        </div>

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
                            <div class="flex items-center gap-2">
                                <dt class="sr-only">Estado</dt>
                                <x-estado-torneo :torneo="$torneo" />
                            </div>
                        </dl>

                        <div class="mt-5 border-t border-gray-100 pt-4">
                            @if ($torneo->estaPasado())
                                <p class="text-sm text-gray-500">La fecha de este torneo ya pasó.</p>
                            @else
                                <form method="POST" action="{{ route('mis-torneos.cancelar', $torneo) }}"
                                    onsubmit="return confirm('¿Seguro que quieres cancelar tu inscripción en «{{ $torneo->nombre }}»?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full rounded-md border border-red-300 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50">
                                        Cancelar inscripción
                                    </button>
                                </form>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <p class="text-sm text-gray-500">
                Puedes cancelar tu inscripción únicamente hasta la fecha del evento. Una vez cancelada, la plaza queda
                disponible para otros jugadores.
            </p>
        @endif
    </div>
@endsection