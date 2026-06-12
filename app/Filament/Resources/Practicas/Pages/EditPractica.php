<?php

namespace App\Filament\Resources\Practicas\Pages;

use App\Filament\Resources\Practicas\PracticaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPractica extends EditRecord
{
    protected static string $resource = PracticaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
