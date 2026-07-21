<?php

namespace App\Filament\Resources\Configuracions\Pages;

use App\Filament\Resources\Configuracions\ConfiguracionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditConfiguracion extends EditRecord
{
    protected static string $resource = ConfiguracionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
