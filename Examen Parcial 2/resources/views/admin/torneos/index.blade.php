@extends('layouts.app')

@section('titulo', 'Gestión de torneos')

@section('contenido')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Gestión de torneos</h1>
                <p class="mt-1 text-sm text-gray-500">Crea, edita y elimina torneos, y consulta sus inscripciones.</p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <form method="GET" action="{{ route('admin.torneos.index') }}" class="flex w-full max-w-md gap-2">
                    <label for="buscar" class="sr-only">Buscar torneos</label>
                    <input type="search" id="buscar" name="buscar" value="{{ $buscar }}"
                        placeholder="Buscar por nombre o juego..."
                        class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    <button type="submit"
                        class="inline-flex shrink-0 items-center gap-2 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Buscar
                    </button>
                </form>
                <a href="{{ route('admin.torneos.create') }}"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Crear torneo
                </a>
            </div>
        </div>

        @if ($torneos->isEmpty())
            <div class="rounded-xl border border-dashed border-gray-300 bg-white p-10 text-center">
                @if ($buscar !== '')
                    <p class="text-base font-semibold text-gray-900">No se encontraron torneos con esa búsqueda.</p>
                    <a href="{{ route('admin.torneos.index') }}"
                        class="mt-4 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-500">Limpiar búsqueda</a>
                @else
                    <p class="text-base font-semibold text-gray-900">Aún no hay torneos registrados.</p>
                    <a href="{{ route('admin.torneos.create') }}"
                        class="mt-4 inline-block rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                        Crear el primer torneo
                    </a>
                @endif
            </div>
        @else
            <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-3">Torneo</th>
                            <th class="px-4 py-3">Juego</th>
                            <th class="px-4 py-3">Fecha</th>
                            <th class="px-4 py-3">Cupo</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($torneos as $torneo)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $torneo->nombre }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $torneo->juego }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $torneo->fecha->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $torneo->totalInscritos() }}/{{ $torneo->cupo }}
                                </td>
                                <td class="px-4 py-3"><x-estado-torneo :torneo="$torneo" /></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.inscripciones.index', $torneo) }}"
                                            class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-100"
                                            title="Ver inscripciones">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                            </svg>
                                            Inscripciones
                                        </a>
                                        <a href="{{ route('admin.torneos.edit', $torneo) }}"
                                            class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1.5 text-xs font-medium text-indigo-600 hover:bg-indigo-50">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                            Editar
                                        </a>
                                        <form method="POST" action="{{ route('admin.torneos.destroy', $torneo) }}"
                                            onsubmit="return confirm('¿Seguro que quieres eliminar el torneo «{{ $torneo->nombre }}»? También se eliminarán sus inscripciones.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection