<?php

namespace App\Filament\Widgets;

use App\Models\ServicioSocial;
use App\Models\Practica;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ProximasFinalizaciones extends BaseWidget
{
    protected ?string $pollingInterval = '30s';
    
    protected int | string | array $columnSpan = 'full';
    
    public function table(Table $table): Table
    {
        // Unir las dos tablas
        $servicioSocial = ServicioSocial::where('estatus', 'en_progreso')
            ->whereNotNull('fecha_inicio')
            ->select(
                'id',
                'user_id',
                'empresa_id',
                'fecha_inicio',
                \DB::raw("DATE_ADD(fecha_inicio, INTERVAL 6 MONTH) as fecha_fin"),
                \DB::raw("'Servicio Social' as tipo")
            );
        
        $practicas = Practica::where('estatus', 'en_progreso')
            ->whereNotNull('fecha_inicio')
            ->select(
                'id',
                'user_id',
                'empresa_id',
                'fecha_inicio',
                \DB::raw("DATE_ADD(fecha_inicio, INTERVAL 4 MONTH) as fecha_fin"),
                \DB::raw("'Prácticas Profesionales' as tipo")
            );
        
        $union = $servicioSocial->union($practicas);
        
        return $table
            ->query($union)
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Estudiante')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Servicio Social' ? 'success' : 'info'),
                Tables\Columns\TextColumn::make('empresa.nombre')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_fin')
                    ->label('Finaliza')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => self::getDaysColor($record)),
                Tables\Columns\TextColumn::make('dias_restantes')
                    ->label('Días restantes')
                    ->state(fn ($record) => Carbon::now()->diffInDays(Carbon::parse($record->fecha_fin)))
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state <= 7 => 'danger',
                        $state <= 15 => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn ($state) => "{$state} días"),
            ])
            ->emptyStateHeading('¡No hay finalizaciones próximas!')
            ->emptyStateDescription('Todas las solicitudes tienen fecha de finalización lejana.')
            ->emptyStateIcon('heroicon-o-calendar');
    }
    
    protected function getDaysColor($record): string
    {
        $dias = Carbon::now()->diffInDays(Carbon::parse($record->fecha_fin));
        
        if ($dias <= 7) {
            return 'danger';
        }
        
        if ($dias <= 15) {
            return 'warning';
        }
        
        return 'success';
    }
}