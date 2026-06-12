<?php

namespace App\Filament\Resources\Empresas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EmpresaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->label('Nombre de la empresa')
                    ->required()
                    ->maxLength(255),
                TextInput::make('direccion')
                    ->label('Dirección')
                    ->maxLength(255),
                TextInput::make('telefono')
                    ->label('Teléfono')
                    ->maxLength(20),
                TextInput::make('contacto')
                    ->label('Persona de contacto')
                    ->maxLength(255),
                TextInput::make('activo')
                    ->label('Activo')
                    ->numeric()
                    ->default(1),
            ]);
    }
}