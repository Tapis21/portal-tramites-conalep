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

    // Mostrar formulario para subir reporte parcial (Primer Informe)
    public function mostrarFormularioReporteParcial($id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        $fechaLimite = $servicioSocial->fecha_limite_primer_informe;
        $fechaHoy = now()->startOfDay();
        $fechaFormateada = $fechaLimite ? \Carbon\Carbon::parse($fechaLimite)->format('d/m/Y') : 'No definida';
        
        // Si no hay fecha límite definida
        if (!$fechaLimite) {
            return redirect()->route('servicio-social.index')
                ->with('error', 'No hay fecha límite definida para el Primer Informe. Contacta al administrador.');
        }

        // Calcular días restantes (positivo = días faltantes, negativo = días después)
        $diasRestantes = $fechaHoy->diffInDays($fechaLimite, false);

        // Verificar si está dentro del período permitido (-5 a +5 días)
        if ($diasRestantes > 5) {
            // Falta más de 5 días
            $fechaInicioSubida = \Carbon\Carbon::parse($fechaLimite)->subDays(5)->format('d/m/Y');
            return redirect()->route('servicio-social.index')
                ->with('error', 'Aún no puedes subir el Primer Informe. La fecha límite es el ' . $fechaFormateada . '. Podrás subirlo a partir del ' . $fechaInicioSubida . '.');
        }

        if ($diasRestantes < -5) {
            // Ya pasó más de 5 días después de la fecha límite
            return redirect()->route('servicio-social.index')
                ->with('error', 'El plazo para subir el Primer Informe venció el ' . $fechaFormateada . '. Ya no es posible subirlo.');
        }

        // Si está en prórroga (1-5 días después), mostrar advertencia
        if ($diasRestantes < 0 && $diasRestantes >= -5) {
            $fechaFinPrórroga = \Carbon\Carbon::parse($fechaLimite)->addDays(5)->format('d/m/Y');
            session()->flash('warning', '⚠️ El plazo oficial venció el ' . $fechaFormateada . '. Tienes 5 días adicionales (hasta el ' . $fechaFinPrórroga . ') para subir el informe.');
        }

        // Si está dentro del período permitido
        return view('servicio_social.subir_reporte_parcial', compact('servicioSocial', 'fechaLimite', 'fechaFormateada'));
    }

    // Procesar la subida del reporte parcial (Primer Informe)
    public function subirReporteParcial(Request $request, $id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        $fechaLimite = $servicioSocial->fecha_limite_primer_informe;
        $fechaHoy = now()->startOfDay();
        $fechaFormateada = $fechaLimite ? \Carbon\Carbon::parse($fechaLimite)->format('d/m/Y') : 'No definida';

        if (!$fechaLimite) {
            return redirect()->route('servicio-social.index')
                ->with('error', 'No hay fecha límite definida para el Primer Informe.');
        }

        $diasRestantes = $fechaHoy->diffInDays($fechaLimite, false);

        // Verificar si está dentro del período permitido (-5 a +5 días)
        if ($diasRestantes > 5) {
            $fechaInicioSubida = \Carbon\Carbon::parse($fechaLimite)->subDays(5)->format('d/m/Y');
            return redirect()->route('servicio-social.index')
                ->with('error', 'Aún no puedes subir el Primer Informe. La fecha límite es el ' . $fechaFormateada . '. Podrás subirlo a partir del ' . $fechaInicioSubida . '.');
        }

        if ($diasRestantes < -5) {
            return redirect()->route('servicio-social.index')
                ->with('error', 'El plazo para subir el Primer Informe venció el ' . $fechaFormateada . '. Ya no es posible subirlo.');
        }

        // Si está en prórroga, mostrar advertencia pero permitir
        if ($diasRestantes < 0 && $diasRestantes >= -5) {
            $fechaFinPrórroga = \Carbon\Carbon::parse($fechaLimite)->addDays(5)->format('d/m/Y');
            session()->flash('warning', '⚠️ El plazo oficial venció el ' . $fechaFormateada . '. Tienes 5 días adicionales (hasta el ' . $fechaFinPrórroga . ') para subir el informe.');
        }

        // Validar archivo
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

    // Mostrar formulario para subir reporte final (Segundo Informe)
    public function mostrarFormularioReporteFinal($id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        $fechaLimite = $servicioSocial->fecha_limite_segundo_informe;
        $fechaHoy = now()->startOfDay();
        $fechaFormateada = $fechaLimite ? \Carbon\Carbon::parse($fechaLimite)->format('d/m/Y') : 'No definida';
        
        if (!$fechaLimite) {
            return redirect()->route('servicio-social.index')
                ->with('error', 'No hay fecha límite definida para el Segundo Informe. Contacta al administrador.');
        }

        $diasRestantes = $fechaHoy->diffInDays($fechaLimite, false);

        if ($diasRestantes > 5) {
            $fechaInicioSubida = \Carbon\Carbon::parse($fechaLimite)->subDays(5)->format('d/m/Y');
            return redirect()->route('servicio-social.index')
                ->with('error', 'Aún no puedes subir el Segundo Informe. La fecha límite es el ' . $fechaFormateada . '. Podrás subirlo a partir del ' . $fechaInicioSubida . '.');
        }

        if ($diasRestantes < -5) {
            return redirect()->route('servicio-social.index')
                ->with('error', 'El plazo para subir el Segundo Informe venció el ' . $fechaFormateada . '. Ya no es posible subirlo.');
        }

        if ($diasRestantes < 0 && $diasRestantes >= -5) {
            $fechaFinPrórroga = \Carbon\Carbon::parse($fechaLimite)->addDays(5)->format('d/m/Y');
            session()->flash('warning', '⚠️ El plazo oficial venció el ' . $fechaFormateada . '. Tienes 5 días adicionales (hasta el ' . $fechaFinPrórroga . ') para subir el informe.');
        }

        return view('servicio_social.subir_reporte_final', compact('servicioSocial', 'fechaLimite', 'fechaFormateada'));
    }

    // Procesar la subida del reporte final (Segundo Informe)
    public function subirReporteFinal(Request $request, $id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);

        if ($servicioSocial->user_id !== Auth::id()) {
            abort(403);
        }

        $fechaLimite = $servicioSocial->fecha_limite_segundo_informe;
        $fechaHoy = now()->startOfDay();
        $fechaFormateada = $fechaLimite ? \Carbon\Carbon::parse($fechaLimite)->format('d/m/Y') : 'No definida';

        if (!$fechaLimite) {
            return redirect()->route('servicio-social.index')
                ->with('error', 'No hay fecha límite definida para el Segundo Informe.');
        }

        $diasRestantes = $fechaHoy->diffInDays($fechaLimite, false);

        if ($diasRestantes > 5) {
            $fechaInicioSubida = \Carbon\Carbon::parse($fechaLimite)->subDays(5)->format('d/m/Y');
            return redirect()->route('servicio-social.index')
                ->with('error', 'Aún no puedes subir el Segundo Informe. La fecha límite es el ' . $fechaFormateada . '. Podrás subirlo a partir del ' . $fechaInicioSubida . '.');
        }

        if ($diasRestantes < -5) {
            return redirect()->route('servicio-social.index')
                ->with('error', 'El plazo para subir el Segundo Informe venció el ' . $fechaFormateada . '. Ya no es posible subirlo.');
        }

        if ($diasRestantes < 0 && $diasRestantes >= -5) {
            $fechaFinPrórroga = \Carbon\Carbon::parse($fechaLimite)->addDays(5)->format('d/m/Y');
            session()->flash('warning', '⚠️ El plazo oficial venció el ' . $fechaFormateada . '. Tienes 5 días adicionales (hasta el ' . $fechaFinPrórroga . ') para subir el informe.');
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
}