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
            return view('servicio_social.no_solicitado');
        }

        // ✅ AGREGAR INFORMES A LA LISTA DE DOCUMENTOS
        $documentosAdministrativos = [
            'Solicitud de Servicio Social',
            'Elección de Modalidad',
            'Carta de Presentación de Servicio Social',
            'Carta de Aceptación',
            'Primer Informe de Actividades Trimestral',   // ✅ NUEVO
            'Segundo Informe de Actividades Trimestral',  // ✅ NUEVO
            'Evaluación de Competencias del Desempeño',
            'Carta de Liberación de Servicio Social'
        ];

        $comentariosPorDocumento = [];

        // ✅ TODOS los documentos (incluyendo informes) se buscan en documentos
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

        // ❌ ELIMINAR $comentariosPorInforme
        return view('servicio_social.index', compact('servicioSocial', 'comentariosPorDocumento'));
    }

    // ============================================================
    // 📌 REPORTE PARCIAL (Primer Informe)
    // ============================================================

    // Mostrar formulario para subir reporte parcial (Primer Informe)
    public function mostrarFormularioReporteParcial($id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        $fechaLimite = $servicioSocial->fecha_limite_primer_informe;
        $fechaHoy = now()->startOfDay();
        $fechaFormateada = $fechaLimite ? Carbon::parse($fechaLimite)->format('d/m/Y') : 'No definida';
        
        $fechaInicio = $servicioSocial->fecha_inicio ? Carbon::parse($servicioSocial->fecha_inicio) : null;
        $horasCompletadas = 0;
        
        if ($fechaInicio && $fechaHoy->greaterThanOrEqualTo($fechaInicio)) {
            $diasTranscurridos = $fechaInicio->diffInDays($fechaHoy);
            $horasCompletadas = $diasTranscurridos * 4;
            $horasCompletadas = min($horasCompletadas, 480);
        }
        
        if (!$fechaLimite) {
            return redirect()->route('servicio-social.index')
                ->with('error', 'No hay fecha límite definida para el Primer Informe. Contacta al administrador.');
        }

        $diasRestantes = $fechaHoy->diffInDays($fechaLimite, false);

        if ($diasRestantes > 5) {
            $fechaInicioSubida = Carbon::parse($fechaLimite)->subDays(5)->format('d/m/Y');
            return redirect()->route('servicio-social.index')
                ->with('error', 'Aún no puedes subir el Primer Informe. La fecha límite es el ' . $fechaFormateada . '. Podrás subirlo a partir del ' . $fechaInicioSubida . '.');
        }

        $estaVencido = $diasRestantes < -5;

        if ($diasRestantes < 0 && $diasRestantes >= -5) {
            $fechaFinPrórroga = Carbon::parse($fechaLimite)->addDays(5)->format('d/m/Y');
            session()->flash('warning', 'El plazo oficial venció el ' . $fechaFormateada . '. Tienes 5 días adicionales (hasta el ' . $fechaFinPrórroga . ') para subir el informe.');
        }

        return view('servicio_social.subir_reporte_parcial', compact('servicioSocial', 'fechaLimite', 'fechaFormateada', 'estaVencido', 'diasRestantes', 'horasCompletadas'));
    }

    // Procesar la subida del reporte parcial (Primer Informe)
    public function subirReporteParcial(Request $request, $id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        // ✅ VALIDACIONES DE FECHAS (se mantienen igual)
        $fechaLimite = $servicioSocial->fecha_limite_primer_informe;
        $fechaHoy = now()->startOfDay();

        if (!$fechaLimite) {
            return redirect()->route('servicio-social.index')
                ->with('error', 'No hay fecha límite definida para el Primer Informe.');
        }

        $diasRestantes = $fechaHoy->diffInDays($fechaLimite, false);

        if ($diasRestantes > 5) {
            $fechaInicioSubida = Carbon::parse($fechaLimite)->subDays(5)->format('d/m/Y');
            return redirect()->route('servicio-social.index')
                ->with('error', 'Aún no puedes subir el Primer Informe. La fecha límite es el ' . Carbon::parse($fechaLimite)->format('d/m/Y') . '. Podrás subirlo a partir del ' . $fechaInicioSubida . '.');
        }

        // ✅ VALIDACIÓN DEL ARCHIVO
        $request->validate([
            'reporte_pdf' => 'required|file|mimes:pdf|max:5120',
            'comentario' => 'nullable|string|max:500',
        ]);

        // ✅ BUSCAR TIPO DE DOCUMENTO (igual que subirModalidad)
        $tipoDocumento = TipoDocumento::where('nombre', 'Primer Informe de Actividades Trimestral')
            ->where('tramite', 'SS')
            ->first();

        if (!$tipoDocumento) {
            return redirect()->route('servicio-social.index')
                ->with('error', 'Tipo de documento no encontrado. Contacta al administrador.');
        }

        // ✅ GUARDAR EN DOCUMENTOS (igual que subirModalidad)
        $path = $request->file('reporte_pdf')->store('reportes_ss_parcial', 'public');

        $documento = Documento::where('user_id', Auth::id())
            ->where('tipo_documento_id', $tipoDocumento->id)
            ->first();

        if ($documento) {
            // ✅ DOCUMENTO EXISTENTE: SOLO actualizar archivo, NO el estatus
            if ($documento->archivo_pdf && file_exists(storage_path('app/public/' . $documento->archivo_pdf))) {
                unlink(storage_path('app/public/' . $documento->archivo_pdf));
            }
            $documento->update([
                'archivo_pdf' => $path,
                'updated_at' => now(),
                // ❌ NO se toca 'estatus'
            ]);
        } else {
            // ✅ DOCUMENTO NUEVO: estatus = 'pendiente'
            $documento = Documento::create([
                'user_id' => Auth::id(),
                'tipo_documento_id' => $tipoDocumento->id,
                'archivo_pdf' => $path,
                'estatus' => 'pendiente',
                'activo' => true,
            ]);
        }

        // ✅ COMENTARIOS (igual que subirModalidad)
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

        return redirect()->route('servicio-social.index')
            ->with('success', 'Primer Informe subido correctamente.');
    }

    // ============================================================
    // 📌 REPORTE FINAL (Segundo Informe)
    // ============================================================

    // Mostrar formulario para subir reporte final (Segundo Informe)
    public function mostrarFormularioReporteFinal($id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        $fechaLimite = $servicioSocial->fecha_limite_segundo_informe;
        $fechaHoy = now()->startOfDay();
        $fechaFormateada = $fechaLimite ? Carbon::parse($fechaLimite)->format('d/m/Y') : 'No definida';
        
        $fechaInicio = $servicioSocial->fecha_inicio ? Carbon::parse($servicioSocial->fecha_inicio) : null;
        $horasCompletadas = 0;
        
        if ($fechaInicio && $fechaHoy->greaterThanOrEqualTo($fechaInicio)) {
            $diasTranscurridos = $fechaInicio->diffInDays($fechaHoy);
            $horasCompletadas = $diasTranscurridos * 4;
            $horasCompletadas = min($horasCompletadas, 480);
        }
        
        if (!$fechaLimite) {
            return redirect()->route('servicio-social.index')
                ->with('error', 'No hay fecha límite definida para el Segundo Informe. Contacta al administrador.');
        }

        $diasRestantes = $fechaHoy->diffInDays($fechaLimite, false);

        if ($diasRestantes > 5) {
            $fechaInicioSubida = Carbon::parse($fechaLimite)->subDays(5)->format('d/m/Y');
            return redirect()->route('servicio-social.index')
                ->with('error', 'Aún no puedes subir el Segundo Informe. La fecha límite es el ' . $fechaFormateada . '. Podrás subirlo a partir del ' . $fechaInicioSubida . '.');
        }

        $estaVencido = $diasRestantes < -5;

        if ($diasRestantes < 0 && $diasRestantes >= -5) {
            $fechaFinPrórroga = Carbon::parse($fechaLimite)->addDays(5)->format('d/m/Y');
            session()->flash('warning', 'El plazo oficial venció el ' . $fechaFormateada . '. Tienes 5 días adicionales (hasta el ' . $fechaFinPrórroga . ') para subir el informe.');
        }

        return view('servicio_social.subir_reporte_final', compact('servicioSocial', 'fechaLimite', 'fechaFormateada', 'estaVencido', 'diasRestantes', 'horasCompletadas'));
    }

    // Procesar la subida del reporte final (Segundo Informe)
    public function subirReporteFinal(Request $request, $id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        // ✅ VALIDACIONES DE FECHAS (se mantienen igual)
        $fechaLimite = $servicioSocial->fecha_limite_segundo_informe;
        $fechaHoy = now()->startOfDay();

        if (!$fechaLimite) {
            return redirect()->route('servicio-social.index')
                ->with('error', 'No hay fecha límite definida para el Segundo Informe.');
        }

        $diasRestantes = $fechaHoy->diffInDays($fechaLimite, false);

        if ($diasRestantes > 5) {
            $fechaInicioSubida = Carbon::parse($fechaLimite)->subDays(5)->format('d/m/Y');
            return redirect()->route('servicio-social.index')
                ->with('error', 'Aún no puedes subir el Segundo Informe. La fecha límite es el ' . Carbon::parse($fechaLimite)->format('d/m/Y') . '. Podrás subirlo a partir del ' . $fechaInicioSubida . '.');
        }

        // ✅ VALIDACIÓN DEL ARCHIVO
        $request->validate([
            'reporte_pdf' => 'required|file|mimes:pdf|max:5120',
            'comentario' => 'nullable|string|max:500',
        ]);

        // ✅ BUSCAR TIPO DE DOCUMENTO (igual que subirModalidad)
        $tipoDocumento = TipoDocumento::where('nombre', 'Segundo Informe de Actividades Trimestral')
            ->where('tramite', 'SS')
            ->first();

        if (!$tipoDocumento) {
            return redirect()->route('servicio-social.index')
                ->with('error', 'Tipo de documento no encontrado. Contacta al administrador.');
        }

        // ✅ GUARDAR EN DOCUMENTOS (igual que subirModalidad)
        $path = $request->file('reporte_pdf')->store('reportes_ss_final', 'public');

        $documento = Documento::where('user_id', Auth::id())
            ->where('tipo_documento_id', $tipoDocumento->id)
            ->first();

        if ($documento) {
            // ✅ DOCUMENTO EXISTENTE: SOLO actualizar archivo, NO el estatus
            if ($documento->archivo_pdf && file_exists(storage_path('app/public/' . $documento->archivo_pdf))) {
                unlink(storage_path('app/public/' . $documento->archivo_pdf));
            }
            $documento->update([
                'archivo_pdf' => $path,
                'updated_at' => now(),
                // ❌ NO se toca 'estatus'
            ]);
        } else {
            // ✅ DOCUMENTO NUEVO: estatus = 'pendiente'
            $documento = Documento::create([
                'user_id' => Auth::id(),
                'tipo_documento_id' => $tipoDocumento->id,
                'archivo_pdf' => $path,
                'estatus' => 'pendiente',
                'activo' => true,
            ]);
        }

        // ✅ COMENTARIOS (igual que subirModalidad)
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

        return redirect()->route('servicio-social.index')
            ->with('success', 'Segundo Informe subido correctamente.');
    }

    // ============================================================
    // 📌 DOCUMENTOS ADMINISTRATIVOS (SIN CAMBIOS)
    // ============================================================

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
            if ($documento->archivo_pdf && file_exists(storage_path('app/public/' . $documento->archivo_pdf))) {
                unlink(storage_path('app/public/' . $documento->archivo_pdf));
            }
            $documento->update([
                'archivo_pdf' => $path,
                'updated_at' => now(),
                // ❌ NO se toca 'estatus'
            ]);
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
            if ($documento->archivo_pdf && file_exists(storage_path('app/public/' . $documento->archivo_pdf))) {
                unlink(storage_path('app/public/' . $documento->archivo_pdf));
            }
            $documento->update([
                'archivo_pdf' => $path,
                'updated_at' => now(),
                // ❌ NO se toca 'estatus'
            ]);
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
            if ($documento->archivo_pdf && file_exists(storage_path('app/public/' . $documento->archivo_pdf))) {
                unlink(storage_path('app/public/' . $documento->archivo_pdf));
            }
            $documento->update([
                'archivo_pdf' => $path,
                'updated_at' => now(),
                // ❌ NO se toca 'estatus'
            ]);
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
            if ($documento->archivo_pdf && file_exists(storage_path('app/public/' . $documento->archivo_pdf))) {
                unlink(storage_path('app/public/' . $documento->archivo_pdf));
            }
            $documento->update([
                'archivo_pdf' => $path,
                'updated_at' => now(),
                // ❌ NO se toca 'estatus'
            ]);
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
            if ($documento->archivo_pdf && file_exists(storage_path('app/public/' . $documento->archivo_pdf))) {
                unlink(storage_path('app/public/' . $documento->archivo_pdf));
            }
            $documento->update([
                'archivo_pdf' => $path,
                'updated_at' => now(),
                // ❌ NO se toca 'estatus'
            ]);
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
            if ($documento->archivo_pdf && file_exists(storage_path('app/public/' . $documento->archivo_pdf))) {
                unlink(storage_path('app/public/' . $documento->archivo_pdf));
            }
            $documento->update([
                'archivo_pdf' => $path,
                'updated_at' => now(),
                // ❌ NO se toca 'estatus'
            ]);
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

        return redirect()->route('servicio-social.index')->with('success', 'Carta de Liberación subida correctamente.');
    }

    // ============================================================
    // 📌 ELIMINAR DOCUMENTO (GENERAL - PARA TODOS LOS DOCUMENTOS)
    // ============================================================

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

        return redirect()->route('servicio-social.index')
            ->with('success', 'Documento eliminado correctamente. Puedes volver a subirlo sin perder el historial de comentarios.');
    }

    // ============================================================
    // 📌 DESCARGA DE WORD RELLENO (USANDO PHPWORD)
    // ============================================================
    public function descargarWordRelleno($id)
    {
        $servicioSocial = ServicioSocial::with('user', 'empresa', 'gradoAcademico', 'horario', 'gradoAcademicoJefe')->findOrFail($id);
        
        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        Carbon::setLocale('es');

        $user = $servicioSocial->user;
        
        $variables = [
            'nombre_completo' => trim($user->name . ' ' . $user->apellidos),
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

        $templatePath = storage_path('app/templates/solicitud_plantilla.docx');
        
        if (!file_exists($templatePath)) {
            return redirect()->route('servicio-social.index')
                ->with('error', 'No se encontró la plantilla de solicitud.');
        }
        
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        foreach ($variables as $key => $value) {
            $templateProcessor->setValue($key, $value);
        }

        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $tempPath = storage_path('app/temp/solicitud_' . $user->matricula . '.docx');
        $templateProcessor->saveAs($tempPath);

        return response()->download($tempPath, 'solicitud_' . $user->matricula . '.docx')->deleteFileAfterSend(true);
    }
}