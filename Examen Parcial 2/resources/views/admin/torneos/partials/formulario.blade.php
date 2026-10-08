<div class="space-y-5">
    <div>
        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre *</label>
        <input id="nombre" name="nombre" type="text" value="{{ old('nombre', $torneo->nombre ?? '') }}" required
            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('nombre') border-red-300 @enderror">
        @error('nombre')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="juego" class="block text-sm font-medium text-gray-700">Juego o deporte *</label>
        <input id="juego" name="juego" type="text" value="{{ old('juego', $torneo->juego ?? '') }}" required
            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('juego') border-red-300 @enderror">
        @error('juego')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="fecha" class="block text-sm font-medium text-gray-700">Fecha *</label>
        <input id="fecha" name="fecha" type="date"
            value="{{ old('fecha', isset($torneo, $torneo->fecha) ? $torneo->fecha->format('Y-m-d') : '') }}" required
            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('fecha') border-red-300 @enderror">
        @error('fecha')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="cupo" class="block text-sm font-medium text-gray-700">Cupo * <span
                class="font-normal text-gray-500">(mínimo 2, máximo 100)</span></label>
        <input id="cupo" name="cupo" type="number" min="2" max="100"
            value="{{ old('cupo', $torneo->cupo ?? '16') }}" required
            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('cupo') border-red-300 @enderror">
        @error('cupo')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
        <textarea id="descripcion" name="descripcion" rows="4" maxlength="1000"
            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('descripcion') border-red-300 @enderror">{{ old('descripcion', $torneo->descripcion ?? '') }}</textarea>
        @error('descripcion')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="estado" class="block text-sm font-medium text-gray-700">Estado *</label>
        <select id="estado" name="estado" required
            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('estado') border-red-300 @enderror">
            <option value="abierto" @selected(old('estado', $torneo->estado ?? 'abierto') === 'abierto')>Abierto</option>
            <option value="cerrado" @selected(old('estado', $torneo->estado ?? 'abierto') === 'cerrado')>Cerrado</option>
        </select>
        @error('estado')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>