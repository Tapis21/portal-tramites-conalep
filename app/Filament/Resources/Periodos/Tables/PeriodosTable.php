<?php

namespace App\Filament\Resources\Periodos\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;

class PeriodosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // ✅ HACER QUE TODA LA FILA SEA CLICKEABLE
            ->recordUrl(fn ($record) => route('filament.admin.resources.periodos.usuarios', $record))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('año_inicio')
                    ->label('Año inicio')
                    ->sortable(),

                TextColumn::make('año_fin')
                    ->label('Año fin')
                    ->sortable(),

                TextColumn::make('nombre')
                    ->label('Periodo')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->actions([
                // ✅ BOTÓN "EDITAR" (independiente del click en fila)
                Action::make('editar')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->url(fn ($record) => route('filament.admin.resources.periodos.edit', $record)),
            ])
            ->defaultSort('año_inicio', 'desc')
            ->paginated([25, 50, 100]);
    }
}