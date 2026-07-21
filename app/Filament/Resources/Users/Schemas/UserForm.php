<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Hash;
use App\Models\Periodo;
use App\Models\EstudiantePeriodo;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),

                TextInput::make('apellidos')
                    ->label('Apellidos')
                    ->required(),

                TextInput::make('matricula')
                    ->label('Matrícula')
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),

                Select::make('role')
                    ->label('Rol')
                    ->options([
                        'estudiante' => 'Estudiante',
                        'admin' => 'Administrador',
                    ])
                    ->required(),

                TextInput::make('carrera')
                    ->label('Carrera'),

                Select::make('semestre')
                    ->label('Semestre')
                    ->options([
                        '1' => '1º',
                        '2' => '2º',
                        '3' => '3º',
                        '4' => '4º',
                        '5' => '5º',
                        '6' => '6º',
                    ]),

                // ✅ NUEVO CAMPO: PERIODO
                Select::make('periodo_id')
                    ->label('Periodo')
                    ->options(
                        Periodo::where('activo', true)
                            ->orderBy('año_inicio', 'desc')
                            ->pluck('nombre', 'id')
                            ->toArray()
                    )
                    ->placeholder('Selecciona un periodo')
                    ->helperText('Periodo actual del estudiante')
                    ->default(function ($record) {
                        if ($record) {
                            $periodo = $record->periodoActual();
                            return $periodo ? $periodo->id : null;
                        }
                        return null;
                    })
                    ->afterStateHydrated(function ($state, $set, $record) {
                        if ($record) {
                            $periodo = $record->periodoActual();
                            if ($periodo) {
                                $set('periodo_id', $periodo->id);
                            }
                        }
                    }),

                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => $state ? Hash::make($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                    ->helperText('Dejar en blanco para mantener la contraseña actual'),
            ]);
    }
}