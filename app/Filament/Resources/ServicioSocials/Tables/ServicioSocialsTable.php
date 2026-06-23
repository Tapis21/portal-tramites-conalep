<?php

namespace App\Filament\Resources\ServicioSocials\Tables;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServicioSocialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Estudiante')
                    ->searchable()
                    ->sortable(),

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

                TextColumn::make('fecha_limite_segundo_informe')
                    ->label('Finaliza')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => self::getDaysColor($record)),

                TextColumn::make('dias_restantes')
                    ->label('Días')
                    ->state(fn ($record) => Carbon::now()->diffInDays($record->fecha_limite_segundo_informe))
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state <= 7 => 'danger',
                        $state <= 15 => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn ($state) => $state . ' días'),

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
            ->actions([
                Action::make('aprobar')
                    ->label('Aprobar')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->button()
                    ->hidden(fn ($record) => $record->estatus !== 'pendiente')
                    ->action(function ($record) {
                        $record->estatus = 'en_progreso';
                        $record->saveQuietly();
                    }),

                Action::make('rechazar')
                    ->label('Rechazar')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->button()
                    ->hidden(fn ($record) => $record->estatus !== 'pendiente')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->estatus = 'no_solicitado';
                        $record->saveQuietly();
                    }),

                Action::make('liberar')
                    ->label('Liberar')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->button()
                    ->hidden(fn ($record) => $record->estatus !== 'en_progreso')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->estatus = 'liberado';
                        $record->saveQuietly();
                    }),
            ])
            ->paginated(false)
            ->defaultSort('created_at', 'desc');
    }

    protected static function getDaysColor($record): string
    {
        $dias = Carbon::now()->diffInDays($record->fecha_limite_segundo_informe);

        if ($dias <= 7) {
            return 'danger';
        }

        if ($dias <= 15) {
            return 'warning';
        }

        return 'success';
    }
}