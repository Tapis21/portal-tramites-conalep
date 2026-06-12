<?php

namespace App\Filament\Resources\ServicioSocials\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class ServicioSocialForm
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
                DatePicker::make('fecha_limite_primer_informe')
                    ->label('Límite primer informe'),
                DatePicker::make('fecha_limite_segundo_informe')
                    ->label('Límite segundo informe'),
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