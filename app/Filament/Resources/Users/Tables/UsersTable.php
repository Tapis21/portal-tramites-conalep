<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('matricula')->label('Matrícula')->searchable(),
                TextColumn::make('name')->label('Nombre')->searchable(),
                TextColumn::make('apellidos')->label('Apellidos'),
                TextColumn::make('email')->label('Email'),
                TextColumn::make('role')->label('Rol')->badge(),
            ])
            ->paginated(false);
    }
}