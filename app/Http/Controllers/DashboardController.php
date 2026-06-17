<?php

namespace App\Http\Controllers;

use App\Models\Anuncio;
use App\Models\ServicioSocial;
use App\Models\Practica;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // ========================================== //
        // SERVICIO SOCIAL
        // ========================================== //
        $servicioSocial = $user->servicioSocial;
        $progresoSS = 0;
        $estatusSS = 'No solicitado';
        
        if ($servicioSocial && $servicioSocial->fecha_inicio) {
            $estatusSS = $this->traducirEstatus($servicioSocial->estatus);
            
            if ($servicioSocial->fecha_limite_segundo_informe) {
                $hoy = now();
                $inicio = \Carbon\Carbon::parse($servicioSocial->fecha_inicio);
                $fin = \Carbon\Carbon::parse($servicioSocial->fecha_limite_segundo_informe);
                if ($hoy->gte($fin)) {
                    $progresoSS = 100;
                } elseif ($hoy->lte($inicio)) {
                    $progresoSS = 0;
                } else {
                    $totalDias = $inicio->diffInDays($fin);
                    $diasTranscurridos = $inicio->diffInDays($hoy);
                    $progresoSS = round(($diasTranscurridos / $totalDias) * 100);
                }
            }
        }
        
        // ========================================== //
        // PRÁCTICAS PROFESIONALES (CORREGIDO)
        // ========================================== //
        // . USAR $user->practicas (COINCIDENTE CON EL MODELO)
        $practica = $user->practicas; // Esto es hasOne, devuelve un solo objeto
        $progresoPP = 0;
        $estatusPP = 'No solicitado';
        
        if ($practica && $practica->fecha_inicio) {
            $estatusPP = $this->traducirEstatus($practica->estatus);
            
            if ($practica->fecha_limite_final) {
                $hoy = now();
                $inicio = \Carbon\Carbon::parse($practica->fecha_inicio);
                $fin = \Carbon\Carbon::parse($practica->fecha_limite_final);
                if ($hoy->gte($fin)) {
                    $progresoPP = 100;
                } elseif ($hoy->lte($inicio)) {
                    $progresoPP = 0;
                } else {
                    $totalDias = $inicio->diffInDays($fin);
                    $diasTranscurridos = $inicio->diffInDays($hoy);
                    $progresoPP = round(($diasTranscurridos / $totalDias) * 100);
                }
            }
        }
        
        // ========================================== //
        // ANUNCIOS
        // ========================================== //
        $anuncios = Anuncio::orderBy('created_at', 'desc')->get();
        
        return view('dashboard', compact(
            'progresoSS', 
            'estatusSS', 
            'progresoPP', 
            'estatusPP', 
            'anuncios',
            'servicioSocial',
            'practica'
        ));
    }
    
    /**
     * Traducir el estatus de la base de datos a formato legible
     */
    private function traducirEstatus($estatus)
    {
        $mapa = [
            'no_solicitado' => 'No solicitado',
            'pendiente' => 'Pendiente',
            'en_progreso' => 'En progreso',
            'pendiente_revision' => 'Pendiente de revisión',
            'liberado' => 'Liberado',
        ];
        
        return $mapa[$estatus] ?? ucfirst($estatus);
    }
}