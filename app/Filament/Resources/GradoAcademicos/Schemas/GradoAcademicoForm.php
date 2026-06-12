<?php

namespace App\Filament\Resources\GradoAcademicos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GradoAcademicoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->label('Grado académico')
                    ->required()
                    ->maxLength(255),
                TextInput::make('abreviatura')
                    ->label('Abreviatura')
                    ->maxLength(10),
                TextInput::make('activo')
                    ->label('Activo')
                    ->numeric()
                    ->default(1),
            ]);
    }
}