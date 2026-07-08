<?php

namespace App\Filament\Resources\Configuracions;

use App\Filament\Resources\Configuracions\Pages\ImportarDatos;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;

class ConfiguracionResource extends Resource
{
    protected static ?string $model = \App\Models\User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCloudArrowUp;

    protected static ?string $navigationLabel = 'Importación Masiva';

    protected static ?string $pluralModelLabel = 'Importación Masiva';

    protected static ?int $navigationSort = 2;
    public static function getNavigationGroup(): ?string
    {
        return '⚙️ Configuración';
    }

    // ✅ GRUPO CORRECTO
    // public static function getNavigationGroup(): ?string
    // {
    //     return 'Configuración';
    // }

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