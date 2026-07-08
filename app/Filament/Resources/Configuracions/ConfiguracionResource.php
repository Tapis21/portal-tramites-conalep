<?php

namespace App\Filament\Resources\Configuracions;

use App\Filament\Resources\Configuracions\Pages\ImportarDatos;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;

class ConfiguracionResource extends Resource
{
    protected static ?string $model = \App\Models\User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Configuración';

    protected static ?string $pluralModelLabel = 'Configuración';

    protected static ?int $navigationSort = 5;

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ImportarDatos::route('/'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}