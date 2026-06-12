<?php

namespace App\Filament\Resources\GradoAcademicos\Pages;

use App\Filament\Resources\GradoAcademicos\GradoAcademicoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGradoAcademicos extends ListRecords
{
    protected static string $resource = GradoAcademicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
