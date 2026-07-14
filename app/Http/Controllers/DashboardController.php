<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Anuncio;
use App\Models\ServicioSocial;
use App\Models\Practica;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 🔹 Anuncios - marcar como vistos automáticamente
        $anuncios = Anuncio::with('admin')
            ->orderBy('created_at', 'desc')
            ->get();

        // Marcar todos los anuncios como vistos para este usuario
        foreach ($anuncios as $anuncio) {
            if (!$anuncio->vistoPor($user)) {
                $anuncio->marcarComoVisto($user);
            }
        }

        // 🔹 Servicio Social
        $servicioSocial = $user->servicioSocial;
        $estatusSS = $servicioSocial ? $this->getEstatusLabel($servicioSocial->estatus) : 'No solicitado';
        $progresoSS = $this->calcularProgreso($servicioSocial, 'servicio_social');

        // 🔹 Prácticas
        $practica = $user->practicas;
        $estatusPP = $practica ? $this->getEstatusLabel($practica->estatus) : 'No solicitado';
        $progresoPP = $this->calcularProgreso($practica, 'practicas');

        // 🔹 Estudiante activo (basado en periodos)
        $periodoActual = $user->periodoActual();
        $estudianteActivo = $periodoActual ? true : false;

        return view('dashboard', compact(
            'anuncios',
            'servicioSocial',
            'estatusSS',
            'progresoSS',
            'practica',
            'estatusPP',
            'progresoPP',
            'estudianteActivo'
        ));
    }

    private function getEstatusLabel($estatus)
    {
        return match ($estatus) {
            'liberado' => 'Liberado',
            'pendiente_revision' => 'Pendiente de revisión',
            'en_progreso' => 'En progreso',
            'pendiente' => 'Pendiente',
            default => 'No solicitado',
        };
    }

    private function calcularProgreso($tramite, $tipo)
    {
        if (!$tramite || !$tramite->fecha_inicio) {
            return 0;
        }

        $fechaInicio = \Carbon\Carbon::parse($tramite->fecha_inicio);

        // Determinar fecha límite según el tipo
        if ($tipo === 'servicio_social') {
            $fechaLimite = $tramite->fecha_limite_segundo_informe 
                ? \Carbon\Carbon::parse($tramite->fecha_limite_segundo_informe) 
                : null;
        } else { // practicas
            $fechaLimite = $tramite->fecha_limite_final 
                ? \Carbon\Carbon::parse($tramite->fecha_limite_final) 
                : null;
        }

        if (!$fechaLimite) {
            return 0;
        }

        $diasTotales = $fechaInicio->diffInDays($fechaLimite);
        if ($diasTotales <= 0) {
            return 0;
        }

        $diasTranscurridos = $fechaInicio->diffInDays(now());

        // Si ya pasó la fecha límite, el progreso es 100%
        if ($diasTranscurridos >= $diasTotales) {
            return 100;
        }

        return round(($diasTranscurridos / $diasTotales) * 100);
    }
}