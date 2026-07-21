<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServicioSocialController;
use App\Http\Controllers\DocumentoStatusController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SolicitudServicioSocialController;

use App\Http\Controllers\PracticaController;
use App\Http\Controllers\SolicitudPracticaController;
use App\Http\Controllers\EmpresaController;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\ImportController;

use App\Http\Controllers\PDFController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [HomeController::class, 'index'])->name('home');

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

    // ==================== RUTAS PARA PDF (SERVICIO SOCIAL) ====================
    Route::get('servicio-social/{id}/descargar-solicitud-pdf', [PDFController::class, 'descargarSolicitudSS'])->name('servicio-social.descargar-solicitud-pdf');
    Route::get('servicio-social/{id}/descargar-modalidad-pdf', [PDFController::class, 'descargarModalidadSS'])->name('servicio-social.descargar-modalidad-pdf');
    Route::get('servicio-social/{id}/descargar-carta-presentacion-pdf', [PDFController::class, 'descargarCartaPresentacionSS'])->name('servicio-social.descargar-carta-presentacion-pdf');
    Route::get('servicio-social/{id}/descargar-primer-informe-pdf', [PDFController::class, 'descargarPrimerInformeSS'])->name('servicio-social.descargar-primer-informe-pdf');
    Route::get('servicio-social/{id}/descargar-segundo-informe-pdf', [PDFController::class, 'descargarSegundoInformeSS'])->name('servicio-social.descargar-segundo-informe-pdf');
    Route::get('servicio-social/{id}/descargar-evaluacion-pdf', [PDFController::class, 'descargarEvaluacionSS'])->name('servicio-social.descargar-evaluacion-pdf');
    
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

    // ==================== RUTAS PARA PDF (PRÁCTICAS) ====================
    Route::get('practicas/{id}/descargar-solicitud-pdf', [PDFController::class, 'descargarSolicitudPP'])->name('practicas.descargar-solicitud-pdf');
    Route::get('practicas/{id}/descargar-modalidad-pdf', [PDFController::class, 'descargarModalidadPP'])->name('practicas.descargar-modalidad-pdf');
    Route::get('practicas/{id}/descargar-carta-presentacion-pdf', [PDFController::class, 'descargarCartaPresentacionPP'])->name('practicas.descargar-carta-presentacion-pdf');
    Route::get('practicas/{id}/descargar-primer-informe-pdf', [PDFController::class, 'descargarPrimerInformePP'])->name('practicas.descargar-primer-informe-pdf');
    Route::get('practicas/{id}/descargar-segundo-informe-pdf', [PDFController::class, 'descargarSegundoInformePP'])->name('practicas.descargar-segundo-informe-pdf');
    Route::get('practicas/{id}/descargar-evaluacion-pdf', [PDFController::class, 'descargarEvaluacionPP'])->name('practicas.descargar-evaluacion-pdf');

    Route::post('/comentarios/marcar-leidos', [App\Http\Controllers\ComentarioController::class, 'marcarLeidos'])->name('comentarios.marcar-leidos')->middleware('auth');

    // ==================== RUTAS ADMIN PARA INFORMES (SERVICIO SOCIAL) ====================
    Route::post('/admin/servicio-social/{id}/validar-reporte-parcial', [ServicioSocialController::class, 'validarReporteParcial'])->name('admin.servicio-social.validar-reporte-parcial');
    Route::post('/admin/servicio-social/{id}/validar-ventanilla-reporte-parcial', [ServicioSocialController::class, 'validarVentanillaReporteParcial'])->name('admin.servicio-social.validar-ventanilla-reporte-parcial');
    Route::post('/admin/servicio-social/{id}/rechazar-reporte-parcial', [ServicioSocialController::class, 'rechazarReporteParcial'])->name('admin.servicio-social.rechazar-reporte-parcial');
    Route::post('/admin/servicio-social/{id}/validar-reporte-final', [ServicioSocialController::class, 'validarReporteFinal'])->name('admin.servicio-social.validar-reporte-final');
    Route::post('/admin/servicio-social/{id}/validar-ventanilla-reporte-final', [ServicioSocialController::class, 'validarVentanillaReporteFinal'])->name('admin.servicio-social.validar-ventanilla-reporte-final');
    Route::post('/admin/servicio-social/{id}/rechazar-reporte-final', [ServicioSocialController::class, 'rechazarReporteFinal'])->name('admin.servicio-social.rechazar-reporte-final');

    // ==================== EMPRESAS (CONVENIOS) ====================
    Route::get('/empresas', [App\Http\Controllers\EmpresaController::class, 'index'])->name('empresas.index');

    // ==================== RUTAS ADMIN PARA INFORMES (PRÁCTICAS) ====================
    Route::post('/admin/practicas/{id}/validar-reporte-parcial', [PracticaController::class, 'validarReporteParcial'])->name('admin.practicas.validar-reporte-parcial');
    Route::post('/admin/practicas/{id}/validar-ventanilla-reporte-parcial', [PracticaController::class, 'validarVentanillaReporteParcial'])->name('admin.practicas.validar-ventanilla-reporte-parcial');
    Route::post('/admin/practicas/{id}/rechazar-reporte-parcial', [PracticaController::class, 'rechazarReporteParcial'])->name('admin.practicas.rechazar-reporte-parcial');
    Route::post('/admin/practicas/{id}/validar-reporte-final', [PracticaController::class, 'validarReporteFinal'])->name('admin.practicas.validar-reporte-final');
    Route::post('/admin/practicas/{id}/validar-ventanilla-reporte-final', [PracticaController::class, 'validarVentanillaReporteFinal'])->name('admin.practicas.validar-ventanilla-reporte-final');
    Route::post('/admin/practicas/{id}/rechazar-reporte-final', [PracticaController::class, 'rechazarReporteFinal'])->name('admin.practicas.rechazar-reporte-final');

    // ==================== RUTA PARA ACTUALIZAR CONTRASEÑA ====================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ==================== RUTA PARA ACTUALIZAR ESTATUS DE DOCUMENTOS ====================
    Route::post('/admin/documentos/update-status', [DocumentoStatusController::class, 'update'])
        ->name('filament.admin.resources.servicio-socials.update-document-status')
        ->middleware(['auth']);

    // ==================== RUTAS PARA PLANTILLAS (ADMIN) ====================
    Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
        // ✅ PLANTILLA ALUMNOS EN XLSX
        Route::get('/plantilla-alumnos', function () {
            return response()->streamDownload(function () {
                $file = fopen('php://output', 'w');
                
                // ✅ BOM UTF-8 para compatibilidad
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // ✅ Encabezados con acentos
                $headers = ['Matrícula', 'Nombre', 'Primer apellido', 'Segundo apellido', 'Grupo Referente', 'Periodo'];
                fputcsv($file, $headers);
                
                // ✅ Líneas de instrucción
                fputcsv($file, ['# ⚠️ DATOS DE EJEMPLO - BORRAR Y REEMPLAZAR', '', '', '', '', '']);
                fputcsv($file, ['# 📌 Reemplaza estos datos con los tuyos', '', '', '', '', '']);
                fputcsv($file, ['# 📌 No modifiques los nombres de las columnas', '', '', '', '', '']);
                fputcsv($file, ['', '', '', '', '', '']);
                
                // ✅ Datos de ejemplo (con acentos)
                fputcsv($file, ['230090047-5', 'DARSY ALINA', 'ROMÁN', 'HUCHÍN', '601-ADMO23', '2023-2026']);
                fputcsv($file, ['232860005-7', 'ALDO YAEL', 'PECH', 'ILLESCAS', '601-ADMO23', '2023-2026']);
                
                fputcsv($file, ['', '', '', '', '', '']);
                fputcsv($file, ['# --- TUS DATOS EMPIEZAN AQUÍ ---', '', '', '', '', '']);
                
                fclose($file);
            }, 'plantilla_alumnos.csv', [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="plantilla_alumnos.csv"',
            ]);
        })->name('admin.plantilla.alumnos');

        // ✅ PLANTILLA EMPRESAS EN XLSX
        Route::get('/plantilla-empresas', function () {
            return response()->streamDownload(function () {
                $file = fopen('php://output', 'w');
                
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                $headers = ['Nombre', 'Dirección', 'Teléfono', 'Contacto', 'Servicio Social', 'Prácticas', 'Dual', 'Fecha_Termino_Convenio'];
                fputcsv($file, $headers);
                
                fputcsv($file, ['# ⚠️ DATOS DE EJEMPLO - BORRAR Y REEMPLAZAR', '', '', '', '', '', '', '']);
                fputcsv($file, ['# 📌 Reemplaza estos datos con los tuyos', '', '', '', '', '', '', '']);
                fputcsv($file, ['', '', '', '', '', '', '', '']);
                
                fputcsv($file, ['CONALEP Cancún II', 'Av. Ejemplo #123', '998-123-4567', 'contacto@conalep.edu.mx', 'SÍ', 'SÍ', 'NO', '2025-12-31']);
                
                fputcsv($file, ['', '', '', '', '', '', '', '']);
                fputcsv($file, ['# --- TUS DATOS EMPIEZAN AQUÍ ---', '', '', '', '', '', '', '']);
                
                fclose($file);
            }, 'plantilla_empresas.csv', [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="plantilla_empresas.csv"',
            ]);
        })->name('admin.plantilla.empresas');
    });
});

require __DIR__.'/auth.php';