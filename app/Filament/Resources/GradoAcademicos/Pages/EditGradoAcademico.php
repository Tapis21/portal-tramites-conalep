<?php

namespace App\Filament\Resources\GradoAcademicos\Pages;

use App\Filament\Resources\GradoAcademicos\GradoAcademicoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGradoAcademico extends EditRecord
{
    protected static string $resource = GradoAcademicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
