<?php

namespace App\Filament\Resources\Configuracions\Pages;

use App\Filament\Resources\Configuracions\ConfiguracionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListConfiguracions extends ListRecords
{
    protected static string $resource = ConfiguracionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
