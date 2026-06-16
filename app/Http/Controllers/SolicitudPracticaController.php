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

        $horarios = collect();
        if ($user->turno_id) {
            $horarios = \App\Models\Horario::where('turno_id', $user->turno_id)->get();
        }

        $empresas = Empresa::where('activo', true)->get();
        $grados = GradoAcademico::where('activo', true)->get();

        return view('solicitud_practicas.form', compact('empresas', 'grados', 'horarios'));
    }

    public function store(Request $request)
    {
        // dd('Llegó al store', $request->all());

        $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'grado_academico_id' => 'required|exists:grados_academicos,id',
            'nombre_persona_carta' => 'required|string|max:255',
            'cargo_persona_carta' => 'required|string|max:255',
            'grado_academico_jefe_id' => 'required|exists:grados_academicos,id',
            'nombre_jefe_inmediato' => 'required|string|max:255',
            'cargo_jefe_inmediato' => 'required|string|max:255',
            'area_asignada' => 'required|string|max:255',
            'apoyo_estudiante' => 'nullable|string|max:255',
            'fecha_inicio' => 'required|date',
            'horario_id' => 'required|exists:horarios,id',
        ]);

        $fechaInicio = Carbon::parse($request->fecha_inicio);

        if ($fechaInicio->isWeekend()) {
            return back()->withErrors(['fecha_inicio' => 'La fecha de inicio no puede ser sábado o domingo.'])->withInput();
        }

        $fechaLimiteParcial = $this->ajustarFechaSiFinDeSemana($fechaInicio->copy()->addMonths(2));
        $fechaLimiteFinal = $this->ajustarFechaSiFinDeSemana($fechaInicio->copy()->addMonths(4));

        // Guardar o actualizar el registro
        $registro = Practica::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'empresa_id' => $request->empresa_id,
                'grado_academico_id' => $request->grado_academico_id,
                'nombre_persona_carta' => $request->nombre_persona_carta,
                'cargo_persona_carta' => $request->cargo_persona_carta,
                'grado_academico_jefe_id' => $request->grado_academico_jefe_id,
                'nombre_jefe_inmediato' => $request->nombre_jefe_inmediato,
                'cargo_jefe_inmediato' => $request->cargo_jefe_inmediato,
                'area_asignada' => $request->area_asignada,
                'apoyo_estudiante' => $request->apoyo_estudiante,
                'fecha_inicio' => $fechaInicio,
                'fecha_limite_parcial' => $fechaLimiteParcial,
                'fecha_limite_final' => $fechaLimiteFinal,
                'horario_id' => $request->horario_id,
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