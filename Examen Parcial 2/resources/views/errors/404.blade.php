<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Página no encontrada · Sistema de Torneos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-gray-50 px-4 text-gray-900 antialiased">
    <div class="w-full max-w-md text-center">
        <p class="text-6xl font-black text-indigo-600">404</p>
        <h1 class="mt-4 text-xl font-bold text-gray-900">Página no encontrada</h1>
        <p class="mt-2 text-sm text-gray-600">
            El recurso que buscas no existe o ya no está disponible.
        </p>
        <a href="{{ url('/') }}"
            class="mt-6 inline-block rounded-md bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-indigo-500">
            Volver al inicio
        </a>
    </div>
</body>
</html>