<?php

namespace App\Filament\Widgets;

use App\Models\Practica;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ProximasFinalizacionesPP extends BaseWidget
{
    protected ?string $pollingInterval = '30s';
    
    protected int | string | array $columnSpan = 'full';
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Practica::query()
                    ->where('estatus', 'en_progreso')
                    ->whereNotNull('fecha_inicio')
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
                Tables\Columns\TextColumn::make('fecha_limite_final')
                    ->label('Finaliza')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => self::getDaysColor($record)),
                Tables\Columns\TextColumn::make('dias_restantes')
                    ->label('Días restantes')
                    ->state(fn ($record) => Carbon::now()->diffInDays($record->fecha_limite_final))
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state <= 7 => 'danger',
                        $state <= 15 => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn ($state) => "{$state} días"),
            ])
            ->emptyStateHeading('¡No hay finalizaciones próximas de Prácticas!')
            ->emptyStateDescription('Todas las solicitudes tienen fecha de finalización lejana.')
            ->emptyStateIcon('heroicon-o-calendar')
            ->defaultSort('fecha_limite_final', 'asc');
    }
    
    protected function getDaysColor($record): string
    {
        $dias = Carbon::now()->diffInDays($record->fecha_limite_final);
        
        if ($dias <= 7) {
            return 'danger';
        }
        
        if ($dias <= 15) {
            return 'warning';
        }
        
        return 'success';
    }
}