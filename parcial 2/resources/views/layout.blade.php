<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Recetario Casero')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 text-gray-900 antialiased">
    <header class="bg-white shadow-sm">
        <nav class="mx-auto flex max-w-5xl items-center justify-between px-4 py-4">
            <a href="{{ route('recetas.index') }}" class="text-lg font-semibold">Recetario Casero</a>
            @auth
                <div class="flex items-center gap-4 text-sm">
                    <a href="{{ route('recetas.index') }}">Mis recetas</a>
                    <a href="{{ route('recetas.create') }}">Nueva receta</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-red-600 hover:underline">Cerrar sesión</button>
                    </form>
                </div>
            @else
                <div class="flex items-center gap-4 text-sm">
                    <a href="{{ route('login') }}">Iniciar sesión</a>
                    <a href="{{ route('registro') }}" class="rounded bg-indigo-600 px-3 py-2 font-medium text-white hover:bg-indigo-500">Registrarse</a>
                </div>
            @endauth
        </nav>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-8">
        @if (session('exito'))
            <div class="mb-6 rounded border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                {{ session('exito') }}
            </div>
        @endif

        @yield('contenido')
    </main>
</body>
</html>