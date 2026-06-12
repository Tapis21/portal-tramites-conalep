<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\GradoAcademico;
use App\Models\ServicioSocial;
use App\Models\Horario; // ESTO FALTA
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SolicitudServicioSocialController extends Controller
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
        $user = Auth::user(); // ESTO TAMBIÉN FALTA
        $solicitud = ServicioSocial::where('user_id', Auth::id())->first();

        if ($solicitud && $solicitud->fecha_inicio) {
            return redirect()->route('servicio-social.index')
                ->with('error', 'Ya has completado tu solicitud de Servicio Social.');
        }

        // CARGAR HORARIOS SEGÚN EL TURNO DEL ESTUDIANTE
        $horarios = collect();
        if ($user->turno_id) {
            $horarios = Horario::where('turno_id', $user->turno_id)->get();
        }

        // CARGAR GENERACIÓN (PERIODO ACTIVO)
        $periodoActivo = $user->periodoActual();
        $generacion = $periodoActivo ? $periodoActivo->nombre : 'No definida';

        $empresas = Empresa::where('activo', true)->get();
        $grados = GradoAcademico::where('activo', true)->get();

        return view('solicitud_servicio_social.form', compact('empresas', 'grados', 'horarios', 'generacion'));
    }

    public function store(Request $request)
    {
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
            'horario_id' => 'required|exists:horarios,id', // VALIDAR HORARIO
        ]);

        $fechaInicio = Carbon::parse($request->fecha_inicio);

        if ($fechaInicio->isWeekend()) {
            return back()->withErrors(['fecha_inicio' => 'La fecha de inicio no puede ser sabado o domingo.'])->withInput();
        }

        $fechaLimitePrimerInforme = $this->ajustarFechaSiFinDeSemana($fechaInicio->copy()->addMonths(3));
        $fechaLimiteSegundoInforme = $this->ajustarFechaSiFinDeSemana($fechaInicio->copy()->addMonths(6));

        // Guardar o actualizar el registro
        $registro = ServicioSocial::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'empresa_id' => $request->empresa_id,
                'grado_academico_id' => $request->grado_academico_id,
                'nombre_persona_carta' => $request->nombre_persona_carta,
                'cargo_persona_carta' => $request->cargo_persona_carta,
                'nombre_jefe_inmediato' => $request->nombre_jefe_inmediato,
                'cargo_jefe_inmediato' => $request->cargo_jefe_inmediato,
                'grado_academico_jefe_id' => $request->grado_academico_jefe_id,
                'area_asignada' => $request->area_asignada,
                'apoyo_estudiante' => $request->apoyo_estudiante,
                'fecha_inicio' => $fechaInicio,
                'fecha_limite_primer_informe' => $fechaLimitePrimerInforme,
                'fecha_limite_segundo_informe' => $fechaLimiteSegundoInforme,
                'horario_id' => $request->horario_id, // GUARDAR HORARIO
                'estatus' => 'pendiente',
            ]
        );

        // Sincronizar manualmente el estatus con el usuario
        $registro->user->update(['estatus_servicio_social' => 'pendiente']);

        return redirect()->route('servicio-social.index')
            ->with('success', 'Solicitud completada. Puedes comenzar a subir tus documentos.');
    }
}