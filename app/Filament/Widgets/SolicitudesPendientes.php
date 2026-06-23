<?php

namespace App\Filament\Widgets;

use App\Models\ServicioSocial;
use App\Models\Practica;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class SolicitudesPendientes extends BaseWidget
{
    protected ?string $pollingInterval = '15s';
    
    protected int | string | array $columnSpan = 'full';
    
    public function table(Table $table): Table
    {
        // Unir las dos tablas con un union
        $servicioSocial = ServicioSocial::where('estatus', 'pendiente')
            ->select(
                'id',
                'user_id',
                'empresa_id',
                'fecha_inicio',
                'estatus',
                \DB::raw("'Servicio Social' as tipo")
            );
        
        $practicas = Practica::where('estatus', 'pendiente')
            ->select(
                'id',
                'user_id',
                'empresa_id',
                'fecha_inicio',
                'estatus',
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
                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('estatus')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente' => 'warning',
                        'en_progreso' => 'info',
                        'liberado' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pendiente' => '⏳ Pendiente',
                        'en_progreso' => '🔄 En progreso',
                        'liberado' => '✅ Liberado',
                        default => $state,
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('ver')
                    ->label('Ver')
                    ->color('primary')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => $record->tipo === 'Servicio Social' 
                        ? route('filament.admin.resources.servicio-socials.edit', $record) 
                        : route('filament.admin.resources.practicas.edit', $record)
                    ),
            ])
            ->emptyStateHeading('¡No hay solicitudes pendientes!')
            ->emptyStateDescription('Todas las solicitudes han sido revisadas.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}