<?php

namespace App\Filament\Resources\Practicas\Tables;

use Carbon\Carbon;
use Filament\Actions\Action;  // 👈 ¡¡¡IMPORTANTE!!! Filament\Actions\Action
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PracticasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 👇 SOLO CAMBIO ESTA COLUMNA
                TextColumn::make('user.name')
                    ->label('Estudiante')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => route('filament.admin.resources.practicas.view', $record))
                    ->openUrlInNewTab(false)
                    ->tooltip('Ver detalles de la solicitud'),

                // 👇 EL RESTO DE COLUMNAS IGUAL
                TextColumn::make('user.matricula')
                    ->label('Matrícula')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('empresa.nombre')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('fecha_limite_final')
                    ->label('Finaliza')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => self::getDaysColor($record)),

                TextColumn::make('dias_restantes')
                    ->label('Días')
                    ->state(fn ($record) => Carbon::now()->diffInDays($record->fecha_limite_final))
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state <= 7 => 'danger',
                        $state <= 15 => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn ($state) => $state . ' días'),

                TextColumn::make('horas_completadas')
                    ->label('Horas')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state, $record) => $state . '/' . $record->horas_requeridas . ' hrs'),

                TextColumn::make('estatus')
                    ->label('Estatus')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'liberado' => 'success',
                        'pendiente_revision' => 'warning',
                        'en_progreso' => 'info',
                        'pendiente' => 'warning',
                        'no_solicitado' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'liberado' => '✅ Liberado',
                        'pendiente_revision' => '⚠️ En Revisión',
                        'en_progreso' => '🔄 En Progreso',
                        'pendiente' => '⏳ Pendiente',
                        'no_solicitado' => '⬜ No Solicitado',
                        default => $state,
                    }),
            ])
            // 👇 LAS ACCIONES QUEDAN IGUAL POR AHORA
            ->actions([
                Action::make('aprobar')
                    ->label('Aprobar')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->button()
                    ->hidden(fn ($record) => $record->estatus !== 'pendiente')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['estatus' => 'en_progreso']);
                    }),

                Action::make('rechazar')
                    ->label('Rechazar')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->button()
                    ->hidden(fn ($record) => $record->estatus !== 'pendiente')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['estatus' => 'no_solicitado']);
                    }),

                Action::make('liberar')
                    ->label('Liberar')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->button()
                    ->hidden(fn ($record) => $record->estatus !== 'en_progreso')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['estatus' => 'liberado']);
                    }),
            ])
            ->paginated(false)
            ->defaultSort('created_at', 'desc');
    }
    
    protected static function getDaysColor($record): string
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