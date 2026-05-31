<?php

namespace App\Http\Controllers;

use App\Models\ServicioSocial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'estatus' => 'liberado'
        ]);

        return redirect()->route('servicio-social.index')
                        ->with('success', 'Reporte final subido correctamente. ¡Felicidades! Has completado tu Servicio Social.');
    }

}