<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServicioSocialController;
use App\Http\Controllers\DocumentoStatusController;
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
    
    Route::resource('servicio-social', ServicioSocialController::class);
    
    Route::get('servicio-social/{id}/subir-reporte-parcial', [ServicioSocialController::class, 'mostrarFormularioReporteParcial'])->name('servicio-social.subir-reporte-parcial');
    Route::post('servicio-social/{id}/subir-reporte-parcial', [ServicioSocialController::class, 'subirReporteParcial'])->name('servicio-social.guardar-reporte-parcial');
    
    Route::get('servicio-social/{id}/subir-reporte-final', [ServicioSocialController::class, 'mostrarFormularioReporteFinal'])->name('servicio-social.subir-reporte-final');
    Route::post('servicio-social/{id}/subir-reporte-final', [ServicioSocialController::class, 'subirReporteFinal'])->name('servicio-social.guardar-reporte-final');
    
    Route::get('servicio-social/{id}/subir-solicitud', [ServicioSocialController::class, 'mostrarFormularioSolicitud'])->name('servicio-social.subir-solicitud');
    Route::post('servicio-social/{id}/subir-solicitud', [ServicioSocialController::class, 'subirSolicitud'])->name('servicio-social.guardar-solicitud');

    Route::get('/servicio-social/{id}/word', [ServicioSocialController::class, 'descargarWordRelleno'])->name('servicio-social.word');
    
    Route::get('servicio-social/{id}/subir-modalidad', [ServicioSocialController::class, 'mostrarFormularioModalidad'])->name('servicio-social.subir-modalidad');
    Route::post('servicio-social/{id}/subir-modalidad', [ServicioSocialController::class, 'subirModalidad'])->name('servicio-social.guardar-modalidad');
    
    Route::get('servicio-social/{id}/subir-carta-presentacion', [ServicioSocialController::class, 'mostrarFormularioCartaPresentacion'])->name('servicio-social.subir-carta-presentacion');
    Route::post('servicio-social/{id}/subir-carta-presentacion', [ServicioSocialController::class, 'subirCartaPresentacion'])->name('servicio-social.guardar-carta-presentacion');
    
    Route::get('servicio-social/{id}/subir-carta-aceptacion', [ServicioSocialController::class, 'mostrarFormularioCartaAceptacion'])->name('servicio-social.subir-carta-aceptacion');
    Route::post('servicio-social/{id}/subir-carta-aceptacion', [ServicioSocialController::class, 'subirCartaAceptacion'])->name('servicio-social.guardar-carta-aceptacion');
    
    Route::get('servicio-social/{id}/subir-evaluacion', [ServicioSocialController::class, 'mostrarFormularioEvaluacion'])->name('servicio-social.subir-evaluacion');
    Route::post('servicio-social/{id}/subir-evaluacion', [ServicioSocialController::class, 'subirEvaluacion'])->name('servicio-social.guardar-evaluacion');
    
    Route::get('servicio-social/{id}/subir-liberacion', [ServicioSocialController::class, 'mostrarFormularioLiberacion'])->name('servicio-social.subir-liberacion');
    Route::post('servicio-social/{id}/subir-liberacion', [ServicioSocialController::class, 'subirLiberacion'])->name('servicio-social.guardar-liberacion');
    
    Route::delete('servicio-social/{id}/eliminar-documento/{tipo}', [ServicioSocialController::class, 'eliminarDocumento'])->name('servicio-social.eliminar-documento');
    Route::delete('servicio-social/{id}/eliminar-informe/{tipo}', [ServicioSocialController::class, 'eliminarInforme'])->name('servicio-social.eliminar-informe');
    
    Route::get('/solicitud-servicio-social', [SolicitudServicioSocialController::class, 'create'])->name('solicitud-servicio-social.create');
    Route::post('/solicitud-servicio-social', [SolicitudServicioSocialController::class, 'store'])->name('solicitud-servicio-social.store');
    
    
    // ==================== PRÁCTICAS PROFESIONALES ====================
    
    Route::resource('practicas', PracticaController::class);
    
    Route::get('practicas/{id}/subir-reporte-parcial', [PracticaController::class, 'mostrarFormularioReporteParcial'])->name('practicas.subir-reporte-parcial');
    Route::post('practicas/{id}/subir-reporte-parcial', [PracticaController::class, 'subirReporteParcial'])->name('practicas.guardar-reporte-parcial');
    
    Route::get('practicas/{id}/subir-reporte-final', [PracticaController::class, 'mostrarFormularioReporteFinal'])->name('practicas.subir-reporte-final');
    Route::post('practicas/{id}/subir-reporte-final', [PracticaController::class, 'subirReporteFinal'])->name('practicas.guardar-reporte-final');
    
    Route::get('practicas/{id}/subir-solicitud', [PracticaController::class, 'mostrarFormularioSolicitud'])->name('practicas.subir-solicitud');
    Route::post('practicas/{id}/subir-solicitud', [PracticaController::class, 'subirSolicitud'])->name('practicas.guardar-solicitud');

    Route::get('/practicas/{id}/word', [PracticaController::class, 'descargarWordRelleno'])->name('practicas.word');
    
    Route::get('practicas/{id}/subir-modalidad', [PracticaController::class, 'mostrarFormularioModalidad'])->name('practicas.subir-modalidad');
    Route::post('practicas/{id}/subir-modalidad', [PracticaController::class, 'subirModalidad'])->name('practicas.guardar-modalidad');
    
    Route::get('practicas/{id}/subir-carta-presentacion', [PracticaController::class, 'mostrarFormularioCartaPresentacion'])->name('practicas.subir-carta-presentacion');
    Route::post('practicas/{id}/subir-carta-presentacion', [PracticaController::class, 'subirCartaPresentacion'])->name('practicas.guardar-carta-presentacion');
    
    Route::get('practicas/{id}/subir-carta-aceptacion', [PracticaController::class, 'mostrarFormularioCartaAceptacion'])->name('practicas.subir-carta-aceptacion');
    Route::post('practicas/{id}/subir-carta-aceptacion', [PracticaController::class, 'subirCartaAceptacion'])->name('practicas.guardar-carta-aceptacion');
    
    Route::get('practicas/{id}/subir-evaluacion', [PracticaController::class, 'mostrarFormularioEvaluacion'])->name('practicas.subir-evaluacion');
    Route::post('practicas/{id}/subir-evaluacion', [PracticaController::class, 'subirEvaluacion'])->name('practicas.guardar-evaluacion');
    
    Route::get('practicas/{id}/subir-liberacion', [PracticaController::class, 'mostrarFormularioLiberacion'])->name('practicas.subir-liberacion');
    Route::post('practicas/{id}/subir-liberacion', [PracticaController::class, 'subirLiberacion'])->name('practicas.guardar-liberacion');
    
    Route::delete('practicas/{id}/eliminar-documento/{tipo}', [PracticaController::class, 'eliminarDocumento'])->name('practicas.eliminar-documento');
    Route::delete('practicas/{id}/eliminar-informe/{tipo}', [PracticaController::class, 'eliminarInforme'])->name('practicas.eliminar-informe');
    
    Route::get('/solicitud-practicas', [SolicitudPracticaController::class, 'create'])->name('solicitud-practicas.create');
    Route::post('/solicitud-practicas', [SolicitudPracticaController::class, 'store'])->name('solicitud-practicas.store');

    Route::post('/comentarios/marcar-leidos', [App\Http\Controllers\ComentarioController::class, 'marcarLeidos'])->name('comentarios.marcar-leidos')->middleware('auth');

    // ==================== RUTAS ADMIN PARA INFORMES (SERVICIO SOCIAL) ====================
    Route::post('/admin/servicio-social/{id}/validar-reporte-parcial', [ServicioSocialController::class, 'validarReporteParcial'])->name('admin.servicio-social.validar-reporte-parcial');
    Route::post('/admin/servicio-social/{id}/validar-ventanilla-reporte-parcial', [ServicioSocialController::class, 'validarVentanillaReporteParcial'])->name('admin.servicio-social.validar-ventanilla-reporte-parcial');
    Route::post('/admin/servicio-social/{id}/rechazar-reporte-parcial', [ServicioSocialController::class, 'rechazarReporteParcial'])->name('admin.servicio-social.rechazar-reporte-parcial');
    Route::post('/admin/servicio-social/{id}/validar-reporte-final', [ServicioSocialController::class, 'validarReporteFinal'])->name('admin.servicio-social.validar-reporte-final');
    Route::post('/admin/servicio-social/{id}/validar-ventanilla-reporte-final', [ServicioSocialController::class, 'validarVentanillaReporteFinal'])->name('admin.servicio-social.validar-ventanilla-reporte-final');
    Route::post('/admin/servicio-social/{id}/rechazar-reporte-final', [ServicioSocialController::class, 'rechazarReporteFinal'])->name('admin.servicio-social.rechazar-reporte-final');

    // ==================== RUTAS ADMIN PARA INFORMES (PRÁCTICAS) ====================
    Route::post('/admin/practicas/{id}/validar-reporte-parcial', [PracticaController::class, 'validarReporteParcial'])->name('admin.practicas.validar-reporte-parcial');
    Route::post('/admin/practicas/{id}/validar-ventanilla-reporte-parcial', [PracticaController::class, 'validarVentanillaReporteParcial'])->name('admin.practicas.validar-ventanilla-reporte-parcial');
    Route::post('/admin/practicas/{id}/rechazar-reporte-parcial', [PracticaController::class, 'rechazarReporteParcial'])->name('admin.practicas.rechazar-reporte-parcial');
    Route::post('/admin/practicas/{id}/validar-reporte-final', [PracticaController::class, 'validarReporteFinal'])->name('admin.practicas.validar-reporte-final');
    Route::post('/admin/practicas/{id}/validar-ventanilla-reporte-final', [PracticaController::class, 'validarVentanillaReporteFinal'])->name('admin.practicas.validar-ventanilla-reporte-final');
    Route::post('/admin/practicas/{id}/rechazar-reporte-final', [PracticaController::class, 'rechazarReporteFinal'])->name('admin.practicas.rechazar-reporte-final');

    // ==================== RUTA PARA ACTUALIZAR ESTATUS DE DOCUMENTOS ====================
    Route::post('/admin/documentos/update-status', [DocumentoStatusController::class, 'update'])
        ->name('filament.admin.resources.servicio-socials.update-document-status')
        ->middleware(['auth']);
});

require __DIR__.'/auth.php';