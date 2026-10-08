<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\RegistroController;
use App\Http\Controllers\Auth\SesionController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\MisTorneosController;
use App\Http\Controllers\TorneoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Torneos (públicos)
|--------------------------------------------------------------------------
*/

Route::get('/', [TorneoController::class, 'index'])->name('torneos.index');
Route::get('/torneos/{torneo}', [TorneoController::class, 'show'])->name('torneos.show');

/*
|--------------------------------------------------------------------------
| Autenticación
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/registro', [RegistroController::class, 'create'])->name('registro.create');
    Route::post('/registro', [RegistroController::class, 'store'])->name('registro.store');
    Route::get('/login', [SesionController::class, 'create'])->name('login');
    Route::post('/login', [SesionController::class, 'store'])->name('login.store');
});

Route::post('/logout', [SesionController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Jugador autenticado
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/mis-torneos', [MisTorneosController::class, 'index'])->name('mis-torneos.index');
    Route::post('/torneos/{torneo}/inscribirse', [InscripcionController::class, 'store'])->name('torneos.inscribirse');
    Route::delete('/mis-torneos/{torneo}/cancelar', [InscripcionController::class, 'cancelar'])->name('mis-torneos.cancelar');
});

/*
|--------------------------------------------------------------------------
| Administrador
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('torneos', Admin\TorneoController::class)->except('show');
    Route::get('torneos/{torneo}/inscripciones', [Admin\InscripcionController::class, 'index'])->name('inscripciones.index');
    Route::delete('inscripciones/{inscripcion}', [Admin\InscripcionController::class, 'destroy'])->name('inscripciones.destroy');
});
