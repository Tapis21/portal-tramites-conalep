<?php

namespace App\Http\Controllers;

use App\Models\Anuncio;
use App\Models\ServicioSocial;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Servicio Social
        $servicioSocial = $user->servicioSocial;
        $progresoSS = 0;
        $estatusSS = 'No solicitado';
        
        if ($servicioSocial && $servicioSocial->fecha_inicio) {
            $estatusSS = $servicioSocial->estatus;
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
        
        // Prácticas Profesionales
        $practica = $user->practica;
        $progresoPP = 0;
        $estatusPP = 'No solicitado';
        
        if ($practica && $practica->fecha_inicio) {
            $estatusPP = $practica->estatus;
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
        
        $anuncios = Anuncio::orderBy('created_at', 'desc')->get();
        
        return view('dashboard', compact('progresoSS', 'estatusSS', 'progresoPP', 'estatusPP', 'anuncios'));
    }
}