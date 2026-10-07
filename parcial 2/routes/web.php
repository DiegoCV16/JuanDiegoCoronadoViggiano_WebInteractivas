<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RecetaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect(auth()->check() ? route('recetas.index') : route('login'));
});

Route::middleware('guest')->group(function (): void {
    Route::get('/registro', [AuthController::class, 'mostrarRegistro'])->name('registro');
    Route::post('/registro', [AuthController::class, 'registrar']);
    Route::get('/login', [AuthController::class, 'mostrarLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'autenticar']);
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'cerrarSesion'])->name('logout');
    Route::resource('recetas', RecetaController::class);
});
