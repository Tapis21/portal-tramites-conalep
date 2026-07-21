<?php

namespace App\Http\Controllers;

use App\Models\Practica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Documento;
use App\Models\TipoDocumento;

use App\Models\Comentario;

use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;

class PracticaController extends Controller
{
    // Muestra el progreso de las Prácticas del estudiante autenticado
    public function index()
    {
        $user = Auth::user();
        $servicioSocial = $user->servicioSocial;

        // Validar que el Servicio Social esté liberado
        if (!$servicioSocial || $servicioSocial->estatus !== 'liberado') {
            return view('practicas.requisito');
        }

        $practica = $user->practicas;

        // Si no existe el registro, mostrar vista "no solicitado"
        if (!$practica) {
            return view('practicas.no_solicitado');
        }

        // ✅ AGREGAR INFORMES A LA LISTA DE DOCUMENTOS
        $documentosAdministrativos = [
            'Solicitud de Prácticas Profesionales',
            'Elección de Modalidad',
            'Carta de Presentación de Prácticas Profesionales',
            'Carta de Aceptación',
            'Primer Informe de Actividades',   // ✅ NUEVO
            'Segundo Informe de Actividades',  // ✅ NUEVO
            'Evaluación de Competencias del Desempeño',
            'Carta de Liberación de Prácticas Profesionales'
        ];

        $comentariosPorDocumento = [];

        // ✅ TODOS los documentos (incluyendo informes) se buscan en documentos
        foreach ($documentosAdministrativos as $nombre) {
            $doc = Documento::where('user_id', Auth::id())
                ->whereHas('tipoDocumento', function($q) use ($nombre) {
                    $q->where('nombre', $nombre)
                      ->where('tramite', 'PP');
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
        return view('practicas.index', compact('practica', 'comentariosPorDocumento'));
    }

    // ============================================================
    // 📌 REPORTE PARCIAL (Primer Informe - 180h)
    // ============================================================

    // Mostrar formulario para subir reporte parcial (Primer Informe - 180h)
    public function mostrarFormularioReporteParcial($id)
    {
        $practica = Practica::findOrFail($id);

        if ($practica->user_id !== Auth::id()) {
            abort(403);
        }

        $fechaLimite = $practica->fecha_limite_parcial;
        $fechaHoy = now()->startOfDay();
        $fechaFormateada = $fechaLimite ? Carbon::parse($fechaLimite)->format('d/m/Y') : 'No definida';
        
        $fechaInicio = $practica->fecha_inicio ? Carbon::parse($practica->fecha_inicio) : null;
        $horasCompletadas = 0;
        
        if ($fechaInicio && $fechaHoy->greaterThanOrEqualTo($fechaInicio)) {
            $diasTranscurridos = $fechaInicio->diffInDays($fechaHoy);
            $horasCompletadas = $diasTranscurridos * 4;
            $horasCompletadas = min($horasCompletadas, 360);
        }
        
        $estaVencido = false;
        $diasRestantes = null;
        
        if ($fechaLimite) {
            $diasRestantes = $fechaHoy->diffInDays($fechaLimite, false);
            $estaVencido = $diasRestantes < -5;
        }

        if ($diasRestantes !== null && $diasRestantes < 0 && $diasRestantes >= -5) {
            $fechaFinPrórroga = Carbon::parse($fechaLimite)->addDays(5)->format('d/m/Y');
            session()->flash('warning', 'El plazo oficial venció el ' . $fechaFormateada . '. Tienes 5 días adicionales (hasta el ' . $fechaFinPrórroga . ') para subir el informe.');
        }

        if ($diasRestantes !== null && $diasRestantes > 5) {
            $fechaInicioSubida = Carbon::parse($fechaLimite)->subDays(5)->format('d/m/Y');
            session()->flash('info', 'La fecha límite para subir es el ' . $fechaFormateada . '. Podrás subirlo a partir del ' . $fechaInicioSubida . '.');
        }

        return view('practicas.subir_reporte_parcial', compact('practica', 'fechaLimite', 'fechaFormateada', 'estaVencido', 'diasRestantes', 'horasCompletadas'));
    }

    // Procesar la subida del reporte parcial (Primer Informe - 180h)
    public function subirReporteParcial(Request $request, $id)
    {
        $practica = Practica::findOrFail($id);

        if ($practica->user_id !== Auth::id()) {
            abort(403);
        }

        // ✅ VALIDACIONES DE FECHAS (se mantienen igual)
        $fechaLimite = $practica->fecha_limite_parcial;
        $fechaHoy = now()->startOfDay();

        if (!$fechaLimite) {
            return redirect()->route('practicas.index')
                ->with('error', 'No hay fecha límite definida para el Primer Informe.');
        }

        $diasRestantes = $fechaHoy->diffInDays($fechaLimite, false);

        if ($diasRestantes > 5) {
            $fechaInicioSubida = Carbon::parse($fechaLimite)->subDays(5)->format('d/m/Y');
            return redirect()->route('practicas.index')
                ->with('error', 'Aún no puedes subir el Primer Informe. La fecha límite es el ' . Carbon::parse($fechaLimite)->format('d/m/Y') . '. Podrás subirlo a partir del ' . $fechaInicioSubida . '.');
        }

        // ✅ VALIDACIÓN DEL ARCHIVO
        $request->validate([
            'reporte_pdf' => 'required|file|mimes:pdf|max:5120',
            'comentario' => 'nullable|string|max:500',
        ]);

        // ✅ BUSCAR TIPO DE DOCUMENTO (igual que subirSolicitud)
        $tipoDocumento = TipoDocumento::where('nombre', 'Primer Informe de Actividades')
            ->where('tramite', 'PP')
            ->first();

        if (!$tipoDocumento) {
            return redirect()->route('practicas.index')
                ->with('error', 'Tipo de documento no encontrado. Contacta al administrador.');
        }

        // ✅ GUARDAR EN DOCUMENTOS (igual que subirSolicitud)
        $path = $request->file('reporte_pdf')->store('reportes_pp_parcial', 'public');

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

        // ✅ COMENTARIOS (igual que subirSolicitud)
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

        return redirect()->route('practicas.index')
            ->with('success', 'Primer Informe subido correctamente.');
    }

    // ============================================================
    // 📌 REPORTE FINAL (Segundo Informe - 360h)
    // ============================================================

    // Mostrar formulario para subir reporte final (Segundo Informe - 360h)
    public function mostrarFormularioReporteFinal($id)
    {
        $practica = Practica::findOrFail($id);

        if ($practica->user_id !== Auth::id()) {
            abort(403);
        }

        $fechaLimite = $practica->fecha_limite_final;
        $fechaHoy = now()->startOfDay();
        $fechaFormateada = $fechaLimite ? Carbon::parse($fechaLimite)->format('d/m/Y') : 'No definida';
        
        $fechaInicio = $practica->fecha_inicio ? Carbon::parse($practica->fecha_inicio) : null;
        $horasCompletadas = 0;
        
        if ($fechaInicio && $fechaHoy->greaterThanOrEqualTo($fechaInicio)) {
            $diasTranscurridos = $fechaInicio->diffInDays($fechaHoy);
            $horasCompletadas = $diasTranscurridos * 4;
            $horasCompletadas = min($horasCompletadas, 360);
        }
        
        $estaVencido = false;
        $diasRestantes = null;
        
        if ($fechaLimite) {
            $diasRestantes = $fechaHoy->diffInDays($fechaLimite, false);
            $estaVencido = $diasRestantes < -5;
        }

        if ($diasRestantes !== null && $diasRestantes < 0 && $diasRestantes >= -5) {
            $fechaFinPrórroga = Carbon::parse($fechaLimite)->addDays(5)->format('d/m/Y');
            session()->flash('warning', 'El plazo oficial venció el ' . $fechaFormateada . '. Tienes 5 días adicionales (hasta el ' . $fechaFinPrórroga . ') para subir el informe.');
        }

        if ($diasRestantes !== null && $diasRestantes > 5) {
            $fechaInicioSubida = Carbon::parse($fechaLimite)->subDays(5)->format('d/m/Y');
            session()->flash('info', 'La fecha límite para subir es el ' . $fechaFormateada . '. Podrás subirlo a partir del ' . $fechaInicioSubida . '.');
        }

        return view('practicas.subir_reporte_final', compact('practica', 'fechaLimite', 'fechaFormateada', 'estaVencido', 'diasRestantes', 'horasCompletadas'));
    }

    // Procesar la subida del reporte final (Segundo Informe - 360h)
    public function subirReporteFinal(Request $request, $id)
    {
        $practica = Practica::findOrFail($id);

        if ($practica->user_id !== Auth::id()) {
            abort(403);
        }

        // ✅ VALIDACIONES DE FECHAS (se mantienen igual)
        $fechaLimite = $practica->fecha_limite_final;
        $fechaHoy = now()->startOfDay();

        if (!$fechaLimite) {
            return redirect()->route('practicas.index')
                ->with('error', 'No hay fecha límite definida para el Segundo Informe.');
        }

        $diasRestantes = $fechaHoy->diffInDays($fechaLimite, false);

        if ($diasRestantes > 5) {
            $fechaInicioSubida = Carbon::parse($fechaLimite)->subDays(5)->format('d/m/Y');
            return redirect()->route('practicas.index')
                ->with('error', 'Aún no puedes subir el Segundo Informe. La fecha límite es el ' . Carbon::parse($fechaLimite)->format('d/m/Y') . '. Podrás subirlo a partir del ' . $fechaInicioSubida . '.');
        }

        // ✅ VALIDACIÓN DEL ARCHIVO
        $request->validate([
            'reporte_pdf' => 'required|file|mimes:pdf|max:5120',
            'comentario' => 'nullable|string|max:500',
        ]);

        // ✅ BUSCAR TIPO DE DOCUMENTO (igual que subirSolicitud)
        $tipoDocumento = TipoDocumento::where('nombre', 'Segundo Informe de Actividades')
            ->where('tramite', 'PP')
            ->first();

        if (!$tipoDocumento) {
            return redirect()->route('practicas.index')
                ->with('error', 'Tipo de documento no encontrado. Contacta al administrador.');
        }

        // ✅ GUARDAR EN DOCUMENTOS (igual que subirSolicitud)
        $path = $request->file('reporte_pdf')->store('reportes_pp_final', 'public');

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

        // ✅ COMENTARIOS (igual que subirSolicitud)
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

        return redirect()->route('practicas.index')
            ->with('success', 'Segundo Informe subido correctamente.');
    }

    // ============================================================
    // 📌 DOCUMENTOS ADMINISTRATIVOS (SIN CAMBIOS)
    // ============================================================

    // Mostrar formulario para subir solicitud
    public function mostrarFormularioSolicitud($id)
    {
        $practica = Practica::findOrFail($id);
        if ($practica->user_id !== Auth::id()) abort(403);
        return view('practicas.subir_solicitud', compact('practica'));
    }

    public function subirSolicitud(Request $request, $id)
    {
        $practica = Practica::findOrFail($id);
        if ($practica->user_id !== Auth::id()) abort(403);

        $request->validate([
            'archivo_pdf' => 'required|file|mimes:pdf|max:5120',
            'comentario' => 'nullable|string|max:500',
        ]);

        $tipoDocumento = TipoDocumento::where('nombre', 'Solicitud de Prácticas Profesionales')
            ->where('tramite', 'PP')
            ->first();
        if (!$tipoDocumento) {
            return redirect()->route('practicas.index')->with('error', 'Tipo de documento no encontrado.');
        }

        $documento = Documento::where('user_id', Auth::id())
            ->where('tipo_documento_id', $tipoDocumento->id)
            ->first();

        $path = $request->file('archivo_pdf')->store('documentos/solicitudes_pp', 'public');

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

        return redirect()->route('practicas.index')
            ->with('success', 'Solicitud subida correctamente.');
    }

    // Mostrar formulario para subir Elección de Modalidad
    public function mostrarFormularioModalidad($id)
    {
        $practica = Practica::findOrFail($id);
        if ($practica->user_id !== Auth::id()) abort(403);
        return view('practicas.subir_modalidad', compact('practica'));
    }

    public function subirModalidad(Request $request, $id)
    {
        $practica = Practica::findOrFail($id);
        if ($practica->user_id !== Auth::id()) abort(403);

        $request->validate([
            'archivo_pdf' => 'required|file|mimes:pdf|max:5120',
            'comentario' => 'nullable|string|max:500',
        ]);

        $tipoDocumento = TipoDocumento::where('nombre', 'Elección de Modalidad')
            ->where('tramite', 'PP')
            ->first();
        if (!$tipoDocumento) {
            return redirect()->route('practicas.index')->with('error', 'Tipo de documento no encontrado.');
        }

        $documento = Documento::where('user_id', Auth::id())
            ->where('tipo_documento_id', $tipoDocumento->id)
            ->first();

        $path = $request->file('archivo_pdf')->store('documentos/modalidad_pp', 'public');

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

        return redirect()->route('practicas.index')
            ->with('success', 'Elección de Modalidad subida correctamente.');
    }

    // Mostrar formulario para subir Carta de Presentación
    public function mostrarFormularioCartaPresentacion($id)
    {
        $practica = Practica::findOrFail($id);
        if ($practica->user_id !== Auth::id()) abort(403);
        return view('practicas.subir_carta_presentacion', compact('practica'));
    }

    public function subirCartaPresentacion(Request $request, $id)
    {
        $practica = Practica::findOrFail($id);
        if ($practica->user_id !== Auth::id()) abort(403);

        $request->validate([
            'archivo_pdf' => 'required|file|mimes:pdf|max:5120',
            'comentario' => 'nullable|string|max:500',
        ]);

        $tipoDocumento = TipoDocumento::where('nombre', 'Carta de Presentación de Prácticas Profesionales')
            ->where('tramite', 'PP')
            ->first();
        if (!$tipoDocumento) {
            return redirect()->route('practicas.index')->with('error', 'Tipo de documento no encontrado.');
        }

        $documento = Documento::where('user_id', Auth::id())
            ->where('tipo_documento_id', $tipoDocumento->id)
            ->first();

        $path = $request->file('archivo_pdf')->store('documentos/carta_presentacion_pp', 'public');

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

        return redirect()->route('practicas.index')
            ->with('success', 'Carta de Presentación subida correctamente.');
    }

    // Mostrar formulario para subir Carta de Aceptación
    public function mostrarFormularioCartaAceptacion($id)
    {
        $practica = Practica::findOrFail($id);
        if ($practica->user_id !== Auth::id()) abort(403);
        return view('practicas.subir_carta_aceptacion', compact('practica'));
    }

    public function subirCartaAceptacion(Request $request, $id)
    {
        $practica = Practica::findOrFail($id);
        if ($practica->user_id !== Auth::id()) abort(403);

        $request->validate([
            'archivo_pdf' => 'required|file|mimes:pdf|max:5120',
            'comentario' => 'nullable|string|max:500',
        ]);

        $tipoDocumento = TipoDocumento::where('nombre', 'Carta de Aceptación')
            ->where('tramite', 'PP')
            ->first();
        if (!$tipoDocumento) {
            return redirect()->route('practicas.index')->with('error', 'Tipo de documento no encontrado.');
        }

        $documento = Documento::where('user_id', Auth::id())
            ->where('tipo_documento_id', $tipoDocumento->id)
            ->first();

        $path = $request->file('archivo_pdf')->store('documentos/carta_aceptacion_pp', 'public');

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

        return redirect()->route('practicas.index')
            ->with('success', 'Carta de Aceptación subida correctamente.');
    }

    // Mostrar formulario para subir Evaluación
    public function mostrarFormularioEvaluacion($id)
    {
        $practica = Practica::findOrFail($id);
        if ($practica->user_id !== Auth::id()) abort(403);
        return view('practicas.subir_evaluacion', compact('practica'));
    }

    public function subirEvaluacion(Request $request, $id)
    {
        $practica = Practica::findOrFail($id);
        if ($practica->user_id !== Auth::id()) abort(403);

        $request->validate([
            'archivo_pdf' => 'required|file|mimes:pdf|max:5120',
            'comentario' => 'nullable|string|max:500',
        ]);

        $tipoDocumento = TipoDocumento::where('nombre', 'Evaluación de Competencias del Desempeño')
            ->where('tramite', 'PP')
            ->first();
        if (!$tipoDocumento) {
            return redirect()->route('practicas.index')->with('error', 'Tipo de documento no encontrado.');
        }

        $documento = Documento::where('user_id', Auth::id())
            ->where('tipo_documento_id', $tipoDocumento->id)
            ->first();

        $path = $request->file('archivo_pdf')->store('documentos/evaluacion_pp', 'public');

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

        return redirect()->route('practicas.index')
            ->with('success', 'Evaluación subida correctamente.');
    }

    // Mostrar formulario para subir Liberación
    public function mostrarFormularioLiberacion($id)
    {
        $practica = Practica::findOrFail($id);
        if ($practica->user_id !== Auth::id()) abort(403);
        return view('practicas.subir_liberacion', compact('practica'));
    }

    public function subirLiberacion(Request $request, $id)
    {
        $practica = Practica::findOrFail($id);
        if ($practica->user_id !== Auth::id()) abort(403);

        $request->validate([
            'archivo_pdf' => 'required|file|mimes:pdf|max:5120',
            'comentario' => 'nullable|string|max:500',
        ]);

        $tipoDocumento = TipoDocumento::where('nombre', 'Carta de Liberación de Prácticas Profesionales')
            ->where('tramite', 'PP')
            ->first();
        if (!$tipoDocumento) {
            return redirect()->route('practicas.index')->with('error', 'Tipo de documento no encontrado.');
        }

        $documento = Documento::where('user_id', Auth::id())
            ->where('tipo_documento_id', $tipoDocumento->id)
            ->first();

        $path = $request->file('archivo_pdf')->store('documentos/liberacion_pp', 'public');

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

        return redirect()->route('practicas.index')
            ->with('success', 'Carta de Liberación subida correctamente.');
    }

    // ============================================================
    // 📌 ELIMINAR DOCUMENTO (GENERAL - PARA TODOS LOS DOCUMENTOS)
    // ============================================================

    // Eliminar un documento específico
    public function eliminarDocumento($id, $tipoDocumentoNombre)
    {
        $practica = Practica::findOrFail($id);
        if ($practica->user_id !== Auth::id()) abort(403);

        $documento = Documento::where('user_id', Auth::id())
            ->whereHas('tipoDocumento', function($q) use ($tipoDocumentoNombre) {
                $q->where('nombre', $tipoDocumentoNombre);
            })->first();

        if (!$documento) {
            return redirect()->route('practicas.index')->with('error', 'Documento no encontrado.');
        }

        if ($documento->archivo_pdf && file_exists(storage_path('app/public/' . $documento->archivo_pdf))) {
            unlink(storage_path('app/public/' . $documento->archivo_pdf));
        }

        $documento->update(['archivo_pdf' => null, 'estatus' => 'pendiente']);

        return redirect()->route('practicas.index')
            ->with('success', 'Documento eliminado correctamente.');
    }

    // ============================================================
    // 📌 ELIMINAR INFORME (YA NO SE USA - SE USA eliminarDocumento)
    // ============================================================
    // ❌ ELIMINADO: eliminarInforme()

    // ============================================================
    // 📌 MÉTODOS DE VALIDACIÓN (YA NO SE USAN)
    // ============================================================
    // ❌ ELIMINADOS: validarReporteParcial, validarVentanillaReporteParcial, 
    //    rechazarReporteParcial, validarReporteFinal, 
    //    validarVentanillaReporteFinal, rechazarReporteFinal

    // ============================================================
    // 📌 DESCARGA DE WORD RELLENO
    // ============================================================
    public function descargarWordRelleno($id)
    {
        $practica = Practica::with('user', 'empresa', 'gradoAcademico', 'horario', 'gradoAcademicoJefe')->findOrFail($id);
        
        if ($practica->user_id !== Auth::id()) {
            abort(403);
        }

        Carbon::setLocale('es');
        $user = $practica->user;
        
        $variables = [
            'nombre_completo' => trim($user->name . '' . $user->apellidos),
            'nombre' => $user->name,
            'apellidos' => $user->apellidos,
            'matricula' => $user->matricula,
            'grupo' => $user->grupo ?? '',
            'carrera' => $user->carrera,
            'semestre' => $user->semestre,
            'turno' => $user->nombre_turno,
            'generacion' => $user->nombre_periodo_actual,
            'fecha_inicio' => $practica->fecha_inicio ? Carbon::parse($practica->fecha_inicio)->translatedFormat('d \d\e F \d\e Y') : '',
            'fecha_finalizacion' => $practica->fecha_limite_final ? Carbon::parse($practica->fecha_limite_final)->translatedFormat('d \d\e F \d\e Y') : '',
            'horario' => $practica->horario ? $practica->horario->hora_inicio . ' - ' . $practica->horario->hora_fin : '',
            'empresa' => $practica->empresa->nombre ?? '',
            'grado_academico' => $practica->gradoAcademico->abreviatura ?? '',
            'nombre_persona_carta' => $practica->nombre_persona_carta,
            'cargo_persona_carta' => $practica->cargo_persona_carta ?? '',
            'grado_academico_jefe' => $practica->gradoAcademicoJefe->abreviatura ?? '',
            'nombre_jefe_inmediato' => $practica->nombre_jefe_inmediato ?? '',
            'cargo_jefe_inmediato' => $practica->cargo_jefe_inmediato ?? '',
            'area_asignada' => $practica->area_asignada,
            'apoyo_estudiante' => $practica->apoyo_estudiante,
        ];

        $templatePath = storage_path('app/templates/solicitud_practicas_plantilla.docx');
        
        if (!file_exists($templatePath)) {
            return redirect()->route('practicas.index')
                ->with('error', 'No se encontró la plantilla de solicitud.');
        }
        
        $templateProcessor = new TemplateProcessor($templatePath);

        foreach ($variables as $key => $value) {
            $templateProcessor->setValue($key, $value ?? '');
        }

        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $tempPath = storage_path('app/temp/solicitud_practicas_' . $user->matricula . '.docx');
        $templateProcessor->saveAs($tempPath);

        return response()->download($tempPath, 'solicitud_practicas_' . $user->matricula . '.docx')->deleteFileAfterSend(true);
    }
}