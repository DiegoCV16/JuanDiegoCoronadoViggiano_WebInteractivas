@extends('layout')

@section('titulo', 'Registro')

@section('contenido')
    <div class="mx-auto max-w-md">
        <h1 class="mb-6 text-2xl font-semibold">Crear una cuenta</h1>

        <form method="POST" action="{{ route('registro') }}" class="space-y-4 rounded border border-gray-200 bg-white p-6">
            @csrf

            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-gray-700">Correo electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full rounded border border-gray-300 px-3 py-2 focus:border-indigo-500 focus:outline-none">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-medium text-gray-700">Contraseña</label>
                <input id="password" type="password" name="password" required
                    class="w-full rounded border border-gray-300 px-3 py-2 focus:border-indigo-500 focus:outline-none">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="mb-1 block text-sm font-medium text-gray-700">Confirmar contraseña</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    class="w-full rounded border border-gray-300 px-3 py-2 focus:border-indigo-500 focus:outline-none">
            </div>

            <button type="submit"
                class="w-full rounded bg-indigo-600 px-4 py-2 font-medium text-white hover:bg-indigo-500">
                Regístrate
            </button>
        </form>

        <p class="mt-4 text-center text-sm text-gray-600">
            ¿Ya tienes cuenta?
            <a href="{{ route('login') }}" class="text-indigo-600 underline">Inicia sesión</a>
        </p>
    </div>
@endsection