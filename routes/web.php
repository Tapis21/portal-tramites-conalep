<?php

use App\Http\Controllers\ProfileController;
use App\http\Controllers\ServicioSocialController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SolicitudServicioSocialController;

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

    // Rutas para Solicitud de Servicio Social
    Route::get('/solicitud-servicio-social', [SolicitudServicioSocialController::class, 'create'])->name('solicitud-servicio-social.create');
    Route::post('/solicitud-servicio-social', [SolicitudServicioSocialController::class, 'store'])->name('solicitud-servicio-social.store');

    // Ruta para eliminar un documento específico de un trámite
    Route::delete('servicio-social/{id}/eliminar-documento/{tipo}', [ServicioSocialController::class, 'eliminarDocumento'])->name('servicio-social.eliminar-documento');
    Route::delete('servicio-social/{id}/eliminar-informe/{tipo}', [ServicioSocialController::class, 'eliminarInforme'])->name('servicio-social.eliminar-informe');

    // Rutas para Servicio Social
    Route::resource('servicio-social', ServicioSocialController::class);

    // Rutas para subir reporte parcial
    Route::get('servicio-social/{id}/subir-reporte-parcial', [ServicioSocialController::class, 'mostrarFormularioReporteParcial'])->name('servicio-social.subir-reporte-parcial');
    Route::post('servicio-social/{id}/subir-reporte-parcial', [ServicioSocialController::class, 'subirReporteParcial'])->name('servicio-social.guardar-reporte-parcial');

    // Ruta para subir reporte final
    Route::get('servicio-social/{id}/subir-reporte-final', [ServicioSocialController::class, 'mostrarFormularioReporteFinal'])->name('servicio-social.subir-reporte-final');
    Route::post('servicio-social/{id}/subir-reporte-final', [ServicioSocialController::class, 'subirReporteFinal'])->name('servicio-social.guardar-reporte-final');

    // Ruta para subir solicitud de modalidad
    Route::get('servicio-social/{id}/subir-solicitud', [ServicioSocialController::class, 'mostrarFormularioSolicitud'])->name('servicio-social.subir-solicitud');
    Route::post('servicio-social/{id}/subir-solicitud', [ServicioSocialController::class, 'subirSolicitud'])->name('servicio-social.guardar-solicitud');

    // Ruta para subir modalidad
    Route::get('servicio-social/{id}/subir-modalidad', [ServicioSocialController::class, 'mostrarFormularioModalidad'])->name('servicio-social.subir-modalidad');
    Route::post('servicio-social/{id}/subir-modalidad', [ServicioSocialController::class, 'subirModalidad'])->name('servicio-social.guardar-modalidad');

    // Ruta para subir carta de presentación
    Route::get('servicio-social/{id}/subir-carta-presentacion', [ServicioSocialController::class, 'mostrarFormularioCartaPresentacion'])->name('servicio-social.subir-carta-presentacion');
    Route::post('servicio-social/{id}/subir-carta-presentacion', [ServicioSocialController::class, 'subirCartaPresentacion'])->name('servicio-social.guardar-carta-presentacion');

    // Ruta para subir carta de aceptación
    Route::get('servicio-social/{id}/subir-carta-aceptacion', [ServicioSocialController::class, 'mostrarFormularioCartaAceptacion'])->name('servicio-social.subir-carta-aceptacion');
    Route::post('servicio-social/{id}/subir-carta-aceptacion', [ServicioSocialController::class, 'subirCartaAceptacion'])->name('servicio-social.guardar-carta-aceptacion');

    // Ruta para subir evaluación
    Route::get('servicio-social/{id}/subir-evaluacion', [ServicioSocialController::class, 'mostrarFormularioEvaluacion'])->name('servicio-social.subir-evaluacion');
    Route::post('servicio-social/{id}/subir-evaluacion', [ServicioSocialController::class, 'subirEvaluacion'])->name('servicio-social.guardar-evaluacion');

    // Ruta para subir liberación
    Route::get('servicio-social/{id}/subir-liberacion', [ServicioSocialController::class, 'mostrarFormularioLiberacion'])->name('servicio-social.subir-liberacion');
    Route::post('servicio-social/{id}/subir-liberacion', [ServicioSocialController::class, 'subirLiberacion'])->name('servicio-social.guardar-liberacion');
});

require __DIR__.'/auth.php';
