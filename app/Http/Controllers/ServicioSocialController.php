<?php

namespace App\Http\Controllers;

use App\Models\ServicioSocial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Documento;
use App\Models\TipoDocumento;

use App\Models\Comentario;

class ServicioSocialController extends Controller
{
    // Muestra el progreso del SS del estudiante autenticado
    public function index()
    {
        $user = Auth::user();
        $servicioSocial = $user->servicioSocial;

        // DEFINIR LA LISTA DE DOCUMENTOS ADMINISTRATIVOS
        $documentosAdministrativos = [
            'Solicitud de Servicio Social',
            'Elección de Modalidad',
            'Carta de Presentación de Servicio Social',
            'Carta de Aceptación',
            'Evaluación de Competencias del Desempeño',
            'Carta de Liberación de Servicio Social'
        ];

        // Obtener comentarios de cada documento administrativo
        $comentariosPorDocumento = [];

        foreach ($documentosAdministrativos as $nombre) {
            $doc = Documento::where('user_id', Auth::id())
                ->whereHas('tipoDocumento', function($q) use ($nombre) {
                    $q->where('nombre', $nombre);
                })
                ->where('activo', true)
                ->first();

            if ($doc) {
                $comentariosPorDocumento[$nombre] = $doc->comentarios()
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                $comentariosPorDocumento[$nombre] = collect(); // vacío
            }
        }

        // Comentarios para informes (Primer y Segundo)
        $comentariosPorInforme = [
            'primero' => $servicioSocial->comentarios()->where('tipo', 'admin')->where('comentable_type', 'App\Models\ServicioSocial')->get(),
            'segundo' => $servicioSocial->comentarios()->where('tipo', 'admin')->where('comentable_type', 'App\Models\ServicioSocial')->get(),
        ];

        // Vista principal del servicio social con toda la información
        return view('servicio_social.index', compact('servicioSocial', 'comentariosPorDocumento', 'comentariosPorInforme'));
    }

    // Formulario para actualizar horas (solo para pruebas)
    public function edit($id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        return view('servicio_social.edit', compact('servicioSocial'));
    }

    // Actualiza las horas completadas
    public function update(Request $request, $id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'horas_completadas' => 'required|integer|min:0|max:480',
        ]);

        $servicioSocial->update([
            'horas_completadas' => $request->horas_completadas
        ]);

        return redirect()->route('servicio-social.index')
                         ->with('success', 'Horas actualizadas correctamente.');
    }

    // Mostrar formulario para subir reporte parcial
    public function mostrarFormularioReporteParcial($id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        // Validar que tenga al menos 240 horas
        if ($servicioSocial->horas_completadas < 240) {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'Debes completar al menos 240 horas para subir el reporte parcial.');
        }

        return view('servicio_social.subir_reporte_parcial', compact('servicioSocial'));
    }

    // Procesar la subida del reporte parcial
    public function subirReporteParcial(Request $request, $id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        // Validar horas
        if ($servicioSocial->horas_completadas < 240) {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'No tienes las horas suficientes (mínimo 240h).');
        }

        $request->validate([
            'reporte_pdf' => 'required|file|mimes:pdf|max:5120',
                'comentario' => 'nullable|string|max:500',
        ]);

        // Eliminar archivo anterior si existe (para reemplazo)
        if ($servicioSocial->archivo_parcial && file_exists(storage_path('app/public/' . $servicioSocial->archivo_parcial))) {
            unlink(storage_path('app/public/' . $servicioSocial->archivo_parcial));
        }

        // Guardar el nuevo archivo
        $path = $request->file('reporte_pdf')->store('reportes_ss_parcial', 'public');

        // Actualizar la base de datos
        $servicioSocial->update([
            'reporte_parcial_subido' => true,
            'archivo_parcial' => $path,
        ]);

        // Guardar comentario del administrador (si existe en el request)
        if ($request->filled('comentario')) {
            $comentario = new \App\Models\Comentario([
                'contenido' => $request->comentario,
                'tipo' => 'admin_primer_informe', // diferente
                'user_id' => Auth::id(),
                'comentable_id' => $servicioSocial->id,
                'comentable_type' => 'App\Models\ServicioSocial',
            ]);
            $comentario->save();
        }

        return redirect()->route('servicio-social.index')
                        ->with('success', 'Primer Informe subido correctamente.');
    }

    // Mostrar formulario para subir reporte final
    public function mostrarFormularioReporteFinal($id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        if ($servicioSocial->horas_completadas < 480) {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'Debes completar 480 horas para subir el reporte final.');
        }

        return view('servicio_social.subir_reporte_final', compact('servicioSocial'));
    }

    // Procesar la subida del reporte final
    public function subirReporteFinal(Request $request, $id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        // Validar horas (mínimo 480 para segundo informe)
        if ($servicioSocial->horas_completadas < 480) {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'No tienes las horas suficientes (mínimo 480h).');
        }

        $request->validate([
            'reporte_pdf' => 'required|file|mimes:pdf|max:5120',
                'comentario' => 'nullable|string|max:500',
        ]);

        // Eliminar archivo anterior si existe (para permitir reemplazo)
        if ($servicioSocial->archivo_final && file_exists(storage_path('app/public/' . $servicioSocial->archivo_final))) {
            unlink(storage_path('app/public/' . $servicioSocial->archivo_final));
        }

        // Guardar el nuevo archivo
        $path = $request->file('reporte_pdf')->store('reportes_ss_final', 'public');

        // Actualizar la base de datos
        $servicioSocial->update([
            'reporte_final_subido' => true,
            'archivo_final' => $path,
            'estatus' => 'pendiente_revision'
        ]);

        // Guardar comentario del administrador (si existe en el request)
        if ($request->filled('comentario')) {
            $comentario = new \App\Models\Comentario([
                'contenido' => $request->comentario,
                'tipo' => 'admin_segundo_informe',
                'user_id' => Auth::id(),
                'comentable_id' => $servicioSocial->id,
                'comentable_type' => 'App\Models\ServicioSocial',
            ]);
            $comentario->save();
        }

        return redirect()->route('servicio-social.index')
                        ->with('success', 'Segundo Informe subido correctamente. El administrador revisará tu documentación para liberar el trámite.');
    }

    // Mostrar formulario para subir solicitud
    public function mostrarFormularioSolicitud($id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        return view('servicio_social.subir_solicitud', compact('servicioSocial'));
    }

    // Procesar la subida de la solicitud
    public function subirSolicitud(Request $request, $id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'archivo_pdf' => 'required|file|mimes:pdf|max:5120',
            'comentario' => 'nullable|string|max:500',
        ]);

        // Eliminar documento anterior si existe (para permitir reemplazo)
        Documento::where('user_id', Auth::id())
            ->whereHas('tipoDocumento', function($q) {
                $q->where('nombre', 'Solicitud de Servicio Social');
            })->delete();

        // Obtener el tipo de documento "Solicitud"
        $tipoDocumento = TipoDocumento::where('nombre', 'Solicitud de Servicio Social')->first();

        if (!$tipoDocumento) {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'Tipo de documento no encontrado. Contacta al administrador.');
        }

        // Guardar el archivo
        $path = $request->file('archivo_pdf')->store('documentos/solicitudes', 'public');

        // Crear el registro en la tabla documentos
        Documento::create([
            'user_id' => Auth::id(),
            'tipo_documento_id' => $tipoDocumento->id,
            'archivo_pdf' => $path,
            'estatus' => 'pendiente',
            'comentario' => $request->comentario,
            'comentario_admin' => null,
        ]);

        return redirect()->route('servicio-social.index')
                        ->with('success', 'Solicitud subida correctamente.');
    }

    // Mostrar formulario para subir Elección de Modalidad
    public function mostrarFormularioModalidad($id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        // Verificar si ya subió el documento

        return view('servicio_social.subir_modalidad', compact('servicioSocial'));
    }

    // Procesar la subida de Elección de Modalidad
    public function subirModalidad(Request $request, $id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'archivo_pdf' => 'required|file|mimes:pdf|max:5120',
            'comentario' => 'nullable|string|max:500',
        ]);

        // Eliminar documento anterior si existe (para permitir reemplazo)
        Documento::where('user_id', Auth::id())
            ->whereHas('tipoDocumento', function($q) {
                $q->where('nombre', 'Elección de Modalidad');
            })->delete();

        $tipoDocumento = TipoDocumento::where('nombre', 'Elección de Modalidad')->first();

        if (!$tipoDocumento) {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'Tipo de documento no encontrado. Contacta al administrador.');
        }

        $path = $request->file('archivo_pdf')->store('documentos/modalidad', 'public');

        Documento::create([
            'user_id' => Auth::id(),
            'tipo_documento_id' => $tipoDocumento->id,
            'archivo_pdf' => $path,
            'estatus' => 'pendiente',
            'comentario' => $request->comentario,
            'comentario_admin' => null,
        ]);

        return redirect()->route('servicio-social.index')
                        ->with('success', 'Elección de Modalidad subida correctamente. Espera la validación del administrador.');
    }

    // Mostrar formulario para subir Carta de Presentación
    public function mostrarFormularioCartaPresentacion($id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        return view('servicio_social.subir_carta_presentacion', compact('servicioSocial'));
    }

    // Procesar la subida de Carta de Presentación
    public function subirCartaPresentacion(Request $request, $id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'archivo_pdf' => 'required|file|mimes:pdf|max:5120',
            'comentario' => 'nullable|string|max:500',
        ]);

        // Eliminar documento anterior si existe (para permitir reemplazo)
        Documento::where('user_id', Auth::id())
            ->whereHas('tipoDocumento', function($q) {
                $q->where('nombre', 'Carta de Presentación de Servicio Social');
            })->delete();

        $tipoDocumento = TipoDocumento::where('nombre', 'Carta de Presentación de Servicio Social')->first();

        if (!$tipoDocumento) {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'Tipo de documento no encontrado. Contacta al administrador.');
        }

        $path = $request->file('archivo_pdf')->store('documentos/carta_presentacion', 'public');

        Documento::create([
            'user_id' => Auth::id(),
            'tipo_documento_id' => $tipoDocumento->id,
            'archivo_pdf' => $path,
            'estatus' => 'pendiente',
            'comentario' => $request->comentario,
            'comentario_admin' => null,
        ]);

        return redirect()->route('servicio-social.index')
                        ->with('success', 'Carta de Presentación subida correctamente. Espera la validación del administrador.');
    }

    // Mostrar formulario para subir Carta de Aceptación
    public function mostrarFormularioCartaAceptacion($id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        return view('servicio_social.subir_carta_aceptacion', compact('servicioSocial'));
    }

    // Procesar la subida de Carta de Aceptación
    public function subirCartaAceptacion(Request $request, $id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'archivo_pdf' => 'required|file|mimes:pdf|max:5120',
            'comentario' => 'nullable|string|max:500',
        ]);

        // Eliminar documento anterior si existe (para permitir reemplazo)
        Documento::where('user_id', Auth::id())
            ->whereHas('tipoDocumento', function($q) {
                $q->where('nombre', 'Carta de Aceptación');
            })->delete();

        $tipoDocumento = TipoDocumento::where('nombre', 'Carta de Aceptación')->first();

        if (!$tipoDocumento) {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'Tipo de documento no encontrado. Contacta al administrador.');
        }

        $path = $request->file('archivo_pdf')->store('documentos/carta_aceptacion', 'public');

        Documento::create([
            'user_id' => Auth::id(),
            'tipo_documento_id' => $tipoDocumento->id,
            'archivo_pdf' => $path,
            'estatus' => 'pendiente',
            'comentario' => $request->comentario,
            'comentario_admin' => null,
        ]);

        return redirect()->route('servicio-social.index')
                        ->with('success', 'Carta de Aceptación subida correctamente. Espera la validación del administrador.');
    }

    // Mostrar formulario para subir Evaluación de Competencias
    public function mostrarFormularioEvaluacion($id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        return view('servicio_social.subir_evaluacion', compact('servicioSocial'));
    }

    // Procesar la subida de Evaluación de Competencias
    public function subirEvaluacion(Request $request, $id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'archivo_pdf' => 'required|file|mimes:pdf|max:5120',
            'comentario' => 'nullable|string|max:500',
        ]);

        // Eliminar documento anterior si existe (para permitir reemplazo)
        Documento::where('user_id', Auth::id())
            ->whereHas('tipoDocumento', function($q) {
                $q->where('nombre', 'Evaluación de Competencias del Desempeño');
            })->delete();

        $tipoDocumento = TipoDocumento::where('nombre', 'Evaluación de Competencias del Desempeño')->first();

        if (!$tipoDocumento) {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'Tipo de documento no encontrado. Contacta al administrador.');
        }

        $path = $request->file('archivo_pdf')->store('documentos/evaluacion', 'public');

        Documento::create([
            'user_id' => Auth::id(),
            'tipo_documento_id' => $tipoDocumento->id,
            'archivo_pdf' => $path,
            'estatus' => 'pendiente',
            'comentario' => $request->comentario,
            'comentario_admin' => null,
        ]);

        return redirect()->route('servicio-social.index')
                        ->with('success', 'Evaluación de Competencias subida correctamente. Espera la validación del administrador.');
    }

    // Mostrar formulario para subir Carta de Liberación
    public function mostrarFormularioLiberacion($id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        return view('servicio_social.subir_liberacion', compact('servicioSocial'));
    }

    // Procesar la subida de Carta de Liberación
    public function subirLiberacion(Request $request, $id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'archivo_pdf' => 'required|file|mimes:pdf|max:5120',
            'comentario' => 'nullable|string|max:500',
        ]);

        // Eliminar documento anterior si existe (para permitir reemplazo)
        Documento::where('user_id', Auth::id())
            ->whereHas('tipoDocumento', function($q) {
                $q->where('nombre', 'Carta de Liberación de Servicio Social');
            })->delete();

        $tipoDocumento = TipoDocumento::where('nombre', 'Carta de Liberación de Servicio Social')->first();

        if (!$tipoDocumento) {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'Tipo de documento no encontrado. Contacta al administrador.');
        }

        $path = $request->file('archivo_pdf')->store('documentos/liberacion', 'public');

        Documento::create([
            'user_id' => Auth::id(),
            'tipo_documento_id' => $tipoDocumento->id,
            'archivo_pdf' => $path,
            'estatus' => 'pendiente',
            'comentario' => $request->comentario,
            'comentario_admin' => null,
        ]);

        return redirect()->route('servicio-social.index')
                        ->with('success', 'Carta de Liberación subida correctamente. Espera la validación del administrador.');
    }

    // Eliminar un documento específico
    public function eliminarDocumento($id, $tipoDocumentoNombre)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        // Buscar y eliminar el documento
        $documento = Documento::where('user_id', Auth::id())
            ->whereHas('tipoDocumento', function($q) use ($tipoDocumentoNombre) {
                $q->where('nombre', $tipoDocumentoNombre);
            })->first();

        if (!$documento) {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'Documento no encontrado.');
        }

        // Eliminar el archivo físico
        if (file_exists(storage_path('app/public/' . $documento->archivo_pdf))) {
            unlink(storage_path('app/public/' . $documento->archivo_pdf));
        }

        $documento->delete();

        return redirect()->route('servicio-social.index')
                        ->with('success', 'Documento eliminado correctamente. Puedes volver a subirlo.');
    }

    // Eliminar un informe (Primer o Segundo Informe)
    public function eliminarInforme($id, $tipo)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        if ($tipo == 'primero') {
            // Eliminar el archivo físico del primer informe
            if ($servicioSocial->archivo_parcial && file_exists(storage_path('app/public/' . $servicioSocial->archivo_parcial))) {
                unlink(storage_path('app/public/' . $servicioSocial->archivo_parcial));
            }
            
            $servicioSocial->update([
                'reporte_parcial_subido' => false,
                'archivo_parcial' => null,
            ]);
            
            $mensaje = 'Primer Informe eliminado correctamente.';
            
        } elseif ($tipo == 'segundo') {
            // Eliminar el archivo físico del segundo informe
            if ($servicioSocial->archivo_final && file_exists(storage_path('app/public/' . $servicioSocial->archivo_final))) {
                unlink(storage_path('app/public/' . $servicioSocial->archivo_final));
            }
            
            $servicioSocial->update([
                'reporte_final_subido' => false,
                'archivo_final' => null,
            ]);
            
            $mensaje = 'Segundo Informe eliminado correctamente.';
            
        } else {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'Tipo de informe no válido.');
        }

        return redirect()->route('servicio-social.index')
                        ->with('success', $mensaje);
    }
}