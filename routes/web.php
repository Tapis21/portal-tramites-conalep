<?php

use App\Http\Controllers\ProfileController;
use App\http\Controllers\ServicioSocialController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas para Servicio Social
    Route::resource('servicio-social', ServicioSocialController::class);

    // Rutas para subir reporte parcial
    Route::get('servicio-social/{id}/subir-reporte-parcial', [ServicioSocialController::class, 'mostrarFormularioReporteParcial'])->name('servicio-social.subir-reporte-parcial');
    Route::post('servicio-social/{id}/subir-reporte-parcial', [ServicioSocialController::class, 'subirReporteParcial'])->name('servicio-social.guardar-reporte-parcial');

    // Ruta para subir reporte final
    Route::get('servicio-social/{id}/subir-reporte-final', [ServicioSocialController::class, 'mostrarFormularioReporteFinal'])->name('servicio-social.subir-reporte-final');
    Route::post('servicio-social/{id}/subir-reporte-final', [ServicioSocialController::class, 'subirReporteFinal'])->name('servicio-social.guardar-reporte-final');
});

require __DIR__.'/auth.php';
