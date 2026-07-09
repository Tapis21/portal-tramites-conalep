<?php

namespace App\Filament\Resources\Periodos\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;

class PeriodoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('año_inicio')
                    ->label('Año de inicio')
                    ->options(function () {
                        $años = [];
                        for ($i = 2020; $i <= 2035; $i++) {
                            $años[$i] = $i;
                        }
                        return $años;
                    })
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set) {
                        $añoFin = $state ? $state + 3 : null;
                        $set('año_fin', $añoFin);
                        if ($state && $añoFin) {
                            $set('nombre', $state . ' - ' . $añoFin);
                        }
                    }),

                Select::make('año_fin')
                    ->label('Año de finalización')
                    ->options(function () {
                        $años = [];
                        for ($i = 2020; $i <= 2035; $i++) {
                            $años[$i] = $i;
                        }
                        return $años;
                    })
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, $get, $set) {
                        $añoInicio = $get('año_inicio');
                        if ($state && $añoInicio) {
                            $set('nombre', $añoInicio . ' - ' . $state);
                        }
                    }),

                TextInput::make('nombre')
                    ->label('Nombre del periodo')
                    ->required()
                    ->disabled()
                    ->dehydrated(true)
                    ->helperText('Se genera automáticamente'),

                Toggle::make('activo')
                    ->label('Activo')
                    ->default(true)
                    ->helperText('Marcar como activo si es el periodo actual'),
            ]);
    }
}