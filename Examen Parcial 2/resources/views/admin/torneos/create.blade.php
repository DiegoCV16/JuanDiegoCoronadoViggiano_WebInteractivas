@extends('layouts.app')

@section('titulo', 'Crear torneo')

@section('contenido')
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('admin.torneos.index') }}"
            class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-500">
            &larr; Volver a la gestión de torneos
        </a>

        <div class="mt-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
            <h1 class="text-xl font-bold text-gray-900">Crear torneo</h1>
            <p class="mt-1 text-sm text-gray-500">Todos los campos marcados con * son obligatorios.</p>

            <form method="POST" action="{{ route('admin.torneos.store') }}" class="mt-6 space-y-5">
                @csrf
                @include('admin.torneos.partials.formulario', ['torneo' => null])

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                        Crear torneo
                    </button>
                    <a href="{{ route('admin.torneos.index') }}"
                        class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection