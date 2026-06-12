<?php

namespace App\Filament\Resources\Empresas\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmpresaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Empresa')
                    ->searchable(),
                TextColumn::make('direccion')
                    ->label('Dirección'),
                TextColumn::make('telefono')
                    ->label('Teléfono'),
                TextColumn::make('contacto')
                    ->label('Contacto'),
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