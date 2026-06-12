<?php

namespace App\Filament\Resources\Practicas\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PracticaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Estudiante')
                    ->searchable(),
                TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->date('d/m/Y'),
                TextColumn::make('fecha_limite_final')
                    ->label('Finaliza')
                    ->date('d/m/Y'),
                TextColumn::make('estatus')
                    ->label('Estatus')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'liberado' => 'success',
                        'pendiente_revision' => 'warning',
                        'en_progreso' => 'info',
                        'pendiente' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->paginated(false)
            ->defaultSort('created_at', 'desc');
    }
}