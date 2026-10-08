<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Torneos') · Sistema de Torneos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
    <header class="border-b border-gray-200 bg-white">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-x-6 gap-y-2 px-4 py-3 sm:px-6">
            <a href="{{ route('torneos.index') }}" class="flex items-center gap-2 text-lg font-bold text-gray-900">
                <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972" />
                </svg>
                Sistema de Torneos
            </a>

            <nav class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm font-medium text-gray-600">
                <a href="{{ route('torneos.index') }}" class="hover:text-indigo-600">Torneos</a>
                @auth
                    <a href="{{ route('mis-torneos.index') }}" class="hover:text-indigo-600">Mis torneos</a>
                @endauth
                @if (auth()->user()?->esAdministrador())
                    <a href="{{ route('admin.torneos.index') }}" class="hover:text-indigo-600">Gestión</a>
                @endif

                @guest
                    <span class="hidden text-gray-300 sm:inline">·</span>
                    <a href="{{ route('login') }}" class="hover:text-indigo-600">Iniciar sesión</a>
                    <a href="{{ route('registro.create') }}"
                        class="rounded-md border border-indigo-600 px-3 py-1 text-indigo-600 hover:bg-indigo-50">Registrarse</a>
                @endguest

                @auth
                    <span class="hidden text-gray-300 sm:inline">·</span>
                    <span class="text-gray-500">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-red-600">Cerrar sesión</button>
                    </form>
                @endauth
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
        @include('partials.flash')
        @yield('contenido')
    </main>

    <footer class="border-t border-gray-200 bg-white">
        <div class="mx-auto max-w-6xl px-4 py-6 text-center text-xs text-gray-500 sm:px-6">
            Sistema de Gestión de Torneos · Segundo Parcial
        </div>
    </footer>
</body>
</html>