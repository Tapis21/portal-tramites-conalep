<?php

namespace App\Filament\Resources\Practicas\Pages;

use App\Filament\Resources\Practicas\PracticaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPracticas extends ListRecords
{
    protected static string $resource = PracticaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
