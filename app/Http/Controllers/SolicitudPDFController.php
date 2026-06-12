<?php

namespace App\Http\Controllers;

use App\Models\ServicioSocial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use Dompdf\Dompdf;

class SolicitudPDFController extends Controller
{
    public function download($id)
    {
        $servicioSocial = ServicioSocial::findOrFail($id);
        $user = $servicioSocial->user;

        // Cargar la plantilla de Word
        $templatePath = storage_path('app/templates/solicitud_plantilla.docx');
        $phpWord = IOFactory::load($templatePath);

        // Reemplazar marcadores
        $variables = [
            'nombre' => $user->name,
            'apellidos' => $user->apellidos,
            'matricula' => $user->matricula,
            'carrera' => $user->carrera,
            'semestre' => $user->semestre,
            'turno' => $user->nombre_turno,
            'generacion' => $user->nombre_periodo_actual,
            'fecha_inicio' => $servicioSocial->fecha_inicio,
            'fecha_finalizacion' => $servicioSocial->fecha_limite_segundo_informe,
            'horario' => $servicioSocial->horario ? $servicioSocial->horario->hora_inicio . ' - ' . $servicioSocial->horario->hora_fin : 'No definido',
            'empresa' => $servicioSocial->empresa->nombre ?? '',
            'grado_academico' => $servicioSocial->gradoAcademico->nombre ?? '',
            'nombre_persona_carta' => $servicioSocial->nombre_persona_carta,
            'area_asignada' => $servicioSocial->area_asignada,
            'apoyo_estudiante' => $servicioSocial->apoyo_estudiante,
        ];

        foreach ($variables as $key => $value) {
            $phpWord->setValue($key, $value);
        }

        // Guardar Word temporal
        $tempWordPath = storage_path('app/temp/solicitud_temp.docx');
        $phpWord->save($tempWordPath);

        // Convertir a PDF usando DomPDF
        $dompdf = new Dompdf();
        $dompdf->loadHtml(file_get_contents($tempWordPath));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Limpiar archivo temporal
        unlink($tempWordPath);

        // Descargar PDF
        return $dompdf->stream("solicitud_{$user->matricula}.pdf");
    }
}