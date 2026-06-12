<?php

namespace App\Filament\Resources\Practicas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class PracticaForm
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
                DatePicker::make('fecha_inicio')
                    ->label('Fecha de inicio'),
                DatePicker::make('fecha_limite_parcial')
                    ->label('Límite primer informe (180h)'),
                DatePicker::make('fecha_limite_final')
                    ->label('Límite segundo informe (360h)'),
                Select::make('estatus')
                    ->label('Estatus')
                    ->options([
                        'no_solicitado' => 'No solicitado',
                        'pendiente' => 'Pendiente',
                        'en_progreso' => 'En progreso',
                        'pendiente_revision' => 'Pendiente de revisión',
                        'liberado' => 'Liberado',
                    ])
                    ->required(),
            ]);
    }
}