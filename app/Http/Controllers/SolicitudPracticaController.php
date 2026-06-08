<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\GradoAcademico;
use App\Models\Practica;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SolicitudPracticaController extends Controller
{
    private function ajustarFechaSiFinDeSemana(Carbon $fecha)
    {
        if ($fecha->isSaturday()) {
            return $fecha->addDays(2);
        } elseif ($fecha->isSunday()) {
            return $fecha->addDays(1);
        }
        return $fecha;
    }

    public function create()
    {
        $user = Auth::user();
        $servicioSocial = $user->servicioSocial;

        // Validar que el Servicio Social esté liberado
        if (!$servicioSocial || $servicioSocial->estatus !== 'liberado') {
            return view('practicas.requisito');
        }

        $solicitud = Practica::where('user_id', Auth::id())->first();

        if ($solicitud && $solicitud->fecha_inicio) {
            return redirect()->route('practicas.index')
                ->with('error', 'Ya has completado tu solicitud de Prácticas Profesionales.');
        }

        $empresas = Empresa::where('activo', true)->get();
        $grados = GradoAcademico::where('activo', true)->get();

        return view('solicitud_practicas.form', compact('empresas', 'grados'));
    }

    public function store(Request $request)
    {
        // dd('Llegó al store', $request->all());

        $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'grado_academico_id' => 'required|exists:grados_academicos,id',
            'nombre_persona_carta' => 'required|string|max:255',
            'area_asignada' => 'required|string|max:255',
            'apoyo_estudiante' => 'nullable|string|max:255',
            'fecha_inicio' => 'required|date',
        ]);

        $fechaInicio = Carbon::parse($request->fecha_inicio);

        if ($fechaInicio->isWeekend()) {
            return back()->withErrors(['fecha_inicio' => 'La fecha de inicio no puede ser sábado o domingo.'])->withInput();
        }

        $fechaLimiteParcial = $this->ajustarFechaSiFinDeSemana($fechaInicio->copy()->addMonths(3));
        $fechaLimiteFinal = $this->ajustarFechaSiFinDeSemana($fechaInicio->copy()->addMonths(6));

        // Guardar o actualizar el registro
        $registro = Practica::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'empresa_id' => $request->empresa_id,
                'grado_academico_id' => $request->grado_academico_id,
                'nombre_persona_carta' => $request->nombre_persona_carta,
                'area_asignada' => $request->area_asignada,
                'apoyo_estudiante' => $request->apoyo_estudiante,
                'fecha_inicio' => $fechaInicio,
                'fecha_limite_parcial' => $fechaLimiteParcial,
                'fecha_limite_final' => $fechaLimiteFinal,
                'estatus' => 'pendiente',
            ]
        );

        // dd('Registro guardado', $registro->id, $registro->fecha_inicio);

        // La sincronización del estatus con el usuario se hace automáticamente
        // mediante el evento updating en el modelo Practica

        return redirect()->route('practicas.index')
            ->with('success', 'Solicitud completada. Puedes comenzar a subir tus documentos.');
    }
}