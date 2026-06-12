<?php

namespace App\Filament\Resources\Documentos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DocumentoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Estudiante')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),
                Select::make('tipo_documento_id')
                    ->label('Tipo de documento')
                    ->relationship('tipoDocumento', 'nombre')
                    ->searchable()
                    ->required(),
                TextInput::make('archivo_pdf')
                    ->label('Archivo PDF')
                    ->disabled()
                    ->helperText('El archivo se sube desde el módulo del estudiante.'),
                Select::make('estatus')
                    ->label('Estado de validación')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'validado' => 'Validado',
                        'rechazado' => 'Rechazado',
                    ])
                    ->required(),
                Textarea::make('comentario_admin')
                    ->label('Comentario del administrador')
                    ->rows(3)
                    ->helperText('Este comentario será visible para el estudiante.'),
            ]);
    }
}