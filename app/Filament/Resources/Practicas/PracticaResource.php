<?php

namespace App\Filament\Resources\Practicas;

use App\Filament\Resources\Practicas\Pages\CreatePractica;
use App\Filament\Resources\Practicas\Pages\EditPractica;
use App\Filament\Resources\Practicas\Pages\ListPracticas;
use App\Filament\Resources\Practicas\Pages\ViewPractica;
use App\Filament\Resources\Practicas\Schemas\PracticaForm;
use App\Filament\Resources\Practicas\Tables\PracticasTable;
use App\Models\Practica;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PracticaResource extends Resource
{
    protected static ?string $model = Practica::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Prácticas Profesionales';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return PracticaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PracticasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPracticas::route('/'),
            'create' => CreatePractica::route('/create'),
            'edit' => EditPractica::route('/{record}/edit'),
            'view' => ViewPractica::route('/{record}'),
        ];
    }
}