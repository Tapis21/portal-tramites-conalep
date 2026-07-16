<?php

namespace App\Http\Controllers;

use App\Models\ServicioSocial;
use App\Models\Practica;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PDFController extends Controller
{
    /**
     * 📌 VALIDAR PERMISOS
     */
    private function validarPermiso($tramite, $id)
    {
        if ($tramite === 'ss') {
            $servicioSocial = ServicioSocial::findOrFail($id);
            if ($servicioSocial->user_id !== Auth::id()) {
                abort(403, 'No tienes permiso para descargar este documento.');
            }
            return $servicioSocial;
        } else {
            $practica = Practica::findOrFail($id);
            if ($practica->user_id !== Auth::id()) {
                abort(403, 'No tienes permiso para descargar este documento.');
            }
            return $practica;
        }
    }

    /**
     * 📌 OBTENER VARIABLES COMUNES PARA SS Y PP
     */
    private function getVariables($tramite, $data)
    {
        $user = $data->user;
        Carbon::setLocale('es');

        // ========================================== //
        // 🔥 LOGO DEL PIE - PARA TODOS LOS PDFs
        // ========================================== //
        $logoPiePath = public_path('images/logotipadopie.png');
        if (file_exists($logoPiePath)) {
            $logoPieBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPiePath));
        } else {
            $logoPieBase64 = '';
        }

        // ========================================== //
        // 🔥 LOGO DEL ENCABEZADO - PARA TODOS LOS PDFs
        // ========================================== //
        $logoPath = public_path('images/Quintana-Roo.png');
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        } else {
            $logoBase64 = '';
        }

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
            'empresa' => $data->empresa->nombre ?? '',
            'grado_academico' => $data->gradoAcademico->abreviatura ?? '',
            'nombre_persona_carta' => $data->nombre_persona_carta ?? '',
            'cargo_persona_carta' => $data->cargo_persona_carta ?? '',
            'grado_academico_jefe' => $data->gradoAcademicoJefe->abreviatura ?? '',
            'nombre_jefe_inmediato' => $data->nombre_jefe_inmediato ?? '',
            'cargo_jefe_inmediato' => $data->cargo_jefe_inmediato ?? '',
            'area_asignada' => $data->area_asignada ?? '',
            'apoyo_estudiante' => $data->apoyo_estudiante ?? '',
            'tipo_tramite' => $tramite === 'ss' ? 'Servicio Social' : 'Prácticas Profesionales',
            'checkbox_ss' => $tramite === 'ss' ? 'X' : '',
            'checkbox_pp' => $tramite === 'pp' ? 'X' : '',
            // 🔥 LOGOS - DISPONIBLES PARA TODOS LOS PDFs
            'logo_base64' => $logoBase64,
            'logo_pie_base64' => $logoPieBase64,
        ];

        // Fechas según el tipo de trámite
        if ($tramite === 'ss') {
            $variables['fecha_inicio'] = $data->fecha_inicio ? Carbon::parse($data->fecha_inicio)->translatedFormat('d \d\e F \d\e Y') : '';
            $variables['fecha_finalizacion'] = $data->fecha_limite_segundo_informe ? Carbon::parse($data->fecha_limite_segundo_informe)->translatedFormat('d \d\e F \d\e Y') : '';
            $variables['horario'] = $data->horario ? $data->horario->hora_inicio . ' - ' . $data->horario->hora_fin : '';
            $variables['horas_totales'] = '480';
        } else {
            $variables['fecha_inicio'] = $data->fecha_inicio ? Carbon::parse($data->fecha_inicio)->translatedFormat('d \d\e F \d\e Y') : '';
            $variables['fecha_finalizacion'] = $data->fecha_limite_final ? Carbon::parse($data->fecha_limite_final)->translatedFormat('d \d\e F \d\e Y') : '';
            $variables['horario'] = $data->horario ? $data->horario->hora_inicio . ' - ' . $data->horario->hora_fin : '';
            $variables['horas_totales'] = '360';
        }

        return $variables;
    }

    // ============================================================
    // 📌 SERVICIO SOCIAL - SOLICITUD
    // ============================================================
    public function descargarSolicitudSS($id)
    {
        try {
            $servicioSocial = $this->validarPermiso('ss', $id);
            $variables = $this->getVariables('ss', $servicioSocial);

            $pdf = Pdf::loadView('servicio_social.pdfs.solicitud', $variables);
            $pdf->setPaper('letter', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => false,
                'dpi' => 96,
                'enable_css_float' => true,
                'enable_remote' => true,
            ]);

            return $pdf->download('solicitud_ss_' . $servicioSocial->user->matricula . '.pdf');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el PDF: ' . $e->getMessage());
        }
    }

    // ============================================================
    // 📌 PRÁCTICAS PROFESIONALES - SOLICITUD
    // ============================================================
    public function descargarSolicitudPP($id)
    {
        try {
            $practica = $this->validarPermiso('pp', $id);
            $variables = $this->getVariables('pp', $practica);

            $pdf = Pdf::loadView('practicas.pdfs.solicitud', $variables);
            $pdf->setPaper('letter', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => false,
                'dpi' => 96,
                'enable_css_float' => true,
                'enable_remote' => true,
            ]);

            return $pdf->download('solicitud_pp_' . $practica->user->matricula . '.pdf');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el PDF: ' . $e->getMessage());
        }
    }

    // ============================================================
    // 📌 SERVICIO SOCIAL - MODALIDAD
    // ============================================================
    public function descargarModalidadSS($id)
    {
        try {
            $servicioSocial = $this->validarPermiso('ss', $id);
            $variables = $this->getVariables('ss', $servicioSocial);

            $pdf = Pdf::loadView('servicio_social.pdfs.modalidad', $variables);
            $pdf->setPaper('letter', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => false,
                'dpi' => 96,
                'enable_css_float' => true,
                'enable_remote' => true,
            ]);

            return $pdf->download('modalidad_ss_' . $servicioSocial->user->matricula . '.pdf');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el PDF: ' . $e->getMessage());
        }
    }

    // ============================================================
    // 📌 SERVICIO SOCIAL - CARTA DE PRESENTACIÓN
    // ============================================================
    public function descargarCartaPresentacionSS($id)
    {
        try {
            $servicioSocial = $this->validarPermiso('ss', $id);
            $variables = $this->getVariables('ss', $servicioSocial);

            // 🔥 IMÁGENES PARA CARTA PRESENTACIÓN
            $logoEducacionPath = public_path('images/Logo-Eduación.png');
            if (file_exists($logoEducacionPath)) {
                $variables['logo_educacion_base64'] = 'data:image/png;base64,' . base64_encode(file_get_contents($logoEducacionPath));
            } else {
                $variables['logo_educacion_base64'] = '';
            }

            $logoConalepPath = public_path('images/logo-conalep.png');
            if (file_exists($logoConalepPath)) {
                $variables['logo_conalep_base64'] = 'data:image/png;base64,' . base64_encode(file_get_contents($logoConalepPath));
            } else {
                $variables['logo_conalep_base64'] = '';
            }

            $firmaDrPath = public_path('images/firmaDrCano.png');
            if (file_exists($firmaDrPath)) {
                $variables['firma_dr_base64'] = 'data:image/png;base64,' . base64_encode(file_get_contents($firmaDrPath));
            } else {
                $variables['firma_dr_base64'] = '';
            }

            $selloConalepPath = public_path('images/SelloConalep.png');
            if (file_exists($selloConalepPath)) {
                $variables['sello_conalep_base64'] = 'data:image/png;base64,' . base64_encode(file_get_contents($selloConalepPath));
            } else {
                $variables['sello_conalep_base64'] = '';
            }

            $pdf = Pdf::loadView('servicio_social.pdfs.carta-presentacion', $variables);
            $pdf->setPaper('letter', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => false,
                'dpi' => 96,
                'enable_css_float' => true,
                'enable_remote' => true,
            ]);

            return $pdf->download('carta_presentacion_ss_' . $servicioSocial->user->matricula . '.pdf');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el PDF: ' . $e->getMessage());
        }
    }

    // ============================================================
    // 📌 PRÁCTICAS - CARTA DE PRESENTACIÓN
    // ============================================================
    public function descargarCartaPresentacionPP($id)
    {
        try {
            $practica = $this->validarPermiso('pp', $id);
            $variables = $this->getVariables('pp', $practica);

            // 🔥 IMÁGENES PARA CARTA PRESENTACIÓN
            $logoEducacionPath = public_path('images/Logo-Eduación.png');
            if (file_exists($logoEducacionPath)) {
                $variables['logo_educacion_base64'] = 'data:image/png;base64,' . base64_encode(file_get_contents($logoEducacionPath));
            } else {
                $variables['logo_educacion_base64'] = '';
            }

            $logoConalepPath = public_path('images/logo-conalep.png');
            if (file_exists($logoConalepPath)) {
                $variables['logo_conalep_base64'] = 'data:image/png;base64,' . base64_encode(file_get_contents($logoConalepPath));
            } else {
                $variables['logo_conalep_base64'] = '';
            }

            $firmaDrPath = public_path('images/firmaDrCano.png');
            if (file_exists($firmaDrPath)) {
                $variables['firma_dr_base64'] = 'data:image/png;base64,' . base64_encode(file_get_contents($firmaDrPath));
            } else {
                $variables['firma_dr_base64'] = '';
            }

            $selloConalepPath = public_path('images/SelloConalep.png');
            if (file_exists($selloConalepPath)) {
                $variables['sello_conalep_base64'] = 'data:image/png;base64,' . base64_encode(file_get_contents($selloConalepPath));
            } else {
                $variables['sello_conalep_base64'] = '';
            }

            $pdf = Pdf::loadView('practicas.pdfs.carta-presentacion', $variables);
            $pdf->setPaper('letter', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => false,
                'dpi' => 96,
                'enable_css_float' => true,
                'enable_remote' => true,
            ]);

            return $pdf->download('carta_presentacion_pp_' . $practica->user->matricula . '.pdf');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el PDF: ' . $e->getMessage());
        }
    }

    // ============================================================
    // 📌 SERVICIO SOCIAL - PRIMER INFORME
    // ============================================================
    public function descargarPrimerInformeSS($id)
    {
        try {
            $servicioSocial = $this->validarPermiso('ss', $id);
            $variables = $this->getVariables('ss', $servicioSocial);

            $pdf = Pdf::loadView('servicio_social.pdfs.primer-informe', $variables);
            $pdf->setPaper('letter', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => false,
                'dpi' => 96,
                'enable_css_float' => true,
                'enable_remote' => true,
            ]);

            return $pdf->download('primer_informe_ss_' . $servicioSocial->user->matricula . '.pdf');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el PDF: ' . $e->getMessage());
        }
    }

    // ============================================================
    // 📌 SERVICIO SOCIAL - SEGUNDO INFORME
    // ============================================================
    public function descargarSegundoInformeSS($id)
    {
        try {
            $servicioSocial = $this->validarPermiso('ss', $id);
            $variables = $this->getVariables('ss', $servicioSocial);

            $pdf = Pdf::loadView('servicio_social.pdfs.segundo-informe', $variables);
            $pdf->setPaper('letter', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => false,
                'dpi' => 96,
                'enable_css_float' => true,
                'enable_remote' => true,
            ]);

            return $pdf->download('segundo_informe_ss_' . $servicioSocial->user->matricula . '.pdf');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el PDF: ' . $e->getMessage());
        }
    }

    // ============================================================
    // 📌 SERVICIO SOCIAL - EVALUACIÓN
    // ============================================================
    public function descargarEvaluacionSS($id)
    {
        try {
            $servicioSocial = $this->validarPermiso('ss', $id);
            $variables = $this->getVariables('ss', $servicioSocial);

            $pdf = Pdf::loadView('servicio_social.pdfs.evaluacion', $variables);
            $pdf->setPaper('letter', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => false,
                'dpi' => 96,
                'enable_css_float' => true,
                'enable_remote' => true,
            ]);

            return $pdf->download('evaluacion_ss_' . $servicioSocial->user->matricula . '.pdf');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el PDF: ' . $e->getMessage());
        }
    }

    // ============================================================
    // 📌 PRÁCTICAS - MODALIDAD
    // ============================================================
    public function descargarModalidadPP($id)
    {
        try {
            $practica = $this->validarPermiso('pp', $id);
            $variables = $this->getVariables('pp', $practica);

            $pdf = Pdf::loadView('practicas.pdfs.modalidad', $variables);
            $pdf->setPaper('letter', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => false,
                'dpi' => 96,
                'enable_css_float' => true,
                'enable_remote' => true,
            ]);

            return $pdf->download('modalidad_pp_' . $practica->user->matricula . '.pdf');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el PDF: ' . $e->getMessage());
        }
    }

    // ============================================================
    // 📌 PRÁCTICAS - PRIMER INFORME
    // ============================================================
    public function descargarPrimerInformePP($id)
    {
        try {
            $practica = $this->validarPermiso('pp', $id);
            $variables = $this->getVariables('pp', $practica);

            $pdf = Pdf::loadView('practicas.pdfs.primer-informe', $variables);
            $pdf->setPaper('letter', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => false,
                'dpi' => 96,
                'enable_css_float' => true,
                'enable_remote' => true,
            ]);

            return $pdf->download('primer_informe_pp_' . $practica->user->matricula . '.pdf');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el PDF: ' . $e->getMessage());
        }
    }

    // ============================================================
    // 📌 PRÁCTICAS - SEGUNDO INFORME
    // ============================================================
    public function descargarSegundoInformePP($id)
    {
        try {
            $practica = $this->validarPermiso('pp', $id);
            $variables = $this->getVariables('pp', $practica);

            $pdf = Pdf::loadView('practicas.pdfs.segundo-informe', $variables);
            $pdf->setPaper('letter', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => false,
                'dpi' => 96,
                'enable_css_float' => true,
                'enable_remote' => true,
            ]);

            return $pdf->download('segundo_informe_pp_' . $practica->user->matricula . '.pdf');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el PDF: ' . $e->getMessage());
        }
    }

    // ============================================================
    // 📌 PRÁCTICAS - EVALUACIÓN
    // ============================================================
    public function descargarEvaluacionPP($id)
    {
        try {
            $practica = $this->validarPermiso('pp', $id);
            $variables = $this->getVariables('pp', $practica);

            $pdf = Pdf::loadView('practicas.pdfs.evaluacion', $variables);
            $pdf->setPaper('letter', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => false,
                'dpi' => 96,
                'enable_css_float' => true,
                'enable_remote' => true,
            ]);

            return $pdf->download('evaluacion_pp_' . $practica->user->matricula . '.pdf');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el PDF: ' . $e->getMessage());
        }
    }
}