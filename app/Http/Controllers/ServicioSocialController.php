<?php

namespace App\Http\Controllers;

use App\Models\ServicioSocial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Documento;
use App\Models\TipoDocumento;

use App\Models\Comentario;

use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;

class ServicioSocialController extends Controller
{
    // Muestra el progreso del SS del estudiante autenticado
    public function index()
    {
        $user = Auth::user();
        $servicioSocial = $user->servicioSocial;

        if (!$servicioSocial || !$servicioSocial->fecha_inicio) {
            return redirect()->route('solicitud-servicio-social.create')
                ->with('info', 'Completa el formulario de solicitud para comenzar.');
        }

        $documentosAdministrativos = [
            'Solicitud de Servicio Social',
            'Elección de Modalidad',
            'Carta de Presentación de Servicio Social',
            'Carta de Aceptación',
            'Evaluación de Competencias del Desempeño',
            'Carta de Liberación de Servicio Social'
        ];

        $comentariosPorDocumento = [];

        foreach ($documentosAdministrativos as $nombre) {
            $doc = Documento::where('user_id', Auth::id())
                ->whereHas('tipoDocumento', function($q) use ($nombre) {
                    $q->where('nombre', $nombre)
                      ->where('tramite', 'SS');
                })
                ->where('activo', true)
                ->first();

            if ($doc) {
                $comentariosPorDocumento[$nombre] = $doc->comentarios()
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                $comentariosPorDocumento[$nombre] = collect();
            }
        }

        $comentariosPorInforme = [
            'primero' => $servicioSocial->comentarios()->where('tipo', 'admin')->where('comentable_type', 'App\Models\ServicioSocial')->get(),
            'segundo' => $servicioSocial->comentarios()->where('tipo', 'admin')->where('comentable_type', 'App\Models\ServicioSocial')->get(),
        ];

        return view('servicio_social.index', compact('servicioSocial', 'comentariosPorDocumento', 'comentariosPorInforme'));
    }

