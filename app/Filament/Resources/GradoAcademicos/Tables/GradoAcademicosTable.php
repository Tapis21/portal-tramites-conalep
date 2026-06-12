<?php

namespace App\Filament\Resources\GradoAcademicos\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GradoAcademicoTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Grado académico')
                    ->searchable(),
                TextColumn::make('abreviatura')
                    ->label('Abreviatura'),
                TextColumn::make('activo')
                    ->label('Activo')
                    ->badge()
                    ->color(fn (string $state): string => $state == '1' ? 'success' : 'danger')
                    ->formatStateUsing(fn ($state) => $state == '1' ? 'Sí' : 'No'),
            ])
            ->paginated(false)
            ->defaultSort('nombre');
    }
}