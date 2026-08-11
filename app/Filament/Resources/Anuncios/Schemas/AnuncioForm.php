<?php

namespace App\Filament\Resources\Anuncios\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AnuncioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('contenido')
                    ->label('Contenido del anuncio')
                    ->required()
                    ->maxLength(1000)
                    ->rows(5)
                    ->placeholder('Escribe el anuncio que quieres publicar...')
                    ->helperText('Máximo 1000 caracteres')
                    ->autofocus(),
            ])
            ->columns(1);
    }
}