    // Mostrar formulario para subir reporte parcial
    public function mostrarFormularioReporteParcial($id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$servicioSocial->fecha_limite_primer_informe || now()->lt($servicioSocial->fecha_limite_primer_informe)) {
            return redirect()->route('servicio-social.index')
                ->with('error', 'Aún no puedes subir el Primer Informe. La fecha límite es el ' . optional($servicioSocial->fecha_limite_primer_informe)->format('d/m/Y'));
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

        if (!$servicioSocial->fecha_limite_primer_informe || now()->lt($servicioSocial->fecha_limite_primer_informe)) {
            return redirect()->route('servicio-social.index')
                ->with('error', 'Aún no puedes subir el Primer Informe.');
        }

        $request->validate([
            'reporte_pdf' => 'required|file|mimes:pdf|max:5120',
            'comentario' => 'nullable|string|max:500',
        ]);

        if ($servicioSocial->archivo_parcial && file_exists(storage_path('app/public/' . $servicioSocial->archivo_parcial))) {
            unlink(storage_path('app/public/' . $servicioSocial->archivo_parcial));
        }

        $path = $request->file('reporte_pdf')->store('reportes_ss_parcial', 'public');

        $servicioSocial->update([
            'reporte_parcial_subido' => true,
            'archivo_parcial' => $path,
        ]);

        if ($request->filled('comentario')) {
            $comentario = new \App\Models\Comentario([
                'contenido' => $request->comentario,
                'tipo' => 'estudiante_primer_informe',
                'user_id' => Auth::id(),
                'comentable_id' => $servicioSocial->id,
                'comentable_type' => 'App\Models\ServicioSocial',
            ]);
            $comentario->save();
        }

        if ($servicioSocial->estatus == 'pendiente') {
            $servicioSocial->estatus = 'en_progreso';
            $servicioSocial->save();
        }

        if ($servicioSocial->documentosCompletos() && $servicioSocial->estatus !== 'liberado') {
            $servicioSocial->estatus = 'pendiente_revision';
            $servicioSocial->save();
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

        if (!$servicioSocial->fecha_limite_segundo_informe || now()->lt($servicioSocial->fecha_limite_segundo_informe)) {
            return redirect()->route('servicio-social.index')
                ->with('error', 'Aún no puedes subir el Segundo Informe. La fecha límite es el ' . optional($servicioSocial->fecha_limite_segundo_informe)->format('d/m/Y'));
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

        if (!$servicioSocial->fecha_limite_segundo_informe || now()->lt($servicioSocial->fecha_limite_segundo_informe)) {
            return redirect()->route('servicio-social.index')
                ->with('error', 'Aún no puedes subir el Segundo Informe.');
        }

        $request->validate([
            'reporte_pdf' => 'required|file|mimes:pdf|max:5120',
            'comentario' => 'nullable|string|max:500',
        ]);

        if ($servicioSocial->archivo_final && file_exists(storage_path('app/public/' . $servicioSocial->archivo_final))) {
            unlink(storage_path('app/public/' . $servicioSocial->archivo_final));
        }

        $path = $request->file('reporte_pdf')->store('reportes_ss_final', 'public');

        $servicioSocial->update([
            'reporte_final_subido' => true,
            'archivo_final' => $path,
        ]);

        if ($request->filled('comentario')) {
            $comentario = new \App\Models\Comentario([
                'contenido' => $request->comentario,
                'tipo' => 'estudiante_segundo_informe',
                'user_id' => Auth::id(),
                'comentable_id' => $servicioSocial->id,
                'comentable_type' => 'App\Models\ServicioSocial',
            ]);
            $comentario->save();
        }

        if ($servicioSocial->estatus == 'pendiente') {
            $servicioSocial->estatus = 'en_progreso';
            $servicioSocial->save();
        }

        if ($servicioSocial->documentosCompletos() && $servicioSocial->estatus !== 'liberado') {
            $servicioSocial->estatus = 'pendiente_revision';
            $servicioSocial->save();
        }

        return redirect()->route('servicio-social.index')
            ->with('success', 'Segundo Informe subido correctamente.');
    }

    // Mostrar formulario para subir solicitud
    public function mostrarFormularioSolicitud($id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);
        if ($servicioSocial->user_id !== Auth::id()) abort(403);
        return view('servicio_social.subir_solicitud', compact('servicioSocial'));
    }

    public function subirSolicitud(Request $request, $id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);
        if ($servicioSocial->user_id !== Auth::id()) abort(403);

        $request->validate([
            'archivo_pdf' => 'required|file|mimes:pdf|max:5120',
            'comentario' => 'nullable|string|max:500',
        ]);

        $tipoDocumento = TipoDocumento::where('nombre', 'Solicitud de Servicio Social')
            ->where('tramite', 'SS')
            ->first();
        if (!$tipoDocumento) {
            return redirect()->route('servicio-social.index')->with('error', 'Tipo de documento no encontrado.');
        }

        $documento = Documento::where('user_id', Auth::id())
            ->where('tipo_documento_id', $tipoDocumento->id)
            ->first();

        $path = $request->file('archivo_pdf')->store('documentos/solicitudes', 'public');

        if ($documento) {
            $documento->update(['archivo_pdf' => $path, 'estatus' => 'pendiente', 'updated_at' => now()]);
        } else {
            $documento = Documento::create([
                'user_id' => Auth::id(),
                'tipo_documento_id' => $tipoDocumento->id,
                'archivo_pdf' => $path,
                'estatus' => 'pendiente',
                'activo' => true,
            ]);
        }

        if ($request->filled('comentario')) {
            $comentario = new Comentario([
                'contenido' => $request->comentario,
                'tipo' => 'estudiante',
                'user_id' => Auth::id(),
                'comentable_id' => $documento->id,
                'comentable_type' => 'App\Models\Documento',
            ]);
            $comentario->save();
        }

        if ($servicioSocial->estatus == 'pendiente') {
            $servicioSocial->estatus = 'en_progreso';
            $servicioSocial->save();
        }

        if ($servicioSocial->documentosCompletos() && $servicioSocial->estatus !== 'liberado') {
            $servicioSocial->estatus = 'pendiente_revision';
            $servicioSocial->save();
        }

        return redirect()->route('servicio-social.index')->with('success', 'Solicitud subida correctamente.');
    }

