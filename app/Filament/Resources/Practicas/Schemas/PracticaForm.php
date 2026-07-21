<?php

namespace App\Filament\Resources\Practicas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PracticaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // DATOS DEL ESTUDIANTE
                Select::make('user_id')
                    ->label('Estudiante')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                // DATOS DE LA EMPRESA
                Select::make('empresa_id')
                    ->label('Empresa')
                    ->relationship('empresa', 'nombre')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('horario_id')
                    ->label('Horario')
                    ->relationship('horario', 'hora_inicio')
                    ->searchable()
                    ->preload()
                    ->required(),

                // FECHAS
                DatePicker::make('fecha_inicio')
                    ->label('Fecha de inicio')
                    ->required()
                    ->native(false),

                DatePicker::make('fecha_limite_parcial')
                    ->label('Límite informe parcial')
                    ->required()
                    ->native(false),

                DatePicker::make('fecha_limite_final')
                    ->label('Límite informe final')
                    ->required()
                    ->native(false),

                // HORAS
                TextInput::make('horas_requeridas')
                    ->label('Horas requeridas')
                    ->default(360)
                    ->numeric()
                    ->required(),

                TextInput::make('horas_completadas')
                    ->label('Horas completadas')
                    ->default(0)
                    ->numeric(),

                // GRADOS ACADÉMICOS
                Select::make('grado_academico_id')
                    ->label('Grado (Carta)')
                    ->relationship('gradoAcademico', 'nombre')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('grado_academico_jefe_id')
                    ->label('Grado (Jefe)')
                    ->relationship('gradoAcademicoJefe', 'nombre')
                    ->searchable()
                    ->preload()
                    ->required(),

                // CONTACTOS
                TextInput::make('nombre_persona_carta')
                    ->label('Nombre de la persona')
                    ->required()
                    ->maxLength(255),

                TextInput::make('cargo_persona_carta')
                    ->label('Cargo de la persona')
                    ->required()
                    ->maxLength(255),

                TextInput::make('nombre_jefe_inmediato')
                    ->label('Nombre del jefe inmediato')
                    ->required()
                    ->maxLength(255),

                TextInput::make('cargo_jefe_inmediato')
                    ->label('Cargo del jefe inmediato')
                    ->required()
                    ->maxLength(255),

                // ÁREA Y APOYO
                TextInput::make('area_asignada')
                    ->label('Área asignada')
                    ->required()
                    ->maxLength(255),

                TextInput::make('apoyo_estudiante')
                    ->label('Apoyo al estudiante')
                    ->placeholder('Ej: Económico, equipo de cómputo')
                    ->maxLength(255),

                // ESTATUS
                Select::make('estatus')
                    ->label('Estatus')
                    ->options([
                        'no_solicitado' => 'No solicitado',
                        'pendiente' => 'Pendiente',
                        'en_progreso' => 'En progreso',
                        'pendiente_revision' => 'Pendiente de revisión',
                        'liberado' => 'Liberado',
                    ])
                    ->required()
                    ->default('pendiente'),
            ]);
    }
}