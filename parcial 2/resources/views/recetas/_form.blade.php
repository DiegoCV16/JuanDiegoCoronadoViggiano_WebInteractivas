@csrf
@if (isset($receta))
    @method('PUT')
@endif

<div>
    <label for="titulo" class="mb-1 block text-sm font-medium text-gray-700">Título</label>
    <input id="titulo" type="text" name="titulo" value="{{ old('titulo', $receta->titulo ?? '') }}" required
        class="w-full rounded border border-gray-300 px-3 py-2 focus:border-indigo-500 focus:outline-none">
    @error('titulo')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div>
        <label for="categoria" class="mb-1 block text-sm font-medium text-gray-700">Categoría</label>
        <select id="categoria" name="categoria" required
            class="w-full rounded border border-gray-300 px-3 py-2 focus:border-indigo-500 focus:outline-none">
            <option value="" disabled @selected(! old('categoria') && ! ($receta->categoria ?? false))>Selecciona una categoría</option>
            @foreach (\App\Models\Receta::CATEGORIAS as $opcion)
                <option value="{{ $opcion }}" @selected(old('categoria', $receta->categoria ?? '') === $opcion)>
                    {{ ucfirst($opcion) }}
                </option>
            @endforeach
        </select>
        @error('categoria')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="tiempo_minutos" class="mb-1 block text-sm font-medium text-gray-700">Tiempo en minutos</label>
        <input id="tiempo_minutos" type="number" name="tiempo_minutos" min="1"
            value="{{ old('tiempo_minutos', $receta->tiempo_minutos ?? '') }}" required
            class="w-full rounded border border-gray-300 px-3 py-2 focus:border-indigo-500 focus:outline-none">
        @error('tiempo_minutos')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="dificultad" class="mb-1 block text-sm font-medium text-gray-700">Dificultad</label>
        <select id="dificultad" name="dificultad" required
            class="w-full rounded border border-gray-300 px-3 py-2 focus:border-indigo-500 focus:outline-none">
            <option value="" disabled @selected(! old('dificultad') && ! ($receta->dificultad ?? false))>Selecciona una dificultad</option>
            @foreach (\App\Models\Receta::DIFICULTADES as $opcion)
                <option value="{{ $opcion }}" @selected(old('dificultad', $receta->dificultad ?? '') === $opcion)>
                    {{ ucfirst($opcion) }}
                </option>
            @endforeach
        </select>
        @error('dificultad')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div>
    <label for="ingredientes" class="mb-1 block text-sm font-medium text-gray-700">Ingredientes</label>
    <textarea id="ingredientes" name="ingredientes" rows="5" required
        class="w-full rounded border border-gray-300 px-3 py-2 focus:border-indigo-500 focus:outline-none"
        placeholder="Un ingrediente por línea">{{ old('ingredientes', $receta->ingredientes ?? '') }}</textarea>
    <p class="mt-1 text-xs text-gray-500">Escribe un ingrediente por línea</p>
    @error('ingredientes')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="pasos" class="mb-1 block text-sm font-medium text-gray-700">Pasos de preparación</label>
    <textarea id="pasos" name="pasos" rows="5" required
        class="w-full rounded border border-gray-300 px-3 py-2 focus:border-indigo-500 focus:outline-none"
        placeholder="Un paso por línea">{{ old('pasos', $receta->pasos ?? '') }}</textarea>
    <p class="mt-1 text-xs text-gray-500">Escribe un paso por línea</p>
    @error('pasos')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="nota" class="mb-1 block text-sm font-medium text-gray-700">Nota personal</label>
    <textarea id="nota" name="nota" rows="3"
        class="w-full rounded border border-gray-300 px-3 py-2 focus:border-indigo-500 focus:outline-none"
        placeholder="Opcional">{{ old('nota', $receta->nota ?? '') }}</textarea>
    @error('nota')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="flex items-center gap-3">
    <button type="submit"
        class="rounded bg-indigo-600 px-4 py-2 font-medium text-white hover:bg-indigo-500">
        {{ isset($receta) ? 'Actualizar receta' : 'Guardar receta' }}
    </button>
    <a href="{{ route('recetas.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
</div>