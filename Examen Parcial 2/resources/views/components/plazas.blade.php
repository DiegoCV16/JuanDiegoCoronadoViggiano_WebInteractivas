@props(['torneo'])

<span>
    <span class="font-semibold text-gray-800">{{ $torneo->totalInscritos() }}/{{ $torneo->cupo }}</span>
    <span class="text-gray-500">plazas</span>
    @if ($torneo->plazasDisponibles() > 0)
        <span class="font-medium text-green-600">· {{ $torneo->plazasDisponibles() }} disponibles</span>
    @else
        <span class="font-medium text-amber-600">· sin plazas</span>
    @endif
</span>