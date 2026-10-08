@extends('layouts.app')

@section('titulo', 'Inscripciones de '.$torneo->nombre)

@section('contenido')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <a href="{{ route('admin.torneos.index') }}"
                    class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    &larr; Volver a la gestión de torneos
                </a>
                <h1 class="mt-2 text-2xl font-bold text-gray-900">{{ $torneo->nombre }}</h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $torneo->juego }} · {{ $torneo->fecha->format('d/m/Y') }} ·
                    <x-plazas :torneo="$torneo" />
                </p>
            </div>
            <x-estado-torneo :torneo="$torneo" />
        </div>

        @if ($torneo->inscripciones->isEmpty())
            <div class="rounded-xl border border-dashed border-gray-300 bg-white p-10 text-center">
                <p class="text-base font-semibold text-gray-900">Este torneo aún no tiene inscripciones.</p>
                <p class="mt-1 text-sm text-gray-500">Cuando los jugadores se inscriban, aparecerán aquí.</p>
            </div>
        @else
            <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-3">Jugador</th>
                            <th class="px-4 py-3">Correo electrónico</th>
                            <th class="px-4 py-3">Fecha de inscripción</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($torneo->inscripciones as $inscripcion)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $inscripcion->usuario->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $inscripcion->usuario->email }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $inscripcion->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    <form method="POST"
                                        action="{{ route('admin.inscripciones.destroy', $inscripcion) }}"
                                        class="flex justify-end"
                                        onsubmit="return confirm('¿Seguro que quieres dar de baja la inscripción de {{ $inscripcion->usuario->name }}? La plaza quedará libre.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                            Dar de baja
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection