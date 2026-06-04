<?php

namespace App\Http\Controllers;

use App\Models\ServicioSocial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Documento;
use App\Models\TipoDocumento;

class ServicioSocialController extends Controller
{
    // Muestra el progreso del SS del estudiante autenticado
    public function index()
    {
        $user = Auth::user();
        $servicioSocial = $user->servicioSocial;

        return view('servicio_social.index', compact('servicioSocial'));
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

        // Validar que no haya subido ya el reporte
        if ($servicioSocial->reporte_parcial_subido) {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'Ya subiste el reporte parcial.');
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
                            ->with('error', 'No tienes las horas suficientes.');
        }

        // Validar que no haya subido ya
        if ($servicioSocial->reporte_parcial_subido) {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'El reporte parcial ya fue subido.');
        }

        // Validar el archivo
        $request->validate([
            'reporte_pdf' => 'required|file|mimes:pdf|max:5120', // Máximo 5MB
        ]);

        // Guardar el archivo
        $path = $request->file('reporte_pdf')->store('reportes_ss_parcial', 'public');

        // Actualizar la base de datos
        $servicioSocial->update([
            'reporte_parcial_subido' => true,
            'archivo_parcial' => $path,
        ]);

        return redirect()->route('servicio-social.index')
                        ->with('success', 'Reporte parcial subido correctamente. Espera la validación del administrador.');
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

        if ($servicioSocial->reporte_final_subido) {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'Ya subiste el reporte final.');
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

        if ($servicioSocial->horas_completadas < 480) {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'No tienes las horas suficientes.');
        }

        if ($servicioSocial->reporte_final_subido) {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'El reporte final ya fue subido.');
        }

        $request->validate([
            'reporte_pdf' => 'required|file|mimes:pdf|max:5120',
        ]);

        $path = $request->file('reporte_pdf')->store('reportes_ss_final', 'public');

        $servicioSocial->update([
            'reporte_final_subido' => true,
            'archivo_final' => $path,
            'estatus' => 'pendiente_revision'
        ]);

        return redirect()->route('servicio-social.index')
                        ->with('success', 'Reporte final subido correctamente. ¡Felicidades! Has completado tu Servicio Social.');
    }

    // Mostrar formulario para subir solicitud
    public function mostrarFormularioSolicitud($id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        // Verificar si ya subió la solicitud
        $solicitudSubida = Documento::where('user_id', Auth::id())
            ->whereHas('tipoDocumento', function ($query) {
                $query->where('nombre', 'Solicitud de Servicio Social');
            })->exists();

        if ($solicitudSubida) {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'Ya subiste la solicitud.');
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

        // Verificar si ya subió la solicitud
        $solicitudSubida = Documento::where('user_id', Auth::id())
            ->whereHas('tipoDocumento', function ($query) {
                $query->where('nombre', 'Solicitud');
            })->exists();

        if ($solicitudSubida) {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'La solicitud ya fue subida.');
        }

        $request->validate([
            'archivo_pdf' => 'required|file|mimes:pdf|max:5120',
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
            'comentario' => null,
        ]);

        return redirect()->route('servicio-social.index')
                        ->with('success', 'Solicitud subida correctamente. Espera la validación del administrador.');
    }

    // Mostrar formulario para subir Elección de Modalidad
    public function mostrarFormularioModalidad($id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        // Verificar si ya subió el documento
        $yaSubido = Documento::where('user_id', Auth::id())
            ->whereHas('tipoDocumento', function ($query) {
                $query->where('nombre', 'Elección de Modalidad');
            })->exists();

        if ($yaSubido) {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'Ya subiste la Elección de Modalidad.');
        }

        return view('servicio_social.subir_modalidad', compact('servicioSocial'));
    }

    // Procesar la subida de Elección de Modalidad
    public function subirModalidad(Request $request, $id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        // Verificar si ya subió el documento
        $yaSubido = Documento::where('user_id', Auth::id())
            ->whereHas('tipoDocumento', function ($query) {
                $query->where('nombre', 'Elección de Modalidad');
            })->exists();

        if ($yaSubido) {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'La Elección de Modalidad ya fue subida.');
        }

        $request->validate([
            'archivo_pdf' => 'required|file|mimes:pdf|max:5120',
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
            'comentario' => null,
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

        // Verificar si ya subió el documento
        $yaSubido = Documento::where('user_id', Auth::id())
            ->whereHas('tipoDocumento', function ($query) {
                $query->where('nombre', 'Carta de Presentación de Servicio Social');
            })->exists();

        if ($yaSubido) {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'Ya subiste la Carta de Presentación.');
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

        // Verificar si ya subió el documento
        $yaSubido = Documento::where('user_id', Auth::id())
            ->whereHas('tipoDocumento', function ($query) {
                $query->where('nombre', 'Carta de Presentación de Servicio Social');
            })->exists();

        if ($yaSubido) {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'La Carta de Presentación ya fue subida.');
        }

        $request->validate([
            'archivo_pdf' => 'required|file|mimes:pdf|max:5120',
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
            'comentario' => null,
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

        // Verificar si ya subió el documento
        $yaSubido = Documento::where('user_id', Auth::id())
            ->whereHas('tipoDocumento', function ($query) {
                $query->where('nombre', 'Carta de Aceptación');
            })->exists();

        if ($yaSubido) {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'Ya subiste la Carta de Aceptación.');
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

        // Verificar si ya subió el documento
        $yaSubido = Documento::where('user_id', Auth::id())
            ->whereHas('tipoDocumento', function ($query) {
                $query->where('nombre', 'Carta de Aceptación');
            })->exists();

        if ($yaSubido) {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'La Carta de Aceptación ya fue subida.');
        }

        $request->validate([
            'archivo_pdf' => 'required|file|mimes:pdf|max:5120',
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
            'comentario' => null,
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

        // Verificar si ya subió el documento
        $yaSubido = Documento::where('user_id', Auth::id())
            ->whereHas('tipoDocumento', function ($query) {
                $query->where('nombre', 'Evaluación de Competencias del Desempeño');
            })->exists();

        if ($yaSubido) {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'Ya subiste la Evaluación de Competencias.');
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

        // Verificar si ya subió el documento
        $yaSubido = Documento::where('user_id', Auth::id())
            ->whereHas('tipoDocumento', function ($query) {
                $query->where('nombre', 'Evaluación de Competencias del Desempeño');
            })->exists();

        if ($yaSubido) {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'La Evaluación de Competencias ya fue subida.');
        }

        $request->validate([
            'archivo_pdf' => 'required|file|mimes:pdf|max:5120',
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
            'comentario' => null,
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

        // Verificar si ya subió el documento
        $yaSubido = Documento::where('user_id', Auth::id())
            ->whereHas('tipoDocumento', function ($query) {
                $query->where('nombre', 'Carta de Liberación de Servicio Social');
            })->exists();

        if ($yaSubido) {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'Ya subiste la Carta de Liberación.');
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

        // Verificar si ya subió el documento
        $yaSubido = Documento::where('user_id', Auth::id())
            ->whereHas('tipoDocumento', function ($query) {
                $query->where('nombre', 'Carta de Liberación de Servicio Social');
            })->exists();

        if ($yaSubido) {
            return redirect()->route('servicio-social.index')
                            ->with('error', 'La Carta de Liberación ya fue subida.');
        }

        $request->validate([
            'archivo_pdf' => 'required|file|mimes:pdf|max:5120',
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
            'comentario' => null,
        ]);

        return redirect()->route('servicio-social.index')
                        ->with('success', 'Carta de Liberación subida correctamente. Espera la validación del administrador.');
    }
}