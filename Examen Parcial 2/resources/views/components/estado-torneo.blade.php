@props(['torneo'])

@php
    $estados = match (true) {
        $torneo->estado === \App\Models\Torneo::ESTADO_CERRADO => ['bg-red-100 text-red-700', 'Cerrado', 'El administrador lo marcó como cerrado'],
        $torneo->estaPasado() => ['bg-gray-100 text-gray-600', 'Finalizado', 'La fecha del torneo ya pasó'],
        $torneo->estaLleno() => ['bg-amber-100 text-amber-700', 'Lleno', 'Las plazas están completas'],
        default => ['bg-green-100 text-green-700', 'Abierto', 'Torneo abierto'],
    };
@endphp

<span class="inline-flex shrink-0 items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $estados[0] }}"
    title="{{ $estados[2] }}">
    {{ $estados[1] }}
</span>