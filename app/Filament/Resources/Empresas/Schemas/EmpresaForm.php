<?php

namespace App\Filament\Resources\Empresas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmpresaForm
{
    public static function configure(Schema $schema, $record = null): Schema
    {
        return $schema
            ->components([
                Section::make('Datos Generales')
                    ->icon('heroicon-o-building-office')
                    ->schema([
                        TextInput::make('nombre')
                            ->label('Nombre de la Empresa')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Grupo Xcaret, Ayuntamiento de Cancún, CONALEP Cancún II'),

                        TextInput::make('direccion')
                            ->label('Dirección')
                            ->maxLength(255)
                            ->placeholder('Ej: Av. Tulum Mz 1 Lt 1, Cancún, Q.Roo')
                            ->helperText('Dirección completa de la empresa'),

                        TextInput::make('telefono')
                            ->label('Teléfono')
                            ->maxLength(255)
                            ->placeholder('Ej: 998-123-4567')
                            ->helperText('Teléfono de contacto de la empresa'),

                        TextInput::make('contacto')
                            ->label('Persona de Contacto')
                            ->maxLength(255)
                            ->placeholder('Ej: Juan Pérez Martínez')
                            ->helperText('Nombre de la persona de contacto en la empresa'),
                    ])
                    ->columns(2),

                Section::make('Convenios y Servicios')
                    ->schema([
                        Toggle::make('activo')
                            ->label('Empresa Activa')
                            ->default(true)
                            ->helperText('Solo las empresas activas aparecerán en las opciones para los estudiantes')
                            ->onIcon('heroicon-o-check-circle')
                            ->offIcon('heroicon-o-x-circle')
                            ->onColor('success')
                            ->offColor('danger'),

                        Toggle::make('servicio_social')
                            ->label('🟢 Servicio Social')
                            ->default(false)
                            ->helperText('¿La empresa acepta estudiantes para Servicio Social?'),

                        Toggle::make('practicas')
                            ->label('🟠 Prácticas Profesionales')
                            ->default(false)
                            ->helperText('¿La empresa acepta estudiantes para Prácticas Profesionales?'),

                        Toggle::make('programa_dual')
                            ->label('🟣 Programa Dual')
                            ->default(false)
                            ->helperText('¿La empresa participa en el Programa Dual?'),

                        DatePicker::make('fecha_termino_convenio')
                            ->label('Fecha de Término del Convenio')
                            ->native(false)
                            ->helperText('Fecha en que termina el convenio con la empresa (dejar vacío para indefinido)')
                            ->placeholder('Selecciona una fecha...'),
                    ])
                    ->columns(2),
            ]);
    }
}