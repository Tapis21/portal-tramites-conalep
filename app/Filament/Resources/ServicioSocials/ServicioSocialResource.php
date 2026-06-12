<?php

namespace App\Filament\Resources\ServicioSocials;

use App\Filament\Resources\ServicioSocials\Pages\CreateServicioSocial;
use App\Filament\Resources\ServicioSocials\Pages\EditServicioSocial;
use App\Filament\Resources\ServicioSocials\Pages\ListServicioSocials;
use App\Filament\Resources\ServicioSocials\Schemas\ServicioSocialForm;
use App\Filament\Resources\ServicioSocials\Tables\ServicioSocialTable;
use App\Models\ServicioSocial;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ServicioSocialResource extends Resource
{
    protected static ?string $model = ServicioSocial::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Servicio Social';

    public static function form(Schema $schema): Schema
    {
        return ServicioSocialForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServicioSocialTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServicioSocials::route('/'),
            'create' => CreateServicioSocial::route('/create'),
            'edit' => EditServicioSocial::route('/{record}/edit'),
        ];
    }
}