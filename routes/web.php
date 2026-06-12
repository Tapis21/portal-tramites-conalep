<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServicioSocialController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SolicitudServicioSocialController;

use App\Http\Controllers\PracticaController;
use App\Http\Controllers\SolicitudPracticaController;

use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ==================== SERVICIO SOCIAL ====================
    
    // Recurso base (index, create, store, show, edit, update, destroy)
    Route::resource('servicio-social', ServicioSocialController::class);
    
    // Subir reporte parcial (Primer Informe)
    Route::get('servicio-social/{id}/subir-reporte-parcial', [ServicioSocialController::class, 'mostrarFormularioReporteParcial'])->name('servicio-social.subir-reporte-parcial');
    Route::post('servicio-social/{id}/subir-reporte-parcial', [ServicioSocialController::class, 'subirReporteParcial'])->name('servicio-social.guardar-reporte-parcial');
    
    // Subir reporte final (Segundo Informe)
    Route::get('servicio-social/{id}/subir-reporte-final', [ServicioSocialController::class, 'mostrarFormularioReporteFinal'])->name('servicio-social.subir-reporte-final');
    Route::post('servicio-social/{id}/subir-reporte-final', [ServicioSocialController::class, 'subirReporteFinal'])->name('servicio-social.guardar-reporte-final');
    
    // Subir solicitud
    Route::get('servicio-social/{id}/subir-solicitud', [ServicioSocialController::class, 'mostrarFormularioSolicitud'])->name('servicio-social.subir-solicitud');
    Route::post('servicio-social/{id}/subir-solicitud', [ServicioSocialController::class, 'subirSolicitud'])->name('servicio-social.guardar-solicitud');

    Route::get('/servicio-social/{id}/word', [ServicioSocialController::class, 'descargarWordRelleno'])->name('servicio-social.word');

    // Route::get('/solicitud-servicio-social/{id}/pdf', [SolicitudPDFController::class, 'download'])->name('solicitud-servicio-social.pdf');

    Route::get('/servicio-social/{servicioSocial}/descargar-plantilla-preliminar', [ServicioSocialController::class, 'descargarPlantillaPreliminar'])
    ->name('servicio-social.descargar-plantilla-preliminar');
    
    // Subir modalidad
    Route::get('servicio-social/{id}/subir-modalidad', [ServicioSocialController::class, 'mostrarFormularioModalidad'])->name('servicio-social.subir-modalidad');
    Route::post('servicio-social/{id}/subir-modalidad', [ServicioSocialController::class, 'subirModalidad'])->name('servicio-social.guardar-modalidad');
    
    // Subir carta de presentación
    Route::get('servicio-social/{id}/subir-carta-presentacion', [ServicioSocialController::class, 'mostrarFormularioCartaPresentacion'])->name('servicio-social.subir-carta-presentacion');
    Route::post('servicio-social/{id}/subir-carta-presentacion', [ServicioSocialController::class, 'subirCartaPresentacion'])->name('servicio-social.guardar-carta-presentacion');
    
    // Subir carta de aceptación
    Route::get('servicio-social/{id}/subir-carta-aceptacion', [ServicioSocialController::class, 'mostrarFormularioCartaAceptacion'])->name('servicio-social.subir-carta-aceptacion');
    Route::post('servicio-social/{id}/subir-carta-aceptacion', [ServicioSocialController::class, 'subirCartaAceptacion'])->name('servicio-social.guardar-carta-aceptacion');
    
    // Subir evaluación
    Route::get('servicio-social/{id}/subir-evaluacion', [ServicioSocialController::class, 'mostrarFormularioEvaluacion'])->name('servicio-social.subir-evaluacion');
    Route::post('servicio-social/{id}/subir-evaluacion', [ServicioSocialController::class, 'subirEvaluacion'])->name('servicio-social.guardar-evaluacion');
    
    // Subir liberación
    Route::get('servicio-social/{id}/subir-liberacion', [ServicioSocialController::class, 'mostrarFormularioLiberacion'])->name('servicio-social.subir-liberacion');
    Route::post('servicio-social/{id}/subir-liberacion', [ServicioSocialController::class, 'subirLiberacion'])->name('servicio-social.guardar-liberacion');
    
    // Eliminar documentos e informes
    Route::delete('servicio-social/{id}/eliminar-documento/{tipo}', [ServicioSocialController::class, 'eliminarDocumento'])->name('servicio-social.eliminar-documento');
    Route::delete('servicio-social/{id}/eliminar-informe/{tipo}', [ServicioSocialController::class, 'eliminarInforme'])->name('servicio-social.eliminar-informe');
    
    // Solicitud de Servicio Social
    Route::get('/solicitud-servicio-social', [SolicitudServicioSocialController::class, 'create'])->name('solicitud-servicio-social.create');
    Route::post('/solicitud-servicio-social', [SolicitudServicioSocialController::class, 'store'])->name('solicitud-servicio-social.store');
    
    
    // ==================== PRÁCTICAS PROFESIONALES ====================
    
    // Recurso base (index, create, store, show, edit, update, destroy)
    Route::resource('practicas', PracticaController::class);
    
    // Subir reporte parcial (Primer Informe - 180h)
    Route::get('practicas/{id}/subir-reporte-parcial', [PracticaController::class, 'mostrarFormularioReporteParcial'])->name('practicas.subir-reporte-parcial');
    Route::post('practicas/{id}/subir-reporte-parcial', [PracticaController::class, 'subirReporteParcial'])->name('practicas.guardar-reporte-parcial');
    
    // Subir reporte final (Segundo Informe - 360h)
    Route::get('practicas/{id}/subir-reporte-final', [PracticaController::class, 'mostrarFormularioReporteFinal'])->name('practicas.subir-reporte-final');
    Route::post('practicas/{id}/subir-reporte-final', [PracticaController::class, 'subirReporteFinal'])->name('practicas.guardar-reporte-final');
    
    // Subir solicitud
    Route::get('practicas/{id}/subir-solicitud', [PracticaController::class, 'mostrarFormularioSolicitud'])->name('practicas.subir-solicitud');
    Route::post('practicas/{id}/subir-solicitud', [PracticaController::class, 'subirSolicitud'])->name('practicas.guardar-solicitud');
    
    // Subir modalidad
    Route::get('practicas/{id}/subir-modalidad', [PracticaController::class, 'mostrarFormularioModalidad'])->name('practicas.subir-modalidad');
    Route::post('practicas/{id}/subir-modalidad', [PracticaController::class, 'subirModalidad'])->name('practicas.guardar-modalidad');
    
    // Subir carta de presentación
    Route::get('practicas/{id}/subir-carta-presentacion', [PracticaController::class, 'mostrarFormularioCartaPresentacion'])->name('practicas.subir-carta-presentacion');
    Route::post('practicas/{id}/subir-carta-presentacion', [PracticaController::class, 'subirCartaPresentacion'])->name('practicas.guardar-carta-presentacion');
    
    // Subir carta de aceptación
    Route::get('practicas/{id}/subir-carta-aceptacion', [PracticaController::class, 'mostrarFormularioCartaAceptacion'])->name('practicas.subir-carta-aceptacion');
    Route::post('practicas/{id}/subir-carta-aceptacion', [PracticaController::class, 'subirCartaAceptacion'])->name('practicas.guardar-carta-aceptacion');
    
    // Subir evaluación
    Route::get('practicas/{id}/subir-evaluacion', [PracticaController::class, 'mostrarFormularioEvaluacion'])->name('practicas.subir-evaluacion');
    Route::post('practicas/{id}/subir-evaluacion', [PracticaController::class, 'subirEvaluacion'])->name('practicas.guardar-evaluacion');
    
    // Subir liberación
    Route::get('practicas/{id}/subir-liberacion', [PracticaController::class, 'mostrarFormularioLiberacion'])->name('practicas.subir-liberacion');
    Route::post('practicas/{id}/subir-liberacion', [PracticaController::class, 'subirLiberacion'])->name('practicas.guardar-liberacion');
    
    // Eliminar documentos e informes
    Route::delete('practicas/{id}/eliminar-documento/{tipo}', [PracticaController::class, 'eliminarDocumento'])->name('practicas.eliminar-documento');
    Route::delete('practicas/{id}/eliminar-informe/{tipo}', [PracticaController::class, 'eliminarInforme'])->name('practicas.eliminar-informe');
    
    // Solicitud de Prácticas Profesionales
    Route::get('/solicitud-practicas', [SolicitudPracticaController::class, 'create'])->name('solicitud-practicas.create');
    Route::post('/solicitud-practicas', [SolicitudPracticaController::class, 'store'])->name('solicitud-practicas.store');
});

require __DIR__.'/auth.php';