<?php

namespace App\Filament\Resources\GradoAcademicos;

use App\Filament\Resources\GradoAcademicos\Pages\CreateGradoAcademico;
use App\Filament\Resources\GradoAcademicos\Pages\EditGradoAcademico;
use App\Filament\Resources\GradoAcademicos\Pages\ListGradoAcademicos;
use App\Filament\Resources\GradoAcademicos\Schemas\GradoAcademicoForm;
use App\Filament\Resources\GradoAcademicos\Tables\GradoAcademicoTable;
use App\Models\GradoAcademico;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GradoAcademicoResource extends Resource
{
    protected static ?string $model = GradoAcademico::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Grados Académicos';

    protected static ?string $pluralModelLabel = 'Grados Académicos';

    public static function form(Schema $schema): Schema
    {
        return GradoAcademicoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GradoAcademicoTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGradoAcademicos::route('/'),
            'create' => CreateGradoAcademico::route('/create'),
            'edit' => EditGradoAcademico::route('/{record}/edit'),
        ];
    }
}