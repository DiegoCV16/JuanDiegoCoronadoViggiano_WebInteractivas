@extends('layout')

@section('titulo', 'Nueva receta')

@section('contenido')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Nueva receta</h1>
    </div>

    <form method="POST" action="{{ route('recetas.store') }}"
        class="max-w-3xl space-y-4 rounded border border-gray-200 bg-white p-6">
        @include('recetas._form')
    </form>
@endsection