<?php

namespace App\Filament\Widgets;

use App\Models\Practica;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Actions\Action;

class SolicitudesPendientesPP extends BaseWidget
{
    protected ?string $pollingInterval = '15s';
    
    protected int | string | array $columnSpan = 'full';
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Practica::query()
                    ->where('estatus', 'pendiente')
                    ->with(['user', 'empresa'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Estudiante')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('empresa.nombre')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('horas_requeridas')
                    ->label('Horas req.')
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
                Action::make('ver')
                    ->label('Ver')
                    ->color('primary')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route('filament.admin.resources.practicas.edit', $record)),
            ])
            ->emptyStateHeading('¡No hay solicitudes de Prácticas pendientes!')
            ->emptyStateDescription('Todas las solicitudes han sido revisadas.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->defaultSort('created_at', 'desc');
    }
}