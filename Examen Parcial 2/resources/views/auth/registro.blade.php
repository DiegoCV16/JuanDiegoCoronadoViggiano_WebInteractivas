@extends('layouts.app')

@section('titulo', 'Crear cuenta')

@section('contenido')
    <div class="mx-auto mt-8 w-full max-w-md rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
        <h1 class="text-xl font-bold text-gray-900">Crear cuenta</h1>
        <p class="mt-1 text-sm text-gray-500">
            Las cuentas se crean siempre con el rol <strong>jugador</strong>.
        </p>

        <form method="POST" action="{{ route('registro.store') }}" class="mt-6 space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                    autocomplete="name"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('name') border-red-300 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                    autocomplete="email"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('email') border-red-300 @enderror">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                <input id="password" name="password" type="password" required autocomplete="new-password"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('password') border-red-300 @enderror">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar contraseña</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                    autocomplete="new-password"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <button type="submit"
                    class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                    Crear cuenta
                </button>
            </div>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500">
            ¿Ya tienes cuenta?
            <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Inicia sesión</a>
        </p>
    </div>
@endsection