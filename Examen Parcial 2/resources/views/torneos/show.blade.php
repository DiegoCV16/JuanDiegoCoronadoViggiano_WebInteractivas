@extends('layouts.app')

@section('titulo', $torneo->nombre)

@section('contenido')
    <div class="mx-auto max-w-4xl space-y-6">
        <a href="{{ route('torneos.index') }}"
            class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-500">
            &larr; Volver a los torneos
        </a>

        <article class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $torneo->nombre }}</h1>
                        <p class="mt-1 text-base font-medium text-indigo-600">{{ $torneo->juego }}</p>
                    </div>
                    <x-estado-torneo :torneo="$torneo" />
                </div>
            </div>

            <dl class="grid gap-4 p-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg bg-gray-50 p-4">
                    <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Fecha</dt>
                    <dd class="mt-1 text-base font-semibold text-gray-900">{{ $torneo->fecha->format('d/m/Y') }}</dd>
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Cupo</dt>
                    <dd class="mt-1 text-base font-semibold text-gray-900">{{ $torneo->cupo }} jugadores</dd>
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Inscritos</dt>
                    <dd class="mt-1 text-base font-semibold text-gray-900">{{ $torneo->totalInscritos() }}</dd>
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Plazas restantes</dt>
                    <dd class="mt-1 text-base font-semibold text-gray-900">{{ $torneo->plazasDisponibles() }}</dd>
                </div>
            </dl>

            @if ($torneo->descripcion)
                <div class="border-t border-gray-100 px-6 py-4">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Descripción</h2>
                    <p class="mt-2 text-sm leading-relaxed text-gray-700">{{ $torneo->descripcion }}</p>
                </div>
            @endif

            <div class="border-t border-gray-100 px-6 py-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Participantes</h2>
                    <span class="text-sm text-gray-500">
                        <x-plazas :torneo="$torneo" />
                    </span>
                </div>

                @if ($torneo->inscripciones->isEmpty())
                    <p class="mt-2 text-sm text-gray-500">Aún no hay jugadores inscritos en este torneo.</p>
                @else
                    <ul class="mt-3 grid gap-2 sm:grid-cols-2">
                        @foreach ($torneo->inscripciones as $inscripcion)
                            <li class="flex items-center gap-3 rounded-lg border border-gray-100 bg-gray-50 px-3 py-2">
                                <span
                                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700">
                                    {{ strtoupper(substr($inscripcion->usuario->name, 0, 1)) }}
                                </span>
                                <span class="truncate text-sm text-gray-800">{{ $inscripcion->usuario->name }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </article>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            @guest
                <p class="text-sm text-gray-600">Para poder inscribirte en un torneo necesitas una cuenta de jugador.</p>
                <div class="mt-4 flex gap-3">
                    <a href="{{ route('login') }}"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">Iniciar sesión</a>
                    <a href="{{ route('registro.create') }}"
                        class="rounded-md border border-indigo-600 px-4 py-2 text-sm font-medium text-indigo-600 hover:bg-indigo-50">Registrarme</a>
                </div>
            @endguest

            @auth
                @if ($inscripcionPropia)
                    <div class="flex flex-wrap items-center gap-3">
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full bg-sky-100 px-3 py-1 text-sm font-semibold text-sky-700">
                            Ya estás inscrito
                        </span>
                        <a href="{{ route('mis-torneos.index') }}"
                            class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Ver mis torneos</a>
                    </div>
                @elseif ($torneo->estado === \App\Models\Torneo::ESTADO_CERRADO)
                    <p class="text-sm text-gray-600">No es posible inscribirse: el administrador marcó este torneo como
                        <strong>cerrado</strong>.
                    </p>
                @elseif ($torneo->estaPasado())
                    <p class="text-sm text-gray-600">No es posible inscribirse: la <strong>fecha</strong> del torneo ya
                        pasó.</p>
                @elseif ($torneo->estaLleno())
                    <p class="text-sm text-gray-600">No es posible inscribirse: el torneo ya está <strong>lleno</strong>,
                        no quedan plazas disponibles.</p>
                @else
                    <div class="flex flex-wrap items-center gap-3">
                        <p class="text-sm text-gray-600">¿Quieres participar? Reserva tu plaza.</p>
                        <form method="POST" action="{{ route('torneos.inscribirse', $torneo) }}">
                            @csrf
                            <button type="submit"
                                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                                Inscribirme
                            </button>
                        </form>
                    </div>
                @endif
            @endauth
        </div>
    </div>
@endsection