    // Mostrar formulario para subir Elección de Modalidad
    public function mostrarFormularioModalidad($id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);
        if ($servicioSocial->user_id !== Auth::id()) abort(403);
        return view('servicio_social.subir_modalidad', compact('servicioSocial'));
    }

    public function subirModalidad(Request $request, $id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);
        if ($servicioSocial->user_id !== Auth::id()) abort(403);

        $request->validate([
            'archivo_pdf' => 'required|file|mimes:pdf|max:5120',
            'comentario' => 'nullable|string|max:500',
        ]);

        $tipoDocumento = TipoDocumento::where('nombre', 'Elección de Modalidad')
            ->where('tramite', 'SS')
            ->first();
        if (!$tipoDocumento) {
            return redirect()->route('servicio-social.index')->with('error', 'Tipo de documento no encontrado.');
        }

        $documento = Documento::where('user_id', Auth::id())
            ->where('tipo_documento_id', $tipoDocumento->id)
            ->first();

        $path = $request->file('archivo_pdf')->store('documentos/modalidad', 'public');

        if ($documento) {
            $documento->update(['archivo_pdf' => $path, 'estatus' => 'pendiente', 'updated_at' => now()]);
        } else {
            $documento = Documento::create([
                'user_id' => Auth::id(),
                'tipo_documento_id' => $tipoDocumento->id,
                'archivo_pdf' => $path,
                'estatus' => 'pendiente',
                'activo' => true,
            ]);
        }

        if ($request->filled('comentario')) {
            $comentario = new Comentario([
                'contenido' => $request->comentario,
                'tipo' => 'estudiante',
                'user_id' => Auth::id(),
                'comentable_id' => $documento->id,
                'comentable_type' => 'App\Models\Documento',
            ]);
            $comentario->save();
        }

        if ($servicioSocial->estatus == 'pendiente') {
            $servicioSocial->estatus = 'en_progreso';
            $servicioSocial->save();
        }

        if ($servicioSocial->documentosCompletos() && $servicioSocial->estatus !== 'liberado') {
            $servicioSocial->estatus = 'pendiente_revision';
            $servicioSocial->save();
        }

        return redirect()->route('servicio-social.index')->with('success', 'Elección de Modalidad subida correctamente.');
    }

    // Mostrar formulario para subir Carta de Presentación
    public function mostrarFormularioCartaPresentacion($id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);
        if ($servicioSocial->user_id !== Auth::id()) abort(403);
        return view('servicio_social.subir_carta_presentacion', compact('servicioSocial'));
    }

    public function subirCartaPresentacion(Request $request, $id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);
        if ($servicioSocial->user_id !== Auth::id()) abort(403);

        $request->validate([
            'archivo_pdf' => 'required|file|mimes:pdf|max:5120',
            'comentario' => 'nullable|string|max:500',
        ]);

        $tipoDocumento = TipoDocumento::where('nombre', 'Carta de Presentación de Servicio Social')
            ->where('tramite', 'SS')
            ->first();
        if (!$tipoDocumento) {
            return redirect()->route('servicio-social.index')->with('error', 'Tipo de documento no encontrado.');
        }

        $documento = Documento::where('user_id', Auth::id())
            ->where('tipo_documento_id', $tipoDocumento->id)
            ->first();

        $path = $request->file('archivo_pdf')->store('documentos/carta_presentacion', 'public');

        if ($documento) {
            $documento->update(['archivo_pdf' => $path, 'estatus' => 'pendiente', 'updated_at' => now()]);
        } else {
            $documento = Documento::create([
                'user_id' => Auth::id(),
                'tipo_documento_id' => $tipoDocumento->id,
                'archivo_pdf' => $path,
                'estatus' => 'pendiente',
                'activo' => true,
            ]);
        }

        if ($request->filled('comentario')) {
            $comentario = new Comentario([
                'contenido' => $request->comentario,
                'tipo' => 'estudiante',
                'user_id' => Auth::id(),
                'comentable_id' => $documento->id,
                'comentable_type' => 'App\Models\Documento',
            ]);
            $comentario->save();
        }

        if ($servicioSocial->estatus == 'pendiente') {
            $servicioSocial->estatus = 'en_progreso';
            $servicioSocial->save();
        }

        if ($servicioSocial->documentosCompletos() && $servicioSocial->estatus !== 'liberado') {
            $servicioSocial->estatus = 'pendiente_revision';
            $servicioSocial->save();
        }

        return redirect()->route('servicio-social.index')->with('success', 'Carta de Presentación subida correctamente.');
    }

    // Mostrar formulario para subir Carta de Aceptación
    public function mostrarFormularioCartaAceptacion($id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);
        if ($servicioSocial->user_id !== Auth::id()) abort(403);
        return view('servicio_social.subir_carta_aceptacion', compact('servicioSocial'));
    }

    public function subirCartaAceptacion(Request $request, $id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);
        if ($servicioSocial->user_id !== Auth::id()) abort(403);

        $request->validate([
            'archivo_pdf' => 'required|file|mimes:pdf|max:5120',
            'comentario' => 'nullable|string|max:500',
        ]);

        $tipoDocumento = TipoDocumento::where('nombre', 'Carta de Aceptación')
            ->where('tramite', 'SS')
            ->first();
        if (!$tipoDocumento) {
            return redirect()->route('servicio-social.index')->with('error', 'Tipo de documento no encontrado.');
        }

        $documento = Documento::where('user_id', Auth::id())
            ->where('tipo_documento_id', $tipoDocumento->id)
            ->first();

        $path = $request->file('archivo_pdf')->store('documentos/carta_aceptacion', 'public');

        if ($documento) {
            $documento->update(['archivo_pdf' => $path, 'estatus' => 'pendiente', 'updated_at' => now()]);
        } else {
            $documento = Documento::create([
                'user_id' => Auth::id(),
                'tipo_documento_id' => $tipoDocumento->id,
                'archivo_pdf' => $path,
                'estatus' => 'pendiente',
                'activo' => true,
            ]);
        }

        if ($request->filled('comentario')) {
            $comentario = new Comentario([
                'contenido' => $request->comentario,
                'tipo' => 'estudiante',
                'user_id' => Auth::id(),
                'comentable_id' => $documento->id,
                'comentable_type' => 'App\Models\Documento',
            ]);
            $comentario->save();
        }

        if ($servicioSocial->estatus == 'pendiente') {
            $servicioSocial->estatus = 'en_progreso';
            $servicioSocial->save();
        }

        if ($servicioSocial->documentosCompletos() && $servicioSocial->estatus !== 'liberado') {
            $servicioSocial->estatus = 'pendiente_revision';
            $servicioSocial->save();
        }

        return redirect()->route('servicio-social.index')->with('success', 'Carta de Aceptación subida correctamente.');
    }

    // Mostrar formulario para subir Evaluación
    public function mostrarFormularioEvaluacion($id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);
        if ($servicioSocial->user_id !== Auth::id()) abort(403);
        return view('servicio_social.subir_evaluacion', compact('servicioSocial'));
    }

    public function subirEvaluacion(Request $request, $id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);
        if ($servicioSocial->user_id !== Auth::id()) abort(403);

        $request->validate([
            'archivo_pdf' => 'required|file|mimes:pdf|max:5120',
            'comentario' => 'nullable|string|max:500',
        ]);

        $tipoDocumento = TipoDocumento::where('nombre', 'Evaluación de Competencias del Desempeño')
            ->where('tramite', 'SS')
            ->first();
        if (!$tipoDocumento) {
            return redirect()->route('servicio-social.index')->with('error', 'Tipo de documento no encontrado.');
        }

        $documento = Documento::where('user_id', Auth::id())
            ->where('tipo_documento_id', $tipoDocumento->id)
            ->first();

        $path = $request->file('archivo_pdf')->store('documentos/evaluacion', 'public');

        if ($documento) {
            $documento->update(['archivo_pdf' => $path, 'estatus' => 'pendiente', 'updated_at' => now()]);
        } else {
            $documento = Documento::create([
                'user_id' => Auth::id(),
                'tipo_documento_id' => $tipoDocumento->id,
                'archivo_pdf' => $path,
                'estatus' => 'pendiente',
                'activo' => true,
            ]);
        }

        if ($request->filled('comentario')) {
            $comentario = new Comentario([
                'contenido' => $request->comentario,
                'tipo' => 'estudiante',
                'user_id' => Auth::id(),
                'comentable_id' => $documento->id,
                'comentable_type' => 'App\Models\Documento',
            ]);
            $comentario->save();
        }

        if ($servicioSocial->estatus == 'pendiente') {
            $servicioSocial->estatus = 'en_progreso';
            $servicioSocial->save();
        }

        if ($servicioSocial->documentosCompletos() && $servicioSocial->estatus !== 'liberado') {
            $servicioSocial->estatus = 'pendiente_revision';
            $servicioSocial->save();
        }

        return redirect()->route('servicio-social.index')->with('success', 'Evaluación subida correctamente.');
    }

    // Mostrar formulario para subir Liberación
    public function mostrarFormularioLiberacion($id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);
        if ($servicioSocial->user_id !== Auth::id()) abort(403);
        return view('servicio_social.subir_liberacion', compact('servicioSocial'));
    }

    public function subirLiberacion(Request $request, $id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);
        if ($servicioSocial->user_id !== Auth::id()) abort(403);

        $request->validate([
            'archivo_pdf' => 'required|file|mimes:pdf|max:5120',
            'comentario' => 'nullable|string|max:500',
        ]);

        $tipoDocumento = TipoDocumento::where('nombre', 'Carta de Liberación de Servicio Social')
            ->where('tramite', 'SS')
            ->first();
        if (!$tipoDocumento) {
            return redirect()->route('servicio-social.index')->with('error', 'Tipo de documento no encontrado.');
        }

        $documento = Documento::where('user_id', Auth::id())
            ->where('tipo_documento_id', $tipoDocumento->id)
            ->first();

        $path = $request->file('archivo_pdf')->store('documentos/liberacion', 'public');

        if ($documento) {
            $documento->update(['archivo_pdf' => $path, 'estatus' => 'pendiente', 'updated_at' => now()]);
        } else {
            $documento = Documento::create([
                'user_id' => Auth::id(),
                'tipo_documento_id' => $tipoDocumento->id,
                'archivo_pdf' => $path,
                'estatus' => 'pendiente',
                'activo' => true,
            ]);
        }

        if ($request->filled('comentario')) {
            $comentario = new Comentario([
                'contenido' => $request->comentario,
                'tipo' => 'estudiante',
                'user_id' => Auth::id(),
                'comentable_id' => $documento->id,
                'comentable_type' => 'App\Models\Documento',
            ]);
            $comentario->save();
        }

        if ($servicioSocial->estatus == 'pendiente') {
            $servicioSocial->estatus = 'en_progreso';
            $servicioSocial->save();
        }

        if ($servicioSocial->documentosCompletos() && $servicioSocial->estatus !== 'liberado') {
            $servicioSocial->estatus = 'pendiente_revision';
            $servicioSocial->save();
        }

        return redirect()->route('servicio-social.index')->with('success', 'Carta de Liberación subida correctamente.');
    }

    // Eliminar un documento específico
    public function eliminarDocumento($id, $tipoDocumentoNombre)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);
        if ($servicioSocial->user_id !== Auth::id()) abort(403);

        $documento = Documento::where('user_id', Auth::id())
            ->whereHas('tipoDocumento', function($q) use ($tipoDocumentoNombre) {
                $q->where('nombre', $tipoDocumentoNombre);
            })->first();

        if (!$documento) {
            return redirect()->route('servicio-social.index')->with('error', 'Documento no encontrado.');
        }

        if ($documento->archivo_pdf && file_exists(storage_path('app/public/' . $documento->archivo_pdf))) {
            unlink(storage_path('app/public/' . $documento->archivo_pdf));
        }

        $documento->update(['archivo_pdf' => null, 'estatus' => 'pendiente']);

        // NO actualizar el estatus del trámite si ya está LIBERADO
        if ($servicioSocial->estatus !== 'liberado') {
            if ($servicioSocial->documentosCompletos()) {
                $servicioSocial->estatus = 'pendiente_revision';
            } else {
                $servicioSocial->estatus = 'pendiente';
            }
            $servicioSocial->save();
        }

        return redirect()->route('servicio-social.index')
            ->with('success', 'Documento eliminado correctamente. Puedes volver a subirlo sin perder el historial de comentarios.');
    }

    // Eliminar un informe (Primer o Segundo Informe)
    public function eliminarInforme($id, $tipo)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);
        if ($servicioSocial->user_id !== Auth::id()) abort(403);

        if ($tipo == 'primero') {
            if ($servicioSocial->archivo_parcial && file_exists(storage_path('app/public/' . $servicioSocial->archivo_parcial))) {
                unlink(storage_path('app/public/' . $servicioSocial->archivo_parcial));
            }
            $servicioSocial->update(['reporte_parcial_subido' => false, 'archivo_parcial' => null]);
            $mensaje = 'Primer Informe eliminado correctamente.';
        } elseif ($tipo == 'segundo') {
            if ($servicioSocial->archivo_final && file_exists(storage_path('app/public/' . $servicioSocial->archivo_final))) {
                unlink(storage_path('app/public/' . $servicioSocial->archivo_final));
            }
            $servicioSocial->update(['reporte_final_subido' => false, 'archivo_final' => null]);
            $mensaje = 'Segundo Informe eliminado correctamente.';
        } else {
            return redirect()->route('servicio-social.index')->with('error', 'Tipo de informe no válido.');
        }

        // NO actualizar el estatus del trámite si ya está LIBERADO
        if ($servicioSocial->estatus !== 'liberado') {
            if ($servicioSocial->documentosCompletos()) {
                $servicioSocial->estatus = 'pendiente_revision';
            } else {
                $servicioSocial->estatus = 'pendiente';
            }
            $servicioSocial->save();
        }

        return redirect()->route('servicio-social.index')->with('success', $mensaje);
    }

    // ==============================================
    // DESCARGA DE WORD RELLENO (USANDO PHPWORD)
    // ==============================================
    public function descargarWordRelleno($id)
    {
        $servicioSocial = ServicioSocial::with('user', 'empresa', 'gradoAcademico', 'horario', 'gradoAcademicoJefe')->findOrFail($id);
        
        // Verificar que el usuario sea el dueño
        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        Carbon::setLocale('es');

        $user = $servicioSocial->user;
        
        // Datos para reemplazar en la plantilla
        $variables = [
            'nombre_completo' => trim($user->name . '' . $user->apellidos), // ← NUEVA LÍNEA
            'nombre' => $user->name,
            'apellidos' => $user->apellidos,
            'matricula' => $user->matricula,
            'grupo' => $user->grupo ?? '',
            'carrera' => $user->carrera,
            'semestre' => $user->semestre,
            'turno' => $user->nombre_turno,
            'generacion' => $user->nombre_periodo_actual,
            'fecha_inicio' => $servicioSocial->fecha_inicio ? Carbon::parse($servicioSocial->fecha_inicio)->translatedFormat('d \d\e F \d\e Y') : '',
            'fecha_finalizacion' => $servicioSocial->fecha_limite_segundo_informe ? Carbon::parse($servicioSocial->fecha_limite_segundo_informe)->translatedFormat('d \d\e F \d\e Y') : '',
            'horario' => $servicioSocial->horario ? $servicioSocial->horario->hora_inicio . ' - ' . $servicioSocial->horario->hora_fin : '',
            'empresa' => $servicioSocial->empresa->nombre ?? '',
            'grado_academico' => $servicioSocial->gradoAcademico->abreviatura ?? '',
            'nombre_persona_carta' => $servicioSocial->nombre_persona_carta,
            'cargo_persona_carta' => $servicioSocial->cargo_persona_carta,
            'grado_academico_jefe' => $servicioSocial->gradoAcademicoJefe->abreviatura ?? '',
            'nombre_jefe_inmediato' => $servicioSocial->nombre_jefe_inmediato,
            'cargo_jefe_inmediato' => $servicioSocial->cargo_jefe_inmediato,
            'area_asignada' => $servicioSocial->area_asignada,
            'apoyo_estudiante' => $servicioSocial->apoyo_estudiante,
        ];

        // Cargar la plantilla usando TemplateProcessor
        $templatePath = storage_path('app/templates/solicitud_plantilla.docx');
        
        // Verificar que la plantilla existe
        if (!file_exists($templatePath)) {
            return redirect()->route('servicio-social.index')
                ->with('error', 'No se encontró la plantilla de solicitud.');
        }
        
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        // Reemplazar variables
        foreach ($variables as $key => $value) {
            $templateProcessor->setValue($key, $value);
        }

        // Crear carpeta temporal si no existe
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        // Guardar archivo temporal
        $tempPath = storage_path('app/temp/solicitud_' . $user->matricula . '.docx');
        $templateProcessor->saveAs($tempPath);

        // Descargar
        return response()->download($tempPath, 'solicitud_' . $user->matricula . '.docx')->deleteFileAfterSend(true);
    }

    // ==============================================
    // DESCARGA DE PDF DESDE HTML (OPCIONAL)
    // ==============================================
    public function descargarPlantillaPreliminar($id)
    {
        $servicioSocial = ServicioSocial::with('user')->findOrFail($id);
        
        // Verificar que el usuario sea el dueño
        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            // Obtener datos del usuario
            $user = $servicioSocial->user;
            
            // Separar nombre y apellidos
            $nombreParts = explode(' ', $user->name ?? '');
            $nombre = $nombreParts[0] ?? '';
            $apellidos = implode(' ', array_slice($nombreParts, 1)) ?? '';

            Carbon::setLocale('es');
            
            // Preparar los placeholders
            $datos = [
                'nombre' => $nombre,
                'apellidos' => $apellidos,
                'matricula' => $user->matricula ?? '',
                'turno' => $user->turno ?? '',
                'semestre' => $user->semestre ?? '',
                'carrera' => $user->carrera ?? '',
                'generacion' => $user->generacion ?? '',
                'fecha_inicio' => $servicioSocial->fecha_inicio ? \Carbon\Carbon::parse($servicioSocial->fecha_inicio)->translatedFormat('d \\d\\e F \\d\\e Y') : '',
                'fecha_finalizacion' => $servicioSocial->fecha_termino ? \Carbon\Carbon::parse($servicioSocial->fecha_termino)->translatedFormat('d \\d\\e F \\d\\e Y') : '',
                'horario' => $servicioSocial->horario ?? '',
                'nombre_persona_carta' => $servicioSocial->nombre_supervisor ?? '',
                'cargo_persona_carta' => $servicioSocial->cargo_persona_carta ?? '',
                'grado_academico_jefe' => $servicioSocial->gradoAcademicoJefe->abreviatura ?? '',
                'nombre_jefe_inmediato' => $servicioSocial->nombre_jefe_inmediato ?? '',
                'cargo_jefe_inmediato' => $servicioSocial->cargo_jefe_inmediato ?? '',
                'grado_academico' => $servicioSocial->grado_academico ?? '',
                'empresa' => $servicioSocial->nombre_empresa ?? '',
                'area_asignada' => $servicioSocial->departamento ?? '',
                'apoyo_estudiante' => $servicioSocial->apoyo_estudiante ?? '',
            ];
            
            // Generar HTML
            $html = $this->generarHtmlSolicitud($datos);
            
            // Configurar Dompdf
            $options = new Options();
            $options->set('defaultFont', 'Arial');
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', false);
            $dompdf = new Dompdf($options);
            
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            // Nombre del archivo
            $nombreArchivo = 'solicitud_servicio_social_' . ($user->matricula ?? $user->id) . '.pdf';
            
            return $dompdf->download($nombreArchivo);
            
        } catch (\Exception $e) {
            \Log::error('Error generando plantilla preliminar: ' . $e->getMessage());
            return redirect()->route('servicio-social.index')
                ->with('error', 'Error al generar la solicitud: ' . $e->getMessage());
        }
    }
    
    /**
     * Genera el HTML de la solicitud con los datos reemplazados
     */
    private function generarHtmlSolicitud($datos)
    {
        $nombreCompleto = trim(($datos['nombre'] ?? '') . ' ' . ($datos['apellidos'] ?? ''));
        
        return '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Solicitud de Servicio Social</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 11pt;
                    margin: 60px 50px;
                    line-height: 1.4;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                }
                .header strong, .header em {
                    display: block;
                }
                .title {
                    text-align: center;
                    font-weight: bold;
                    margin: 30px 0 20px 0;
                    text-decoration: underline;
                }
                .importante {
                    font-size: 9pt;
                    font-style: italic;
                    margin: 15px 0;
                    text-align: justify;
                }
                .datos {
                    margin: 15px 0;
                }
                h4 {
                    margin: 20px 0 10px 0;
                    background: #f0f0f0;
                    padding: 5px;
                }
                .firma-container {
                    display: flex;
                    justify-content: space-between;
                    margin-top: 50px;
                }
                .firma {
                    text-align: center;
                    width: 45%;
                }
                .footer {
                    font-size: 8pt;
                    text-align: center;
                    margin-top: 40px;
                    border-top: 1px solid #ccc;
                    padding-top: 10px;
                }
                hr {
                    margin: 15px 0;
                }
                u {
                    text-decoration: underline;
                }
                .nota-final {
                    font-size: 9pt;
                    text-align: justify;
                    margin-top: 30px;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <strong>Colegio de Educación Profesional Técnica del Estado de Quintana Roo.</strong>
                <em>ORGANISMO PÚBLICO DESCENTRALIZADO</em>
                <strong>Plantel Cancún II</strong>
            </div>
            
            <div style="text-align: center; margin-top: 20px;">
                <strong>JEFATURA DE PROYECTO DE PROMOCIÓN Y VINCULACIÓN</strong>
            </div>
            
            <div class="title">
                SOLICITUD DE INSCRIPCIÓN AL PROGRAMA DE SERVICIO SOCIAL ( ) PRÁCTICAS PROFESIONALES ( )
            </div>
            
            <div class="importante">
                <strong>LEER, IMPORTANTE:</strong> De acuerdo al título cuarto y del Capítulo IV art.135 al 141 y capitulo V art. 142 al 149 de las Reglas de convivencia Escolar del Sistema Nacional de Colegios de Educación Profesional Técnica se presenta la siguiente solicitud, el cual debe ser debidamente llenada por el estudiante, firmada y sellada por la institución o empresa que reciba al estudiante para que el colegio le extienda posteriormente la carta de presentación.
            </div>
            
            <hr>
            
            <h4>DATOS DEL ESTUDIANTE</h4>
            <div class="datos">
                Nombre: <u><strong>' . htmlspecialchars($nombreCompleto) . '</strong></u><br>
                Grupo: _____________ Matrícula: <u><strong>' . htmlspecialchars($datos['matricula'] ?? '_____________') . '</strong></u><br>
                Turno: <u><strong>' . htmlspecialchars($datos['turno'] ?? '_____________') . '</strong></u> Semestre: <u><strong>' . htmlspecialchars($datos['semestre'] ?? '_____________') . '°</strong></u><br>
                Carrera: <u><strong>' . htmlspecialchars($datos['carrera'] ?? '_____________') . '</strong></u> Generación: <u><strong>' . htmlspecialchars($datos['generacion'] ?? '_____________') . '</strong></u>
            </div>
            
            <h4>FECHA DE INICIO Y TÉRMINO</h4>
            <div class="datos">
                Del día <u><strong>' . htmlspecialchars($datos['fecha_inicio'] ?? '_____________') . '</strong></u> al día <u><strong>' . htmlspecialchars($datos['fecha_finalizacion'] ?? '_____________') . '</strong></u><br>
                En el Horario de: <u><strong>' . htmlspecialchars($datos['horario'] ?? '_____________') . '</strong></u>, cubriendo 4 hrs. al día de Lunes a viernes con un total de horas SS: 480 ( ) PP: 360( ) a lo largo de su servicio/prácticas
            </div>
            
            <h4>DATOS DE LA INSTITUCIÓN O EMPRESA</h4>
            <div class="datos">
                Grado académico, nombre completo y cargo de a quien irá dirigida la carta de presentación:<br>
                <u><strong>' . htmlspecialchars($datos['nombre_persona_carta'] ?? '_________________________') . '</strong></u><br><br>
                Grado académico, nombre y cargo del jefe inmediato:<br>
                <u><strong>' . htmlspecialchars($datos['grado_academico'] ?? '_________________________') . ' ' . htmlspecialchars($datos['nombre_persona_carta'] ?? '_________________________') . '</strong></u><br><br>
                Institución/Empresa: <u><strong>' . htmlspecialchars($datos['empresa'] ?? '_________________________') . '</strong></u><br><br>
                Área asignada: <u><strong>' . htmlspecialchars($datos['area_asignada'] ?? '_________________________') . '</strong></u><br><br>
                Tipo de apoyo brindado al estudiante:<br>
                <u><strong>' . htmlspecialchars($datos['apoyo_estudiante'] ?? '_________________________') . '</strong></u>
            </div>
            
            <div class="nota-final">
                <strong>LEER, IMPORTANTE:</strong> Con el fin de dar cumplimiento a los prescrito por la Ley Reglamentaria del Artículo 5º Constitucional, el suscrito acepta sujetarse al reglamento correspondiente y cumplir con el periodo manifestado, así como observar una conducta ejemplar durante su permanencia de lo contrario no le será extendida la constancia que lo acredite por la prestación de dicho servicio o práctica; igualmente, la institución o la empresa notificará al colegio los hechos en los que incurra el estudiante o cualquier evento a favor del mismo.
            </div>
            
            <div class="firma-container">
                <div class="firma">
                    <br><br><br><br>
                    <u>_________________________</u><br>
                    <strong>NOMBRE Y FIRMA DEL ALUMNO</strong>
                </div>
                <div class="firma">
                    <br><br><br><br>
                    <u>_________________________</u><br>
                    <strong>SELLO Y FIRMA DE LA EMPRESA/INSTITUCIÓN</strong>
                </div>
            </div>
            
            <div class="footer">
                Región 228, Mza 5, Lote 1, Av. 20 de Nov. Entre costa maya y calle 61, zona 4, Cancún, Quintana Roo, CP. 77516<br>
                Certificado conforme a los requisitos de la norma ISO 9001:2008<br>
                Teléfono y Fax (01 998) 2710194 - e-mail: vinculacion.cancun2@qroo.conalep.edu.mx
            </div>
        </body>
        </html>';
    }
}