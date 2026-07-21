<?php

namespace App\Filament\Widgets;

use App\Models\Empresa;
use App\Models\ServicioSocial;
use App\Models\Practica;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EstadisticasGenerales extends BaseWidget
{
    protected ?string $pollingInterval = '10s';
    
    protected function getStats(): array
    {
        // Total de solicitudes (Servicio Social + Prácticas)
        $totalSS = ServicioSocial::count();
        $totalPP = Practica::count();
        $total = $totalSS + $totalPP;
        
        // Pendientes
        $pendientesSS = ServicioSocial::where('estatus', 'pendiente')->count();
        $pendientesPP = Practica::where('estatus', 'pendiente')->count();
        $pendientes = $pendientesSS + $pendientesPP;
        
        // Activas (en_progreso)
        $activasSS = ServicioSocial::where('estatus', 'en_progreso')->count();
        $activasPP = Practica::where('estatus', 'en_progreso')->count();
        $activas = $activasSS + $activasPP;
        
        return [
            Stat::make('Total Solicitudes', $total)
                ->description('Servicio Social: ' . $totalSS . ' | Prácticas: ' . $totalPP)
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 8, 12]),

            Stat::make('Pendientes', $pendientes)
                ->description('Por revisar')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([3, 5, 2, 4, 6, 3, 5]),

            Stat::make('Activas', $activas)
                ->description('En curso')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([5, 8, 6, 10, 12, 9, 14]),

            Stat::make('Empresas', Empresa::count())
                ->description('Registradas')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('info')
                ->chart([4, 6, 5, 7, 8, 9, 10]),
        ];
    }
}